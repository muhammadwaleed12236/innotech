<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'branch_id',
        'customer_id', 'customer_name', 'customer_name_ur', 'cnic', 'filer_type', 'zone',
        'contact_person', 'mobile', 'email_address', 'contact_person_2', 'mobile_2',
        'email_address_2', 'opening_balance', 'balance_range', 'address', 'status',
        'customer_type', 'party_type', 'linked_vendor_id', 'previous_balance', 'sales_officer_id',
        'payment_reminder_date', 'reminder_snoozed_at', 'reminder_day', 'source', 'province', 'city', 'area'
    ];

    protected $appends = ['strn_number', 'ntn_number', 'is_registered', 'is_filer'];

    public function getStrnNumberAttribute()
    {
        return (!empty($this->attributes['strn_number']) ? $this->attributes['strn_number'] : null)
            ?? (!empty($this->attributes['strn']) ? $this->attributes['strn'] : null)
            ?? $this->getCustomValue('strn_number');
    }

    public function getNtnNumberAttribute()
    {
        return (!empty($this->attributes['ntn_number']) ? $this->attributes['ntn_number'] : null)
            ?? (!empty($this->attributes['ntn']) ? $this->attributes['ntn'] : null)
            ?? $this->getCustomValue('ntn_number');
    }

    public function getIsRegisteredAttribute(): bool
    {
        return !empty(trim((string) $this->strn_number));
    }

    public function getIsFilerAttribute(): bool
    {
        return strtolower((string) $this->filer_type) === 'filer' || !empty(trim((string) $this->ntn_number));
    }

    
    public function getNameAttribute()
    {
        return $this->customer_name;
    }

    public function getPhoneAttribute()
    {
        return $this->mobile;
    }

    public function linkedVendor()
    {
        return $this->belongsTo(Vendor::class, 'linked_vendor_id');
    }
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
        static::addGlobalScope(new \App\Scopes\BranchScope);
        static::creating(function ($model) {
            if (empty($model->branch_id) && \Illuminate\Support\Facades\Auth::check()) {
                $user = \Illuminate\Support\Facades\Auth::user();
                if ($user && !$user->isSuperAdmin()) {
                    $model->branch_id = method_exists($user, 'getEffectiveBranchId')
                        ? $user->getEffectiveBranchId()
                        : ($user->branch_id ?? null);
                }
            }
            if (empty($model->uuid)) {
                $model->uuid = (string) \Illuminate\Support\Str::uuid();
            }
            if (!isset($model->is_synced)) {
                $model->is_synced = 0;
            }
        });
    }
}
