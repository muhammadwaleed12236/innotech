<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;
use App\Models\WarehouseStock;
use Illuminate\Support\Facades\DB;

echo "Syncing all stock from stock_movements...\n";

$products = Product::all();
$count = 0;
foreach ($products as $p) {
    $in = DB::table('stock_movements')->where('product_id', $p->id)->where('type', 'in')->sum('qty');
    $out = DB::table('stock_movements')->where('product_id', $p->id)->where('type', 'out')->sum('qty');
    
    // In SaleController, OUT movements are inserted as negative qty. 
    // Let's check if $out is negative. If so, we just add them.
    // Or we just sum all 'qty' regardless of type if negative is used for out.
    $net_from_sum = DB::table('stock_movements')->where('product_id', $p->id)->sum('qty');
    
    if ($net_from_sum > 0) {
        $stock = WarehouseStock::firstOrNew([
            'product_id' => $p->id,
            'warehouse_id' => 1
        ]);
        
        $stock->total_pieces = $net_from_sum;
        
        $ppb = $p->pieces_per_box > 0 ? $p->pieces_per_box : 1;
        $stock->quantity = round($stock->total_pieces / $ppb, 2);
        $stock->save();
        $count++;
    }
}

echo "Synced $count products.\n";
