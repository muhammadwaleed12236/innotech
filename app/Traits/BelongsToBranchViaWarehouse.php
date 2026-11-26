<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait BelongsToBranchViaWarehouse
{
    /**
     * Scope warehouse-owned stock/batch/serial/movement models to the authenticated user's branch.
     */
    protected static function bootBelongsToBranchViaWarehouse(): void
    {
        static::addGlobalScope('branch_via_warehouse', function (Builder $builder) {
            if (!Auth::check()) {
                return;
            }
            $user = Auth::user();
            if ($user->isSuperAdmin()) {
                return;
            }
            $branchId = method_exists($user, 'getEffectiveBranchId')
                ? $user->getEffectiveBranchId()
                : ($user->branch_id ?? null);

            if ($branchId) {
                $builder->whereHas('warehouse', function ($q) use ($branchId) {
                    $q->where('branch_id', $branchId);
                });
            } else {
                $builder->whereRaw('1 = 0');
            }
        });
    }
}
