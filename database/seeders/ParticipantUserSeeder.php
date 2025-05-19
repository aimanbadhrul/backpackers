<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ParticipantUserSeeder extends Seeder
{
    public function run()
    {
        // // Ensure the role exists
        // $participantRole = Role::firstOrCreate(['name' => 'Participant', 'guard_name' => 'backpack']);

        // // Create 10 users with the role Participant
        // for ($i = 1; $i <= 10; $i++) {
        //     $user = User::create([
        //         'name' => 'Participant ' . $i,
        //         'email' => 'participant' . $i . '@example.com',
        //         'password' => Hash::make('password'), // Default password: password
        //     ]);

        //     // Assign role to user
        //     $user->assignRole($participantRole);
        // }
    }
}
