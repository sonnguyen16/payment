<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hcmOffice = \App\Models\Office::where('name', 'HCM')->first();
        $itDept = \App\Models\Department::where('name', 'IT')->where('office_id', $hcmOffice->id)->first();


        // Admin
        $admin = \App\Models\User::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'office_id' => null,
            'department_id' => null,
        ]);
        $admin->assignRole('admin');

        // Employee
        $employee = \App\Models\User::create([
            'name' => 'NhanVien Test',
            'email' => 'employee@test.com',
            'password' => bcrypt('password'),
            'office_id' => $hcmOffice->id,
            'department_id' => $itDept->id,
        ]);
        $employee->assignRole('employee');

        // Department Head
        $manager = \App\Models\User::create([
            'name' => 'Trưởng Bộ Phận Test',
            'email' => 'manager@test.com',
            'password' => bcrypt('password'),
            'department_id' => $itDept->id,
            'office_id' => $hcmOffice->id,
        ]);
        $manager->assignRole('department_head');
        $itDept->update(['head_user_id' => $manager->id]);

        // Accountant
        $accountant = \App\Models\User::create([
            'name' => 'Kế Toán Test',
            'email' => 'accountant@test.com',
            'password' => bcrypt('password'),
            'office_id' => $hcmOffice->id,
        ]);
        $accountant->assignRole('accountant');

        // CEO
        $ceo = \App\Models\User::create([
            'name' => 'Tổng Giám Đốc Test',
            'email' => 'ceo@test.com',
            'password' => bcrypt('password'),
        ]);
        $ceo->assignRole('ceo');
    }
}
