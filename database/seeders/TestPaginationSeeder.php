<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use App\Models\Office;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestPaginationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating test data for pagination...');
        
        // Tạo thêm offices nếu cần
        $offices = Office::all();
        if ($offices->count() < 3) {
            Office::create(['name' => 'Đà Nẵng', 'location' => 'Đà Nẵng, Việt Nam']);
            Office::create(['name' => 'Cần Thơ', 'location' => 'Cần Thơ, Việt Nam']);
            $offices = Office::all();
        }
        
        // Tạo 250 projects để test pagination
        $this->command->info('Creating 250 projects...');
        for ($i = 1; $i <= 250; $i++) {
            Project::create([
                'code' => 'TEST_' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'name' => 'Dự án Test ' . $i,
                'description' => 'Mô tả cho dự án test số ' . $i . '. Đây là dự án được tạo để test pagination.',
                'budget' => rand(10000000, 100000000), // 10M - 100M VND
                'spent' => 0,
                'status' => collect(['active', 'completed', 'cancelled'])->random(),
                'start_date' => now()->subDays(rand(1, 365)),
                'end_date' => now()->addDays(rand(30, 365)),
            ]);
            
            if ($i % 50 == 0) {
                $this->command->info("Created {$i} projects...");
            }
        }
        
        // Tạo thêm users để test
        $this->command->info('Creating additional users...');
        for ($i = 1; $i <= 100; $i++) {
            User::create([
                'name' => 'Test User ' . $i,
                'email' => 'testuser' . $i . '@example.com',
                'password' => bcrypt('password'),
                'office_id' => $offices->random()->id,
                'department_id' => null,
            ]);
            
            if ($i % 25 == 0) {
                $this->command->info("Created {$i} users...");
            }
        }
        
        $this->command->info('Test pagination data created successfully!');
        $this->command->info('Projects: ' . Project::count());
        $this->command->info('Users: ' . User::count());
    }
}
