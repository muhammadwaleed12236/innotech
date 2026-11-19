<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesOfficer extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'name_urdu',
        'type',
        'reference',
        'mobile',
        'cnic',
        'email',
        'address',
        'commission_rate',
        'status',
        'user_id',
    ];

    protected $casts = [
        'commission_rate' => 'float',
        'status' => 'boolean',
    ];

    /**
     * Scope for active officers/bookers
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope for order bookers
     */
    public function scopeBookers($query)
    {
        return $query->whereIn('type', ['order_booker', 'both']);
    }

    /**
     * Scope for salesmen / delivery persons
     */
    public function scopeSalesmen($query)
    {
        return $query->whereIn('type', ['salesman', 'both']);
    }

    /**
     * Linked portal user account
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Sales / Bookings booked by this order booker
     */
    public function bookedSales()
    {
        return $this->hasMany(Sale::class, 'order_booker_id');
    }

    /**
     * Sales assigned to this salesman for delivery
     */
    public function salesmanSales()
    {
        return $this->hasMany(Sale::class, 'salesman_id');
    }

    /**
     * Legacy sales association
     */
    public function sales()
    {
        return $this->hasMany(Sale::class, 'sales_officer_id');
    }

    /**
     * Assigned Customers / Shops
     */
    public function customers()
    {
        return $this->hasMany(Customer::class, 'sales_officer_id');
    }

    /**
     * Auto generate next Booker / Salesman ID (e.g. B-0001, S-0001)
     */
    public static function generateNextCode(string $type = 'order_booker'): string
    {
        $prefix = ($type === 'salesman') ? 'S-' : 'B-';
        $maxRecord = self::where('code', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();

        if (!$maxRecord || empty($maxRecord->code)) {
            return "{$prefix}0001";
        }

        // Extract number from prefix
        $numPart = preg_replace('/[^0-9]/', '', $maxRecord->code);
        $nextNum = ((int)$numPart) + 1;
        return $prefix . str_pad($nextNum, 4, '0', STR_PAD_LEFT);
    }
}
