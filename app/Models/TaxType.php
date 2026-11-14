<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaxType extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'st_purchase_active'         => 'boolean',
        'st_purchase_rate'           => 'decimal:2',
        'st_sale_active'             => 'boolean',
        'st_sale_rate'               => 'decimal:2',
        'mrp_tax_active'             => 'boolean',
        'mrp_tax_rate'               => 'decimal:2',
        'unreg_st_active'            => 'boolean',
        'unreg_st_rate'              => 'decimal:2',
        'excise_duty_active'         => 'boolean',
        'excise_duty_rate'           => 'decimal:2',
        'adv_tax_purchase_active'    => 'boolean',
        'adv_tax_purchase_rate'      => 'decimal:2',
        'adv_tax_sale_active'        => 'boolean',
        'adv_tax_sale_filer_rate'    => 'decimal:2',
        'adv_tax_sale_non_filer_rate'=> 'decimal:2',
        'applied_from'               => 'date',
        'include_special_disc'       => 'boolean',
        'is_default'                 => 'boolean',
        'status'                     => 'boolean',
    ];

    /**
     * Scope for active tax types
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Check if this tax type operates as Third Schedule (tax on Retail / MRP)
     */
    public function isThirdSchedule(): bool
    {
        return $this->tax_applied_on === 'retail_value' || (bool) $this->mrp_tax_active;
    }

    /**
     * Get effective Purchase Sales Tax rate (%)
     */
    public function getPurchaseGstRate(): float
    {
        if ($this->isThirdSchedule() && $this->mrp_tax_active) {
            return (float) ($this->mrp_tax_rate ?: $this->st_purchase_rate);
        }
        return $this->st_purchase_active ? (float) $this->st_purchase_rate : 0.0;
    }

    /**
     * Get effective Purchase Advance Tax rate (%)
     */
    public function getPurchaseAdvTaxRate(): float
    {
        return $this->adv_tax_purchase_active ? (float) $this->adv_tax_purchase_rate : 0.0;
    }

    /**
     * Get effective Sale Sales Tax rate (%)
     */
    public function getSaleGstRate(): float
    {
        if ($this->isThirdSchedule() && $this->mrp_tax_active) {
            return (float) ($this->mrp_tax_rate ?: $this->st_sale_rate);
        }
        return $this->st_sale_active ? (float) $this->st_sale_rate : 0.0;
    }

    /**
     * Get effective Sale Advance Tax rate (%)
     */
    public function getSaleAdvTaxRate(bool $isFiler = true): float
    {
        if (!$this->adv_tax_sale_active) {
            return 0.0;
        }
        return (float) ($isFiler ? $this->adv_tax_sale_filer_rate : $this->adv_tax_sale_non_filer_rate);
    }

    /**
     * Products associated with this tax type
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'tax_type_id');
    }

    /**
     * Purchase items associated with this tax type
     */
    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class, 'tax_type_id');
    }
}
