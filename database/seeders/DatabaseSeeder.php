<?php

namespace Database\Seeders;

use App\Models\Role;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
{
    $superadmin = Role::firstOrCreate(['name' => 'superadmin']);
    $officeAdmin = Role::firstOrCreate(['name' => 'office_admin']);
    $user = Role::firstOrCreate(['name' => 'user']);
    
    $superadmin = User::firstOrCreate([
        'name' => 'Super Admin',
        'email' => 'superadmin@backpackers.com',
        'password' => bcrypt('qwerty')
    ]);
    $superadmin->assignRole('superadmin');

    $officeAdmin = User::firstOrCreate([
        'name' => 'Office Admin',
        'email' => 'officeadmin@backpackers.com',
        'password' => bcrypt('qwerty')
    ]);
    $officeAdmin->assignRole('office_admin');

    $user = User::firstOrCreate([
        'name' => 'User1',
        'email' => 'user1@backpackers.com',
        'password' => bcrypt('qwerty')
    ]);
    $user->assignRole('user');
}
}
