<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountHead;
use App\Models\AccountHistory;
use App\Models\JournalEntry;
use Illuminate\Http\Request;

class AccountsHeadController extends Controller
{
    public function index()
    {
        $heads = AccountHead::all();

        // Exclude system control accounts (AR, AP, SALES, PURCHASE)
        $excludedCodes = ['AR', 'AP', 'SALES', 'PURCHASE'];
        $excludedTitles = ['Accounts Receivable', 'Accounts Payable', 'Sales Revenue', 'Purchase Expense'];

        $accounts = Account::with(['head', 'histories.user'])
            ->whereNotIn('account_code', $excludedCodes)
            ->whereNotIn('title', $excludedTitles)
            ->orderBy('id', 'asc')
            ->get();

        return view('admin_panel.chart_of_accounts', compact('heads', 'accounts'));
    }

    public function storeHead(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:account_heads,name',
        ]);

        AccountHead::create([
            'name' => $request->name,
        ]);

        return back()->with('success', 'Account Head added successfully!');
    }

    public function storeAccount(Request $request)
    {
        $request->validate([
            'head_id' => 'required|exists:account_heads,id',
            'title' => 'required',
            'opening_balance' => 'required|numeric',
            'type' => 'required',
        ]);

        $initialBalance = (float) $request->opening_balance;

        $account = Account::create([
            'head_id' => $request->head_id,
            'title' => $request->title,
            'opening_balance' => $initialBalance,
            'current_balance' => $initialBalance, // Sync current balance initially
            'type' => $request->type,
            'status' => $request->has('status') ? 1 : 0,
        ]);

        $account->account_code = 'ACC-'.str_pad($account->id, 4, '0', STR_PAD_LEFT);
        $account->save();

        // Log initial balance history
        AccountHistory::create([
            'account_id' => $account->id,
            'old_balance' => 0,
            'new_balance' => $initialBalance,
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name ?? 'User',
            'note' => 'Account created with initial balance: Rs ' . number_format($initialBalance, 2),
        ]);

        return back()->with('success', 'Account added successfully!');
    }

    public function updateAccount(Request $request, $id)
    {
        $request->validate([
            'head_id' => 'required|exists:account_heads,id',
            'title' => 'required',
            'opening_balance' => 'required|numeric',
            'current_balance' => 'required|numeric',
            'type' => 'required',
        ]);

        $account = Account::findOrFail($id);

        $oldOpening = (float) $account->opening_balance;
        $newOpening = (float) $request->opening_balance;

        $oldCurrent = (float) $account->current_balance;
        $newCurrent = (float) $request->current_balance;

        $account->title = $request->title;
        $account->head_id = $request->head_id;
        $account->type = $request->type;
        $account->opening_balance = $newOpening;
        $account->current_balance = $newCurrent;
        $account->status = $request->has('status') ? 1 : 0;

        $account->save();

        // Create detailed audit log entry if opening balance, current balance changed or note provided
        if ($oldOpening != $newOpening || $oldCurrent != $newCurrent || $request->filled('note')) {
            $user = auth()->user();

            $noteParts = [];
            if ($oldCurrent != $newCurrent) {
                $diff = $newCurrent - $oldCurrent;
                $diffStr = ($diff >= 0 ? '+' : '') . number_format($diff, 2);
                $noteParts[] = "Current Balance: Rs " . number_format($oldCurrent, 2) . " -> Rs " . number_format($newCurrent, 2) . " (Diff: " . $diffStr . ")";
            }
            if ($oldOpening != $newOpening) {
                $noteParts[] = "Opening: Rs " . number_format($oldOpening, 2) . " -> Rs " . number_format($newOpening, 2);
            }
            if ($request->filled('note')) {
                $noteParts[] = "Reason: " . $request->note;
            }

            AccountHistory::create([
                'account_id' => $account->id,
                'old_balance' => $oldCurrent,
                'new_balance' => $newCurrent,
                'user_id' => $user ? $user->id : null,
                'user_name' => $user ? $user->name : 'User',
                'note' => implode(' | ', $noteParts),
            ]);
        }

        return back()->with('success', 'Account "' . $account->title . '" ledger balance updated successfully!');
    }

    public function showLedger($id, Request $request)
    {
        if ($id === 'company-discount' || $id === 'COMP-DISC') {
            $account = Account::firstOrCreate(
                ['account_code' => 'COMP-DISC'],
                [
                    'title' => 'Company Discount & Claims',
                    'type' => 'Credit',
                    'opening_balance' => 0,
                    'status' => 1
                ]
            );
        } else {
            $account = Account::find($id);
            if (!$account) {
                // If ID is not found, check if it's looking for Company Discount account
                $account = Account::where('account_code', 'COMP-DISC')
                    ->orWhere('title', 'like', '%Company Discount%')
                    ->first();
                if (!$account) {
                    $account = Account::firstOrCreate(
                        ['account_code' => 'COMP-DISC'],
                        [
                            'title' => 'Company Discount & Claims',
                            'type' => 'Credit',
                            'opening_balance' => 0,
                            'status' => 1
                        ]
                    );
                }
            }
        }

        $isDiscountAccount = (
            $account->account_code === 'COMP-DISC' ||
            stripos($account->title, 'Company Discount') !== false ||
            stripos($account->title, 'Discount') !== false ||
            stripos($account->title, 'Claims') !== false ||
            (int)$account->id === 4
        );

        // Auto-sync historical and existing purchase company discounts into this ledger
        if ($isDiscountAccount) {
            self::syncCompanyDiscountLedger($account);
        }

        $query = JournalEntry::where('account_id', $account->id)
            ->with(['party', 'source'])
            ->orderBy('entry_date', 'asc')
            ->orderBy('id', 'asc');

        if ($request->filled('from_date')) {
            $query->whereDate('entry_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('entry_date', '<=', $request->to_date);
        }

        if ($request->filled('madd') && $request->madd !== 'all') {
            $query->where('description', 'like', "%[مَد: {$request->madd}]%");
        }

        $entries = $query->get();

        // Calculate Head-wise (Madd-wise) and Vendor-wise summaries
        $maddSummary = [];
        $vendorSummary = [];
        $totalDiscountReceived = 0;
        $totalAdjustedOrPaid = 0;

        foreach ($entries as $entry) {
            $cr = (float)($entry->credit ?? 0);
            $dr = (float)($entry->debit ?? 0);
            $totalDiscountReceived += $cr;
            $totalAdjustedOrPaid += $dr;

            $maddName = 'جنرل کمپنی ڈسکاؤنٹ';
            if (preg_match('/\[مَد:\s*([^\]]+)\]/u', $entry->description, $matches)) {
                $maddName = trim($matches[1]);
            } elseif (stripos($entry->description, 'Customer') !== false) {
                $maddName = 'کسٹمر شیئر';
            } elseif (stripos($entry->description, 'Owner') !== false || stripos($entry->description, 'Humara') !== false) {
                $maddName = 'ہمارا شیئر';
            }

            if (!isset($maddSummary[$maddName])) {
                $maddSummary[$maddName] = [
                    'name' => $maddName,
                    'amount' => 0,
                    'count' => 0
                ];
            }
            $maddSummary[$maddName]['amount'] += $cr;
            $maddSummary[$maddName]['count']++;

            $vName = $entry->party ? ($entry->party->name ?? $entry->party->title ?? 'N/A') : 'Other';
            if (!isset($vendorSummary[$vName])) {
                $vendorSummary[$vName] = [
                    'name' => $vName,
                    'amount' => 0,
                    'count' => 0
                ];
            }
            $vendorSummary[$vName]['amount'] += $cr;
            $vendorSummary[$vName]['count']++;
        }

        return view('admin_panel.accounts.ledger', compact(
            'account',
            'entries',
            'isDiscountAccount',
            'maddSummary',
            'vendorSummary',
            'totalDiscountReceived',
            'totalAdjustedOrPaid'
        ));
    }

    /**
     * Sync historical and existing purchase company discounts into the Company Discount Ledger
     */
    public static function syncCompanyDiscountLedger(Account $account): void
    {
        try {
            $purchases = \App\Models\Purchase::with(['vendor'])
                ->whereIn('status_purchase', ['approved', 'posted', 'Returned', 'Partial'])
                ->get();

            foreach ($purchases as $purchase) {
                $items = \DB::table('purchase_items')
                    ->where('purchase_id', $purchase->id)
                    ->get();

                $itemTotalDisc = (float)$items->sum('item_discount');
                $itemCustDisc = (float)$items->sum('customer_disc_amount');
                $itemOurDisc = (float)$items->sum('our_disc_amount');

                $invTotalDisc = (float)($purchase->company_disc_amount ?: $purchase->discount ?: 0);
                $invCustDisc = (float)($purchase->customer_disc_amount ?? 0);
                $invOurDisc = (float)($purchase->our_disc_amount ?? 0);

                // Collect custom heads (prefer invoice-level, or fallback to items)
                $customHeads = [];
                $invCustom = is_array($purchase->custom_disc_breakdown) ? $purchase->custom_disc_breakdown : (json_decode($purchase->custom_disc_breakdown ?? '[]', true) ?: []);
                if (!empty($invCustom) && is_array($invCustom)) {
                    foreach ($invCustom as $ch) {
                        $h = trim($ch['head'] ?? '');
                        $amt = (float)($ch['amount'] ?? 0);
                        if ($h !== '' && $amt > 0) {
                            $customHeads[$h] = ($customHeads[$h] ?? 0) + $amt;
                        }
                    }
                } else {
                    foreach ($items as $item) {
                        $itemCb = is_array($item->custom_disc_breakdown) ? $item->custom_disc_breakdown : (json_decode($item->custom_disc_breakdown ?? '[]', true) ?: []);
                        if (is_array($itemCb)) {
                            foreach ($itemCb as $ch) {
                                $h = trim($ch['head'] ?? '');
                                $amt = (float)($ch['amount'] ?? 0);
                                if ($h !== '' && $amt > 0) {
                                    $customHeads[$h] = ($customHeads[$h] ?? 0) + $amt;
                                }
                            }
                        }
                    }
                }

                $custAmt = $invCustDisc > 0 ? $invCustDisc : $itemCustDisc;
                $ourAmt = $invOurDisc > 0 ? $invOurDisc : $itemOurDisc;
                $customSum = array_sum($customHeads);

                $totalPurchDisc = max($invTotalDisc, $itemTotalDisc, ($custAmt + $ourAmt + $customSum));

                if ($totalPurchDisc <= 0) {
                    JournalEntry::where('account_id', $account->id)
                        ->where('source_type', \App\Models\Purchase::class)
                        ->where('source_id', $purchase->id)
                        ->delete();
                    continue;
                }

                // Prepare list of heads for this purchase (Excluding Our Discount as requested)
                $headsToRecord = [];

                if ($custAmt <= 0 && empty($customHeads)) {
                    // If no explicit customer split, take remaining discount after our share
                    $remAfterOur = max(0, $totalPurchDisc - $ourAmt);
                    if ($remAfterOur > 0) {
                        $headsToRecord[] = [
                            'madd' => 'کسٹمر ڈسکاؤنٹ',
                            'amount' => $remAfterOur,
                            'label' => 'Customer Discount / Claims'
                        ];
                    }
                } else {
                    if ($custAmt > 0) {
                        $headsToRecord[] = [
                            'madd' => 'کسٹمر شیئر',
                            'amount' => $custAmt,
                            'label' => 'Customer Share'
                        ];
                    }
                    foreach ($customHeads as $hName => $hAmt) {
                        if ($hAmt > 0) {
                            $headsToRecord[] = [
                                'madd' => $hName,
                                'amount' => $hAmt,
                                'label' => $hName
                            ];
                        }
                    }

                    // Check if leftover discount remains (excluding our share)
                    $recordedSum = $custAmt + $ourAmt + $customSum;
                    $leftover = round($totalPurchDisc - $recordedSum, 2);
                    if ($leftover > 0) {
                        $headsToRecord[] = [
                            'madd' => 'اضافی کسٹمر کلیمز',
                            'amount' => $leftover,
                            'label' => 'Additional Customer Claims'
                        ];
                    }
                }

                // Delete old synced entries for this purchase to ensure clean state
                JournalEntry::where('account_id', $account->id)
                    ->where(function($q) use ($purchase) {
                        $q->where(function($sub) use ($purchase) {
                            $sub->where('source_type', \App\Models\Purchase::class)
                                ->where('source_id', $purchase->id);
                        })->orWhere('description', 'like', "%Purchase #{$purchase->invoice_no}%");
                    })->delete();

                $entryDate = $purchase->purchase_date ? \Carbon\Carbon::parse($purchase->purchase_date)->format('Y-m-d') : now()->format('Y-m-d');
                $vendorName = $purchase->vendor ? $purchase->vendor->name : 'N/A';

                foreach ($headsToRecord as $h) {
                    $desc = "[مَد: {$h['madd']}] Purchase #{$purchase->invoice_no} ({$vendorName}) - {$h['label']}";
                    JournalEntry::create([
                        'account_id' => $account->id,
                        'source_type' => \App\Models\Purchase::class,
                        'source_id' => $purchase->id,
                        'entry_date' => $entryDate,
                        'debit' => 0,
                        'credit' => $h['amount'],
                        'party_type' => \App\Models\Vendor::class,
                        'party_id' => $purchase->vendor_id,
                        'description' => $desc,
                    ]);
                }
            }

            // Update Account current_balance
            $netCr = (float)JournalEntry::where('account_id', $account->id)
                ->selectRaw('COALESCE(SUM(credit) - SUM(debit), 0) as net')
                ->value('net');
            $account->current_balance = -$netCr; // Negative is Cr
            $account->save();
        } catch (\Throwable $e) {
            \Log::error("Failed to sync Company Discount Ledger: " . $e->getMessage());
        }
    }

    public function toggleStatus($id)
    {
        $account = Account::findOrFail($id);
        $account->status = ! $account->status;
        $account->save();

        return back()->with('success', 'Account status updated successfully!');
    }
}
