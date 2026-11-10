<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

/**
 * BranchScope – automatically restricts queries to the authenticated user's branch.
 *
 * Super Admins (branch_id IS NULL or isSuperAdmin() === true) bypass this scope
 * and can see records from all branches.
 *
 * Usage: add  `static::addGlobalScope(new BranchScope);`  inside the model's booted() method.
 */
class BranchScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        // Only apply when a user is authenticated
        if (!Auth::check()) {
            return;
        }

        $user = Auth::user();

        // Super Admins bypass the scope (branch_id is null or has super-admin role)
        if ($user->isSuperAdmin()) {
            return;
        }

        // Branch users: restrict to their own branch
        if ($user->branch_id) {
            $builder->where($model->getTable() . '.branch_id', $user->branch_id);
        }
    }
}
