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
    $superadmin = Role::firstOrCreate(['name' => 'Superadmin']);
    $officeAdmin = Role::firstOrCreate(['name' => 'Office Admin']);
    $eventLeader = Role::firstOrCreate(['name' => 'Event Leader']);
    $user = Role::firstOrCreate(['name' => 'User']);
    
    $superadmin = User::firstOrCreate([
        'name' => 'Super Admin',
        'email' => 'superadmin@backpackers.com',
        'password' => bcrypt('qwerty')
    ]);
    $superadmin->assignRole('Superadmin');

    $officeAdmin = User::firstOrCreate([
        'name' => 'Office Admin',
        'email' => 'officeadmin@backpackers.com',
        'password' => bcrypt('qwerty')
    ]);
    $officeAdmin->assignRole('Office Admin');

    $eventLeader = User::firstOrCreate([
        'name' => 'E Leader 1',
        'email' => 'eventleader1@backpackers.com',
        'password' => bcrypt('qwerty')
    ]);
    $eventLeader->assignRole('Event Leader');

    $user = User::firstOrCreate([
        'name' => 'User1',
        'email' => 'user1@backpackers.com',
        'password' => bcrypt('qwerty')
    ]);
    $user->assignRole('User');
}
}
