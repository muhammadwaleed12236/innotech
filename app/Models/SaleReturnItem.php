<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SaleReturnItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_return_id',
        'product_id',
        'is_manual',
        'product_name',
        'vendor_id',
        'purchase_price',
        'color',
        'warehouse_id',
        'qty',
        'boxes',
        'loose_pieces',
        'price',
        'trade_price',
        'retail_price',
        'reg_disc_percent',
        'sp_disc_percent',
        'item_discount',
        'sales_tax_percent',
        'sales_tax_amount',
        'further_tax_percent',
        'further_tax_amount',
        'mrp_amount',
        'unit',
        'line_total',
    ];

    protected $casts = [
        'qty' => 'decimal:2',
        'boxes' => 'decimal:2',
        'price' => 'decimal:2',
        'trade_price' => 'decimal:2',
        'retail_price' => 'decimal:2',
        'reg_disc_percent' => 'decimal:2',
        'sp_disc_percent' => 'decimal:2',
        'item_discount' => 'decimal:2',
        'sales_tax_percent' => 'decimal:2',
        'sales_tax_amount' => 'decimal:2',
        'further_tax_percent' => 'decimal:2',
        'further_tax_amount' => 'decimal:2',
        'mrp_amount' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    // Relationships
    public function saleReturn()
    {
        return $this->belongsTo(SaleReturn::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
