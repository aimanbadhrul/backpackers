<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        // Define permissions
        $permissions = [
            // Events
            'create events',
            'edit own events',
            'submit events for approval',
            'approve events',
            'view all events',

            // Applications
            'view applications',
            'approve applications',
            'reject applications',

            // Users & Roles
            'manage users',
            'manage roles',
            'manage permissions',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to roles
        $superadmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $officeAdmin = Role::firstOrCreate(['name' => 'Office Admin']);
        $eventLeader = Role::firstOrCreate(['name' => 'Event Leader']);
        $participant = Role::firstOrCreate(['name' => 'Participant']);

        // Superadmin gets all permissions
        $superadmin->syncPermissions($permissions);

        // Office Admin
        $officeAdmin->syncPermissions([
            'approve events',
            'view all events',
            'view applications',
            'approve applications',
            'reject applications',
        ]);

        // Event Leader
        $eventLeader->syncPermissions([
            'create events',
            'edit own events',
            'submit events for approval',
            'view all events',
        ]);
    }
}
