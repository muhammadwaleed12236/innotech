<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    protected $guarded = [];

    protected $casts = [
        'price'                => 'decimal:2',
        'retail_price'         => 'decimal:2',
        'retail_value'         => 'decimal:2',
        'trade_value'          => 'decimal:2',
        'item_discount'        => 'decimal:2',
        'value_after_discount' => 'decimal:2',
        'gst_percent'          => 'decimal:2',
        'gst_amount'           => 'decimal:2',
        'sales_tax_on_retail'  => 'decimal:2',
        'sales_tax_on_trade'   => 'decimal:2',
        'exclusive_gst_amount' => 'decimal:2',
        'line_total'           => 'decimal:2',
        'is_third_schedule'    => 'boolean',
        'serials'              => 'array',
    ];

    public function purchase()  { return $this->belongsTo(Purchase::class); }
    public function product()   { return $this->belongsTo(Product::class); }
    public function warehouse() { return $this->belongsTo(Warehouse::class); }
}
