<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        
        // User/Account permissions
        Permission::create(['name' => 'view users']);
        Permission::create(['name' => 'create users']);
        Permission::create(['name' => 'edit users']);
        Permission::create(['name' => 'delete users']);
        Permission::create(['name' => 'assign roles']);

        // Patient permissions
        Permission::create(['name' => 'view patients']);
        Permission::create(['name' => 'create patients']);
        Permission::create(['name' => 'edit patients']);
        Permission::create(['name' => 'delete patients']);

        // Appointment permissions
        Permission::create(['name' => 'view appointments']);
        Permission::create(['name' => 'create appointments']);
        Permission::create(['name' => 'edit appointments']);
        Permission::create(['name' => 'delete appointments']);
        Permission::create(['name' => 'schedule reminders']);

        // Encounter/EMR permissions
        Permission::create(['name' => 'view encounters']);
        Permission::create(['name' => 'create encounters']);
        Permission::create(['name' => 'edit encounters']);
        Permission::create(['name' => 'delete encounters']);
        Permission::create(['name' => 'upload files']);

        // Billing permissions
        Permission::create(['name' => 'view invoices']);
        Permission::create(['name' => 'create invoices']);
        Permission::create(['name' => 'edit invoices']);
        Permission::create(['name' => 'delete invoices']);
        Permission::create(['name' => 'manage billing codes']);

        // Reports permissions
        Permission::create(['name' => 'view reports']);
        Permission::create(['name' => 'export reports']);

        // Create roles and assign permissions (as specified in the spec.txt)
        
        // Administrator role - has all permissions
        $adminRole = Role::create(['name' => 'administrator']);
        $adminRole->givePermissionTo(Permission::all());
        
        // FrontDesk Clerk role
        $frontDeskRole = Role::create(['name' => 'frontdesk']);
        $frontDeskRole->givePermissionTo([
            'view patients', 'create patients', 'edit patients',
            'view appointments', 'create appointments', 'edit appointments', 'delete appointments', 'schedule reminders',
            'view encounters',
        ]);

        // Clinician role
        $clinicianRole = Role::create(['name' => 'clinician']);
        $clinicianRole->givePermissionTo([
            'view patients',
            'view appointments', 'edit appointments',
            'view encounters', 'create encounters', 'edit encounters', 'upload files',
        ]);

        // Biller role
        $billerRole = Role::create(['name' => 'biller']);
        $billerRole->givePermissionTo([
            'view patients',
            'view encounters',
            'view invoices', 'create invoices', 'edit invoices', 'manage billing codes',
            'view reports', 'export reports',
        ]);
    }
}