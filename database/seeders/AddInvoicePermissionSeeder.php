<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class AddInvoicePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create the permission if it doesn't exist
        Permission::firstOrCreate(['name' => 'view invoices', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'create invoices', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'edit invoices', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'delete invoices', 'guard_name' => 'web']);
        
        // Give this permission to all existing roles that should have it
        $roles = ['super-admin', 'administrator', 'admin', 'manager', 'clinician'];
        
        foreach ($roles as $roleName) {
            $role = Role::where('name', $roleName)->first();
            
            if ($role) {
                $role->givePermissionTo('view invoices');
                
                // Admin roles also get full invoice management permissions
                if (in_array($roleName, ['super-admin', 'administrator', 'admin', 'manager'])) {
                    $role->givePermissionTo('create invoices');
                    $role->givePermissionTo('edit invoices');
                    $role->givePermissionTo('delete invoices');
                }
            }
        }
        
        // Give the permissions to all users (for development purposes only)
        $users = User::all();
        foreach ($users as $user) {
            $user->givePermissionTo('view invoices');
            $user->givePermissionTo('create invoices'); // Add create permission for development
            $user->givePermissionTo('edit invoices');   // Add edit permission for development
            $user->givePermissionTo('delete invoices'); // Add delete permission for development
        }
    }
}