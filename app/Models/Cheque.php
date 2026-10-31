<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cheque extends Model
{
    use HasFactory;

    protected $fillable = [
        'cheque_no',
        'bank_name',
        'cheque_date',
        'amount',
        'customer_id',
        'customer_payment_id',
        'status', // in_hand, deposited, cleared, bounced
        'deposit_account_id',
        'cleared_at',
        'bounced_at',
        'bounce_reason',
        'note',
        'created_by',
    ];

    protected $casts = [
        'cheque_date' => 'date',
        'cleared_at' => 'date',
        'bounced_at' => 'date',
        'amount' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function payment()
    {
        return $this->belongsTo(CustomerPayment::class, 'customer_payment_id');
    }

    public function depositAccount()
    {
        return $this->belongsTo(Account::class, 'deposit_account_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function journalEntries()
    {
        return $this->morphMany(JournalEntry::class, 'source');
    }

    // Scopes
    public function scopeInHand($query)
    {
        return $query->where('status', 'in_hand');
    }

    public function scopeCleared($query)
    {
        return $query->where('status', 'cleared');
    }

    public function scopeBounced($query)
    {
        return $query->where('status', 'bounced');
    }

    public function scopeDueToday($query)
    {
        return $query->whereDate('cheque_date', Carbon::today())
            ->whereIn('status', ['in_hand', 'deposited']);
    }

    public function scopeDueUpcoming($query, $days = 3)
    {
        return $query->whereBetween('cheque_date', [
                Carbon::tomorrow()->toDateString(),
                Carbon::today()->addDays($days)->toDateString(),
            ])
            ->whereIn('status', ['in_hand', 'deposited']);
    }

    public function scopeOverdue($query)
    {
        return $query->whereDate('cheque_date', '<', Carbon::today())
            ->whereIn('status', ['in_hand', 'deposited']);
    }

    // Status Helper
    public function getDueBadgeAttribute()
    {
        if ($this->status === 'cleared') {
            return ['class' => 'bg-success text-white', 'text' => '✓ Cleared in Bank'];
        }
        if ($this->status === 'bounced') {
            return ['class' => 'bg-danger text-white', 'text' => '✗ Bounced'];
        }

        $today = Carbon::today();
        $date = Carbon::parse($this->cheque_date);

        if ($date->isPast() && !$date->isToday()) {
            return ['class' => 'bg-danger text-white', 'text' => '⚠️ Overdue (' . $date->diffForHumans() . ')'];
        } elseif ($date->isToday()) {
            return ['class' => 'bg-warning text-dark fw-bold', 'text' => '🔔 Due Today (Deposit in Bank)'];
        } else {
            $daysLeft = (int) $today->diffInDays($date);
            if ($daysLeft <= 3) {
                return ['class' => 'bg-warning text-dark fw-bold', 'text' => '⏳ Due in ' . $daysLeft . ' Days (Alert)'];
            }
            return ['class' => 'bg-info text-dark', 'text' => '📅 PDC (Due in ' . $daysLeft . ' Days)'];
        }
    }
}
