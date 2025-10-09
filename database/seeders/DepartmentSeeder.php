<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $offices = \App\Models\Office::all();
        
        foreach ($offices as $office) {
            \App\Models\Department::create(['name' => 'IT', 'office_id' => $office->id]);
            \App\Models\Department::create(['name' => 'Sales', 'office_id' => $office->id]);
            \App\Models\Department::create(['name' => 'Marketing', 'office_id' => $office->id]);
        }
    }
}
