<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountHead;
use App\Models\Cheque;
use App\Models\Customer;
use App\Models\CustomerLedger;
use App\Models\CustomerPayment;
use App\Services\JournalEntryService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChequeController extends Controller
{
    protected $journalService;

    public function __construct(JournalEntryService $journalService)
    {
        $this->journalService = $journalService;
    }

    /**
     * Display Cheque Register and Management Screen
     */
    public function index(Request $request)
    {
        try {
            \Illuminate\Support\Facades\Artisan::call('cheques:check-due');
        } catch (\Exception $e) {}
        $status = $request->get('status', 'all');
        $filterDue = $request->get('due', 'all');
        $search = $request->get('search');

        $query = Cheque::with(['customer', 'depositAccount', 'creator'])->orderBy('cheque_date', 'asc');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($filterDue === 'today') {
            $query->dueToday();
        } elseif ($filterDue === 'upcoming') {
            $query->dueUpcoming(3);
        } elseif ($filterDue === 'overdue') {
            $query->overdue();
        } elseif ($filterDue === 'pdc') {
            $query->whereDate('cheque_date', '>', Carbon::today())->whereIn('status', ['in_hand', 'deposited']);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('cheque_no', 'like', "%{$search}%")
                  ->orWhere('bank_name', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('customer_name', 'like', "%{$search}%");
                  });
            });
        }

        $cheques = $query->paginate(25);

        // Calculate Overview Statistics
        $today = Carbon::today()->toDateString();
        $threeDaysLater = Carbon::today()->addDays(3)->toDateString();

        $stats = [
            'in_hand_count' => Cheque::where('status', 'in_hand')->count(),
            'in_hand_amount' => Cheque::where('status', 'in_hand')->sum('amount'),

            'due_today_count' => Cheque::whereIn('status', ['in_hand', 'deposited'])
                ->whereDate('cheque_date', $today)->count(),
            'due_today_amount' => Cheque::whereIn('status', ['in_hand', 'deposited'])
                ->whereDate('cheque_date', $today)->sum('amount'),

            'upcoming_count' => Cheque::whereIn('status', ['in_hand', 'deposited'])
                ->whereBetween('cheque_date', [Carbon::tomorrow()->toDateString(), $threeDaysLater])->count(),
            'upcoming_amount' => Cheque::whereIn('status', ['in_hand', 'deposited'])
                ->whereBetween('cheque_date', [Carbon::tomorrow()->toDateString(), $threeDaysLater])->sum('amount'),

            'overdue_count' => Cheque::whereIn('status', ['in_hand', 'deposited'])
                ->whereDate('cheque_date', '<', $today)->count(),
            'overdue_amount' => Cheque::whereIn('status', ['in_hand', 'deposited'])
                ->whereDate('cheque_date', '<', $today)->sum('amount'),

            'pdc_count' => Cheque::whereIn('status', ['in_hand', 'deposited'])
                ->whereDate('cheque_date', '>', $today)->count(),
            'pdc_amount' => Cheque::whereIn('status', ['in_hand', 'deposited'])
                ->whereDate('cheque_date', '>', $today)->sum('amount'),

            'cleared_count' => Cheque::where('status', 'cleared')->count(),
            'cleared_amount' => Cheque::where('status', 'cleared')->sum('amount'),

            'bounced_count' => Cheque::where('status', 'bounced')->count(),
            'bounced_amount' => Cheque::where('status', 'bounced')->sum('amount'),
        ];

        // Fetch Cash Accounts (for direct cheque encashment)
        $cashHeadIds = AccountHead::where('name', 'LIKE', '%cash%')->pluck('id');
        $cashAccounts = Account::where(function ($q) use ($cashHeadIds) {
                $q->whereIn('head_id', $cashHeadIds)
                  ->orWhere('title', 'LIKE', '%cash%');
            })
            ->where('title', 'NOT LIKE', '%cheque%')
            ->where('status', 1)
            ->get();

        // Fetch Bank Accounts (for bank clearance / deposit)
        $bankHeadIds = AccountHead::where('name', 'LIKE', '%bank%')->pluck('id');
        $bankAccounts = Account::where(function ($q) use ($bankHeadIds) {
                $q->whereIn('head_id', $bankHeadIds)
                  ->orWhere('title', 'LIKE', '%bank%');
            })
            ->where('title', 'NOT LIKE', '%cheque%')
            ->where('status', 1)
            ->get();

        if ($bankAccounts->isEmpty() && $cashAccounts->isEmpty()) {
            $bankAccounts = Account::where('status', 1)->where('title', 'NOT LIKE', '%cheque%')->get();
        }

        $customers = Customer::orderBy('customer_name', 'asc')->get();

        return view('admin_panel.cheques.index', compact('cheques', 'stats', 'bankAccounts', 'cashAccounts', 'customers', 'status', 'filterDue'));
    }

    /**
     * Store Cheque directly from Cheque screen
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'cheque_no' => 'required|string|max:100',
            'bank_name' => 'required|string|max:150',
            'cheque_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'note' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $userId = Auth::id();

            // 1. Create CustomerPayment
            $payment = CustomerPayment::create([
                'customer_id' => $request->customer_id,
                'admin_or_user_id' => $userId,
                'amount' => $request->amount,
                'payment_method' => 'Cheque',
                'payment_date' => date('Y-m-d'),
                'note' => $request->note,
            ]);

            // 2. Create Cheque
            $cheque = Cheque::create([
                'cheque_no' => $request->cheque_no,
                'bank_name' => $request->bank_name,
                'cheque_date' => $request->cheque_date,
                'amount' => $request->amount,
                'customer_id' => $request->customer_id,
                'customer_payment_id' => $payment->id,
                'status' => 'in_hand',
                'note' => $request->note,
                'created_by' => $userId,
            ]);

            // 3. Update Customer Ledger (Minus/Payment Received)
            $latestLedger = CustomerLedger::where('customer_id', $request->customer_id)->latest()->first();
            $prevBalance = $latestLedger ? $latestLedger->closing_balance : (Customer::find($request->customer_id)->opening_balance ?? 0);
            $newBalance = $prevBalance - $request->amount;

            CustomerLedger::create([
                'customer_id' => $request->customer_id,
                'admin_or_user_id' => $userId,
                'previous_balance' => $prevBalance,
                'opening_balance' => 0,
                'closing_balance' => $newBalance,
                'description' => "Payment: Cheque #{$request->cheque_no} ({$request->bank_name})" . ($request->note ? " - {$request->note}" : ''),
            ]);

            DB::commit();
            return redirect()->route('cheques.index')->with('success', 'Cheque received and customer ledger updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error saving cheque: ' . $e->getMessage()]);
        }
    }

    /**
     * Mark Cheque as Cleared in Bank (Updates Bank Balance)
     */
    public function markCleared(Request $request, $id)
    {
        $request->validate([
            'deposit_account_id' => 'required|exists:accounts,id',
            'cleared_at' => 'required|date',
            'clearing_note' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $cheque = Cheque::findOrFail($id);

            if ($cheque->status === 'cleared') {
                return back()->withErrors(['error' => 'Cheque is already marked as cleared.']);
            }

            $targetAccount = Account::findOrFail($request->deposit_account_id);
            $isCash = stripos($targetAccount->title, 'cash') !== false;
            $actionLabel = $isCash ? 'Encashed to Cash' : 'Cleared in Bank';

            $cheque->status = 'cleared';
            $cheque->deposit_account_id = $request->deposit_account_id;
            $cheque->cleared_at = $request->cleared_at;
            if ($request->clearing_note) {
                $cheque->note = ($cheque->note ? $cheque->note . ' | ' : '') . $actionLabel . ': ' . $request->clearing_note;
            } else {
                $cheque->note = ($cheque->note ? $cheque->note . ' | ' : '') . $actionLabel;
            }
            $cheque->save();

            // Record Accounting Journal Entry: DEBIT Target Account (Increases Bank or Cash balance!)
            $desc = "Cheque #{$cheque->cheque_no} ({$cheque->bank_name}) {$actionLabel} -> {$targetAccount->title}";
            if ($cheque->customer) {
                $desc .= " - Party: " . $cheque->customer->customer_name;
            }

            $this->journalService->recordEntry(
                $cheque,
                $request->deposit_account_id,
                $cheque->amount, // Debit (Increases Bank or Cash balance!)
                0,               // Credit
                $desc,
                $request->cleared_at,
                $cheque->customer
            );

            DB::commit();
            $msgType = $isCash ? "Cash in Hand balance updated" : "Bank balance updated";
            return redirect()->route('cheques.index')->with('success', "Cheque #{$cheque->cheque_no} {$actionLabel} successfully! {$msgType}.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to clear cheque: ' . $e->getMessage()]);
        }
    }

    /**
     * Mark Cheque as Bounced (Reverses Customer Ledger)
     */
    public function markBounced(Request $request, $id)
    {
        $request->validate([
            'bounce_reason' => 'required|string|max:255',
            'bounced_at' => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            $cheque = Cheque::findOrFail($id);

            // If it was cleared before, reverse journal entries
            if ($cheque->status === 'cleared') {
                $this->journalService->reverseEntriesForSource($cheque);
            }

            $cheque->status = 'bounced';
            $cheque->bounce_reason = $request->bounce_reason;
            $cheque->bounced_at = $request->bounced_at;
            $cheque->save();

            // Reversal on Customer Ledger: Add balance back (+ Plus)
            if ($cheque->customer_id) {
                $latestLedger = CustomerLedger::where('customer_id', $cheque->customer_id)->latest()->first();
                $prevBalance = $latestLedger ? $latestLedger->closing_balance : (Customer::find($cheque->customer_id)->opening_balance ?? 0);
                $newBalance = $prevBalance + $cheque->amount;

                CustomerLedger::create([
                    'customer_id' => $cheque->customer_id,
                    'admin_or_user_id' => Auth::id(),
                    'previous_balance' => $prevBalance,
                    'opening_balance' => 0,
                    'closing_balance' => $newBalance,
                    'description' => "Cheque Bounced Reversal: #{$cheque->cheque_no} ({$cheque->bank_name}) - Reason: {$request->bounce_reason}",
                ]);
            }

            DB::commit();
            return redirect()->route('cheques.index')->with('success', "Cheque #{$cheque->cheque_no} marked as bounced. Customer balance restored.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to mark cheque as bounced: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete Cheque
     */
    public function destroy($id)
    {
        $cheque = Cheque::findOrFail($id);

        if ($cheque->status === 'cleared') {
            $this->journalService->reverseEntriesForSource($cheque);
        }

        if ($cheque->customer_payment_id) {
            CustomerPayment::where('id', $cheque->customer_payment_id)->delete();
        }

        $cheque->delete();
        return redirect()->route('cheques.index')->with('success', 'Cheque deleted successfully.');
    }

    /**
     * API for Header Notification Badge / Daily Alerts
     */
    public function getAlerts()
    {
        $today = Carbon::today()->toDateString();
        $threeDaysLater = Carbon::today()->addDays(3)->toDateString();

        $dueToday = Cheque::with('customer')
            ->whereIn('status', ['in_hand', 'deposited'])
            ->whereDate('cheque_date', $today)
            ->get();

        $overdue = Cheque::with('customer')
            ->whereIn('status', ['in_hand', 'deposited'])
            ->whereDate('cheque_date', '<', $today)
            ->get();

        $upcoming = Cheque::with('customer')
            ->whereIn('status', ['in_hand', 'deposited'])
            ->whereBetween('cheque_date', [Carbon::tomorrow()->toDateString(), $threeDaysLater])
            ->get();

        return response()->json([
            'due_today_count' => $dueToday->count(),
            'due_today' => $dueToday,
            'overdue_count' => $overdue->count(),
            'overdue' => $overdue,
            'upcoming_count' => $upcoming->count(),
            'upcoming' => $upcoming,
            'total_alerts' => $dueToday->count() + $overdue->count(),
        ]);
    }
}
