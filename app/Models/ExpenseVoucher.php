<?php

namespace App\Models;

use App\Scopes\BranchScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseVoucher extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Apply branch-level isolation automatically.
     * Super Admins see all branches; branch users see only their own branch.
     * Note: expense_vouchers may not have branch_id column; scope is safe (checks column existence).
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new BranchScope);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public static function generateInvoiceNo()
    {
        $last = self::latest('id')->first();
        $nextId = $last ? $last->id + 1 : 1;
        return 'EVID-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
    }
}
