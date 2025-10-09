<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        $employee = \Spatie\Permission\Models\Role::create(['name' => 'employee']);
        $departmentHead = \Spatie\Permission\Models\Role::create(['name' => 'department_head']);
        $accountant = \Spatie\Permission\Models\Role::create(['name' => 'accountant']);
        $ceo = \Spatie\Permission\Models\Role::create(['name' => 'ceo']);
        $admin = \Spatie\Permission\Models\Role::create(['name' => 'admin']);

        // Create permissions
        $permissions = [
            'create_payment_request',
            'edit_own_payment_request',
            'cancel_own_payment_request',
            'approve_payment_request',
            'reject_payment_request',
            'delete_payment_request',
            'process_payment',
            'view_all_payment_requests',
            'view_department_payment_requests',
            'view_office_payment_requests',
            'manage_users',
            'manage_offices',
            'manage_departments',
        ];

        foreach ($permissions as $permission) {
            \Spatie\Permission\Models\Permission::create(['name' => $permission]);
        }

        // Assign permissions to roles
        $employee->givePermissionTo([
            'create_payment_request',
            'edit_own_payment_request',
            'cancel_own_payment_request',
        ]);

        $departmentHead->givePermissionTo([
            'create_payment_request',
            'edit_own_payment_request',
            'cancel_own_payment_request',
            'approve_payment_request',
            'reject_payment_request',
            'delete_payment_request',
            'view_department_payment_requests',
        ]);

        $accountant->givePermissionTo([
            'approve_payment_request',
            'reject_payment_request',
            'delete_payment_request',
            'process_payment',
            'view_office_payment_requests',
        ]);

        $ceo->givePermissionTo([
            'approve_payment_request',
            'reject_payment_request',
            'delete_payment_request',
            'view_all_payment_requests',
        ]);

        $admin->givePermissionTo([
            'manage_users',
            'manage_offices',
            'manage_departments',
            'view_all_payment_requests',
            'delete_payment_request',
        ]);
    }
}
