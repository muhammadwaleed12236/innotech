<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing Purchase Item Batch and Serial fields...\n";
$item = new \App\Models\PurchaseItem();
$item->batch_no = 'BATCH-TEST-001';
$item->serials = ['860000000000001', '860000000000002'];

echo "Batch No: " . $item->batch_no . "\n";
echo "Serials: " . json_encode($item->serials) . "\n";
echo "All good!\n";
