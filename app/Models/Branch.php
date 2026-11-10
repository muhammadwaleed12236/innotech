<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $guarded = [];

    /** The primary manager / owner of this branch */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** All users assigned to this branch */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /** All purchases belonging to this branch */
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    /** All sales belonging to this branch */
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
