<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerClaim extends Model
{
    use \App\Traits\BelongsToBranch;
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'claim_date' => 'date',
        'total_claim_amount' => 'decimal:2',
        'company_allowance_before' => 'decimal:2',
        'claim_adjusted' => 'decimal:2',
        'company_remaining_balance' => 'decimal:2',
        'refund_amount' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function refundAccount()
    {
        return $this->belongsTo(Account::class, 'refund_account_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(CustomerClaimItem::class);
    }
}
