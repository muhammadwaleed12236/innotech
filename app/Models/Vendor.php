<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'name',
        'email',
        'phone',
        'cnic',
        'ntn_number',
        'strn_number',
        'address',
        'party_type',
        'linked_customer_id',
        'opening_balance',
        'province',
        'city',
        'area'
    ];

      
    public function linkedCustomer()
    {
        return $this->belongsTo(Customer::class, 'linked_customer_id');
    }
    public function ledger()
    {
        return $this->hasOne(VendorLedger::class);
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new \App\Scopes\BranchScope);
        static::creating(function ($vendor) {
            if (empty($vendor->branch_id) && \Illuminate\Support\Facades\Auth::check()) {
                $user = \Illuminate\Support\Facades\Auth::user();
                if ($user && !$user->isSuperAdmin()) {
                    $vendor->branch_id = method_exists($user, 'getEffectiveBranchId')
                        ? $user->getEffectiveBranchId()
                        : ($user->branch_id ?? null);
                }
            }
        });
    }
}
