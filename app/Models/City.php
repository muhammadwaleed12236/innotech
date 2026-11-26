<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use \App\Traits\BelongsToBranch;
    use HasFactory;

    protected $fillable = [
        'name',
        'province',
    ];

    public function areas()
    {
        return $this->hasMany(Area::class, 'city_id');
    }
}
