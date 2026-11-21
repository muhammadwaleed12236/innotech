<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerClaimItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'pieces_per_box' => 'decimal:2',
        'cartons' => 'decimal:2',
        'loose_pieces' => 'decimal:2',
        'total_pieces' => 'decimal:2',
        'rate' => 'decimal:2',
        'line_total' => 'decimal:2',
        'expiry_date' => 'date',
        'company_disc_share_percent' => 'decimal:2',
        'company_disc_share_amount' => 'decimal:2',
    ];

    public function claim()
    {
        return $this->belongsTo(CustomerClaim::class, 'customer_claim_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
