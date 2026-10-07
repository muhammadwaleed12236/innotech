<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$product = App\Models\Product::where('item_code', 'ITEM-0003')->first();
if ($product) {
    $variants = [
        [
            'name' => 'testing',
            'size' => '-',
            'color' => '-',
            'stock' => 360,
            'sale_price' => 1200,
            'purch_price' => 900,
            'alert' => 10,
            'barcode' => 'ITEM-0003'
        ]
    ];
    $product->color = json_encode($variants);
    $product->save();
    echo "Product ITEM-0003 updated with variants.\n";
} else {
    echo "Product not found.\n";
}
