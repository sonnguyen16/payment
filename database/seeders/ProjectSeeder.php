<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Project::create([
            'code' => 'WEB-2025-001',
            'name' => 'Website Redesign',
            'description' => 'Thiết kế lại website công ty',
            'budget' => 50000000,
            'spent' => 0,
            'status' => 'active',
            'start_date' => now(),
            'end_date' => now()->addMonths(6),
        ]);

        \App\Models\Project::create([
            'code' => 'APP-2025-002',
            'name' => 'Mobile App Development',
            'description' => 'Phát triển ứng dụng mobile',
            'budget' => 100000000,
            'spent' => 0,
            'status' => 'active',
            'start_date' => now(),
            'end_date' => now()->addMonths(12),
        ]);

        \App\Models\Project::create([
            'code' => 'INF-2025-003',
            'name' => 'Infrastructure Upgrade',
            'description' => 'Nâng cấp hạ tầng IT',
            'budget' => 200000000,
            'spent' => 0,
            'status' => 'active',
            'start_date' => now(),
            'end_date' => now()->addMonths(3),
        ]);
    }
}
