<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'customer_id', 'customer_name', 'customer_name_ur', 'cnic', 'filer_type', 'zone',
        'contact_person', 'mobile', 'email_address', 'contact_person_2', 'mobile_2',
        'email_address_2', 'opening_balance', 'balance_range', 'address', 'status',
        'customer_type', 'previous_balance', 'sales_officer_id',
        'payment_reminder_date', 'reminder_snoozed_at', 'reminder_day', 'source'
    ];

    public function salesOfficer()
    {
        return $this->belongsTo(SalesOfficer::class, 'sales_officer_id');
    }

    /**
     * Polymorphic relationship to journal entries
     */
    public function journalEntries()
    {
        return $this->morphMany(JournalEntry::class, 'party');
    }

    /**
     * Dynamic custom field values relationship
     */
    public function customFieldValues()
    {
        return $this->hasMany(CustomerCustomFieldValue::class, 'customer_id');
    }

    /**
     * Get a custom field value by key
     */
    public function getCustomValue(string $fieldKey, $default = null)
    {
        if ($this->relationLoaded('customFieldValues')) {
            $val = $this->customFieldValues->firstWhere('field_key', $fieldKey);
            return $val ? $val->value : $default;
        }

        $val = $this->customFieldValues()->where('field_key', $fieldKey)->value('value');
        return $val !== null ? $val : $default;
    }

    /**
     * Get all custom field values as key => value array
     */
    public function getAllCustomValues(): array
    {
        if ($this->relationLoaded('customFieldValues')) {
            return $this->customFieldValues->pluck('value', 'field_key')->toArray();
        }

        return $this->customFieldValues()->pluck('value', 'field_key')->toArray();
    }

    /**
     * Get current balance from BalanceService
     */
    public function getPreviousBalanceAttribute()
    {
        // Use BalanceService to calculate the real-time balance
        // including opening balance and journal entries.
        try {
            $balanceService = app(\App\Services\BalanceService::class);
            return $balanceService->getCustomerBalance($this);
        } catch (\Exception $e) {
            // Fallback to column if service fails
            return $this->attributes['previous_balance'] ?? 0;
        }
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) \Illuminate\Support\Str::uuid();
            }
            if (!isset($model->is_synced)) {
                $model->is_synced = 0;
            }
        });
    }
}
