<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('permissions')) {
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

            $perms = [
                'opening.stock.view',
                'opening.stock.create',
                'opening.stock.edit',
                'opening.stock.delete',
            ];

            foreach ($perms as $perm) {
                Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
            }

            // Grant to Super Admin and Admin if roles exist
            $roles = Role::whereIn('name', ['Super Admin', 'superadmin', 'Admin', 'admin'])->get();
            foreach ($roles as $role) {
                $role->givePermissionTo($perms);
            }

            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('permissions')) {
            Permission::whereIn('name', [
                'opening.stock.view',
                'opening.stock.create',
                'opening.stock.edit',
                'opening.stock.delete',
            ])->delete();

            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        }
    }
};
