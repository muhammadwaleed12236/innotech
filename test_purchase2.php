<?php
require __DIR__."/vendor/autoload.php";
$app = require_once __DIR__."/bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$p = \App\Models\Purchase::select("id","invoice_no","net_amount")->get();
echo json_encode($p->toArray(), JSON_PRETTY_PRINT);

