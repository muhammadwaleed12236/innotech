<?php
$file = 'c:/xampp/htdocs/atif_traderss/app/Http/Controllers/PurchaseController.php';
$content = file_get_contents($file);

// 1. Capture oldNetAmount
$oldCapture = "            \$purchase = Purchase::with('items')->findOrFail(\$id);";
$newCapture = "            \$purchase = Purchase::with('items')->findOrFail(\$id);
            \$oldNetAmount = \$purchase->net_amount;";
$content = str_replace($oldCapture, $newCapture, $content, $count1);

// 2. Replace Vendor Ledger Logic
$oldLedger = "            // Vendor ledger (simple overwrite pattern)
            \$prevClosing = \App\Models\VendorLedger::where('vendor_id', \$purchase->vendor_id)
                ->value('closing_balance') ?? 0;
            \App\Models\VendorLedger::updateOrCreate(
                ['vendor_id' => \$purchase->vendor_id],
                [
                    'vendor_id' => \$purchase->vendor_id,
                    'admin_or_user_id' => auth()->id(),
                    'previous_balance' => \$prevClosing,
                    'opening_balance' => \$prevClosing,
                    'closing_balance' => \$prevClosing + \$netAmount,
                ]
            );";

$newLedger = "            \$diff = \$netAmount - \$oldNetAmount;

            // Update Vendor Ledger accurately with diff
            \$vendorLedger = \App\Models\VendorLedger::where('vendor_id', \$purchase->vendor_id)->first();
            if (\$vendorLedger) {
                \$vendorLedger->closing_balance += \$diff;
                \$vendorLedger->save();
            } else {
                \App\Models\VendorLedger::create([
                    'vendor_id' => \$purchase->vendor_id,
                    'admin_or_user_id' => auth()->id(),
                    'previous_balance' => 0,
                    'opening_balance' => 0,
                    'closing_balance' => \$netAmount,
                ]);
            }

            // Adjust Journal Vouchers correctly
            \$voucher = \App\Models\VoucherMaster::where('remarks', \"Purchase Voucher #{\$purchase->invoice_no}\")->first();
            if (\$voucher) {
                \$voucher->total_amount = max(0, \$voucher->total_amount + \$diff);
                \$voucher->save();

                \$balanceService = app(\App\Services\BalanceService::class);
                \$expenseAccountId = \$balanceService->getPurchaseExpenseId();
                \$apAccountId = \$balanceService->getAccountsPayableId();

                \App\Models\JournalEntry::where('source_type', \App\Models\VoucherMaster::class)
                    ->where('source_id', \$voucher->id)
                    ->where('account_id', \$expenseAccountId)
                    ->update(['debit' => \$purchase->net_amount]);

                \App\Models\JournalEntry::where('source_type', \App\Models\VoucherMaster::class)
                    ->where('source_id', \$voucher->id)
                    ->where('account_id', \$apAccountId)
                    ->update(['credit' => \$purchase->net_amount]);
            }";

$content = str_replace($oldLedger, $newLedger, $content, $count2);

file_put_contents($file, $content);
echo "Capture replaced: \$count1, Ledger replaced: \$count2\n";
