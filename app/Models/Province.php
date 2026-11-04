<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    use HasFactory;

    protected $table = 'provinces';

    protected $fillable = [
        'name',
        'status',
    ];

    /**
     * Scope for active provinces
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Get cities associated with this province
     */
    public function cities()
    {
        return $this->hasMany(City::class, 'province', 'name');
    }
}
