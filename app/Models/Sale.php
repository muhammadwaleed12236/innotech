<?php

// app/Models/Sale.php
namespace App\Models;

use App\Scopes\BranchScope;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    /**
     * Apply branch-level isolation automatically.
     * Super Admins see all branches; branch users see only their own branch.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new BranchScope);

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) \Illuminate\Support\Str::uuid();
            }
            if (!isset($model->is_synced)) {
                $model->is_synced = 0;
            }
        });
    }

    protected $casts = ['selected_terms' => 'array'];
    protected $fillable = [
        'customer_id', 'sales_officer_id', 'reference', 'po_no', 'po_date', 'total_amount_Words', 'total_bill_amount',
        'total_extradiscount', 'total_net', 'cash', 'card', 'change', 'change_account_id',
        'total_items', 'discount_type', 'sale_status', 'invoice_no', 'is_booking',
        'freight_charges', 'freight_type', 'selected_terms',
        'total_gross', 'total_exclusive_gst', 'total_sales_tax', 'total_further_tax', 'total_bonus_qty',
        'total_trade_activity', 'total_mrp_tax', 'total_adv_tax',
        'total_reg_discount', 'total_sp_discount',
        'source', 'branch_id',
    ];

    public function salesOfficer()
    {
        return $this->belongsTo(SalesOfficer::class, 'sales_officer_id', 'id');
    }

    public function customer_relation()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function change_account()
    {
        return $this->belongsTo(Account::class, 'change_account_id', 'id');
    }

    public function product_relation()
    {
        return $this->belongsTo(Product::class, 'product', 'id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function getIsWalkinAttribute()
    {
        return empty($this->customer_id) || !empty($this->walkin_name);
    }

    public function getIsPosAttribute(): bool
    {
        return ($this->source === 'pos');
    }

    public static function generateInvoiceNo($prefix = null)
    {
        return \App\Models\InvoiceSeries::generateNextNo($prefix);
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function journalEntries()
    {
        return $this->morphMany(JournalEntry::class, 'source');
    }

    public function returns()
    {
        return $this->hasMany(SaleReturn::class, 'sale_id');
    }
}
