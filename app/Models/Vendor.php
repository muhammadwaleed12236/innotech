<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'cnic',
        'ntn_number',
        'strn_number',
        'address',
        'opening_balance'
    ];

      public function ledger()
    {
        return $this->hasOne(VendorLedger::class);
    }
}
