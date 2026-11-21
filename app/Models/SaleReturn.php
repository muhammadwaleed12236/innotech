<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SaleReturn extends Model
{
    use HasFactory;

    protected $table = 'sale_returns';

    protected $fillable = [
        'sale_id',
        'return_invoice',
        'customer_id',
        'sales_officer_id',
        'order_booker_id',
        'salesman_id',
        'walkin_name',
        'warehouse_id',
        'return_date',
        'bill_amount',
        'item_discount',
        'total_reg_discount',
        'total_sp_discount',
        'total_sales_tax',
        'total_further_tax',
        'total_adv_tax',
        'extra_discount',
        'net_amount',
        'paid',
        'balance',
        'remarks',
        'status',
    ];

    protected $casts = [
        'return_date' => 'date',
        'bill_amount' => 'decimal:2',
        'item_discount' => 'decimal:2',
        'total_reg_discount' => 'decimal:2',
        'total_sp_discount' => 'decimal:2',
        'total_sales_tax' => 'decimal:2',
        'total_further_tax' => 'decimal:2',
        'total_adv_tax' => 'decimal:2',
        'extra_discount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'paid' => 'decimal:2',
        'balance' => 'decimal:2',
    ];

    // Relationships
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function customer_relation()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function salesOfficer()
    {
        return $this->belongsTo(SalesOfficer::class, 'sales_officer_id');
    }

    public function orderBooker()
    {
        return $this->belongsTo(SalesOfficer::class, 'order_booker_id');
    }

    public function salesman()
    {
        return $this->belongsTo(SalesOfficer::class, 'salesman_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function items()
    {
        return $this->hasMany(SaleReturnItem::class);
    }
}
