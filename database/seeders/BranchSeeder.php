<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Branch;
use App\Models\Warehouse;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure we have branch managers
        $user3 = User::firstOrCreate(
            ['email' => 'isb@branch.com'],
            [
                'name' => 'Islamabad Manager',
                'password' => Hash::make('password'),
                'usertype' => 'admin',
            ]
        );

        $branches = [
            [
                'id' => 1,
                'name' => 'Main Branch - Karachi',
                'address' => 'I.I. Chundrigar Road, Karachi',
                'number' => '021-34567890',
                'user_id' => 1,
            ],
            [
                'id' => 2,
                'name' => 'Lahore Branch',
                'address' => 'Gulberg III, Lahore',
                'number' => '042-35789012',
                'user_id' => 2,
            ],
            [
                'id' => 3,
                'name' => 'Islamabad Branch',
                'address' => 'Blue Area, Islamabad',
                'number' => '051-2890123',
                'user_id' => $user3->id,
            ],
        ];

        foreach ($branches as $b) {
            Branch::updateOrCreate(['id' => $b['id']], $b);
        }

        // Link warehouses to respective branches
        Warehouse::where('id', 1)->update(['branch_id' => 1]);
        Warehouse::where('id', 2)->update(['branch_id' => 2]);
        Warehouse::where('id', 3)->update(['branch_id' => 3]);

        // Ensure sales without branch_id have a default branch
        DB::table('sales')->whereNull('branch_id')->update(['branch_id' => 1]);
        DB::table('purchases')->whereNull('branch_id')->update(['branch_id' => 1]);
    }
}
