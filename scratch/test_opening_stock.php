<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\StockMovement;
use App\Models\ProductBatch;
use App\Models\ProductSerial;

echo "Testing long notes in stock_movements...\n";

$longNote = "Opening Stock [SERIAL] | Variant: baby Incubator | With Standard Accessories Baby Incubator with Trolley 01 Nos Baby Incubator Hood 01 Nos Temperature Sensor 01 Temperature Probes 01 Skin Probe 01 Monitor Shelf 01 IV Pole 01 Water Resoivoir 01|Rescate 03|- | Serials: 1 | Multi-Product Opening Stock Terminal Entry " . str_repeat("Extended Test Details ", 10);

try {
    $sm = StockMovement::create([
        'product_id' => 1,
        'type'       => 'adjustment',
        'qty'        => 1,
        'ref_type'   => 'OPENING_STOCK',
        'note'       => $longNote,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    echo "Successfully created StockMovement ID: {$sm->id}\n";
    echo "Note length inserted: " . strlen($sm->note) . " characters.\n";

    // Clean up test record
    $sm->delete();
    echo "Test record deleted successfully.\n";
    echo "ALL TESTS PASSED PERFECTLY!\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
