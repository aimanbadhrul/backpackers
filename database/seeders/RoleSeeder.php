<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create(['name' => 'Superadmin']);
        Role::create(['name' => 'Office Admin']);
        Role::create(['name' => 'Event Leader']);
        Role::create(['name' => 'User']);
    }
}
