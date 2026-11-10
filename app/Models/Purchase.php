<?php

namespace App\Models;

use App\Scopes\BranchScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
    use SoftDeletes;

    /**
     * Apply branch-level isolation automatically.
     * Super Admins see all branches; branch users see only their own branch.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new BranchScope);
    }

    protected $guarded = [];

    protected $casts = [
        'purchase_date'              => 'date',
        'subtotal'                   => 'decimal:2',
        'total_retail_value'         => 'decimal:2',
        'total_trade_value'          => 'decimal:2',
        'total_value_after_discount' => 'decimal:2',
        'total_sales_tax_on_retail'  => 'decimal:2',
        'total_sales_tax_on_trade'   => 'decimal:2',
        'wht_percent'                => 'decimal:2',
        'wht_amount'                 => 'decimal:2',
        'total_exclusive_gst'        => 'decimal:2',
        'total_gst'                  => 'decimal:2',
        'discount'                   => 'decimal:2',
        'additional_discount'        => 'decimal:2',
        'company_disc_percent'       => 'decimal:2',
        'company_disc_amount'        => 'decimal:2',
        'our_disc_percent'           => 'decimal:2',
        'our_disc_amount'            => 'decimal:2',
        'customer_disc_percent'      => 'decimal:2',
        'customer_disc_amount'       => 'decimal:2',
        'company_settled_amount'     => 'decimal:2',
        'custom_disc_breakdown'      => 'array',
        'our_disc_breakdown'         => 'array',
        'extra_cost'                 => 'decimal:2',
        'net_amount'                 => 'decimal:2',
        'paid_amount'                => 'decimal:2',
        'due_amount'                 => 'decimal:2',
    ];

    public function branch()   { return $this->belongsTo(Branch::class); }
    public function warehouse(){ return $this->belongsTo(Warehouse::class); }
    public function vendor()   { return $this->belongsTo(Vendor::class, 'vendor_id'); }
    public function items()    { return $this->hasMany(PurchaseItem::class); }
    public function returns()  { return $this->hasMany(PurchaseReturn::class); }
}
