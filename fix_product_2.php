<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$product = App\Models\Product::where('item_code', 'ITEM-0002')->first();
if ($product) {
    $variants = [
        [
            'name' => 'trouser casual',
            'size' => '-',
            'color' => '-',
            'stock' => 230,
            'sale_price' => 1200,
            'purch_price' => 850,
            'alert' => 10,
            'barcode' => '728068722320'
        ]
    ];
    $product->color = json_encode($variants);
    $product->save();
    echo "Product ITEM-0002 updated with variants.\n";
} else {
    echo "Product not found.\n";
}
