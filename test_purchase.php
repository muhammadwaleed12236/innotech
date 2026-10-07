<?php
require __DIR__."/vendor/autoload.php";
$app = require_once __DIR__."/bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$p = \App\Models\Purchase::latest("id")->first();
echo "Purchase:\n";
echo json_encode($p->toArray(), JSON_PRETTY_PRINT) . "\n\n";

$v = \App\Models\VoucherMaster::where("remarks", "Purchase Voucher #" . $p->invoice_no)->first();
if ($v) {
    echo "Voucher:\n";
    echo json_encode($v->toArray(), JSON_PRETTY_PRINT) . "\n\n";
    
    $je = \App\Models\JournalEntry::where("source_type", \App\Models\VoucherMaster::class)
            ->where("source_id", $v->id)->get();
    echo "Journal Entries:\n";
    echo json_encode($je->toArray(), JSON_PRETTY_PRINT) . "\n\n";
} else {
    echo "No Voucher found for invoice_no " . $p->invoice_no . "\n";
}

