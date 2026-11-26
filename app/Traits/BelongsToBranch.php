<?php

namespace App\Traits;

use App\Scopes\BranchScope;
use Illuminate\Support\Facades\Auth;

trait BelongsToBranch
{
    /**
     * Automatically apply BranchScope on reads and auto-assign branch_id on create
     * for non-superadmin (branch) users.
     */
    protected static function bootBelongsToBranch(): void
    {
        static::addGlobalScope(new BranchScope);

        static::creating(function ($model) {
            if (empty($model->branch_id) && Auth::check()) {
                $user = Auth::user();
                if ($user && !$user->isSuperAdmin()) {
                    $model->branch_id = method_exists($user, 'getEffectiveBranchId')
                        ? $user->getEffectiveBranchId()
                        : ($user->branch_id ?? null);
                }
            }
        });
    }
}
