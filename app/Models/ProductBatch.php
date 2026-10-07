<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductBatch extends Model
{
    use HasFactory;

    protected $table = 'product_batches';

    protected $fillable = [
        'product_id',
        'variant_key',
        'warehouse_id',
        'batch_no',
        'mfg_date',
        'expiry_date',
        'qty',
        'cost_price',
        'remarks',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function serials()
    {
        return $this->hasMany(ProductSerial::class, 'batch_id');
    }
}
