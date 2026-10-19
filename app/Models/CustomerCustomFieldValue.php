<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerCustomFieldValue extends Model
{
    use HasFactory;

    protected $table = 'customer_custom_field_values';

    protected $fillable = [
        'customer_id',
        'field_definition_id',
        'field_key',
        'value',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function fieldDefinition()
    {
        return $this->belongsTo(CustomerFieldDefinition::class, 'field_definition_id');
    }
}
