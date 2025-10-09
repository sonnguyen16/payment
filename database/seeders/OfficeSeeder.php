<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OfficeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Office::create(['name' => 'HCM', 'location' => 'Hồ Chí Minh']);
        \App\Models\Office::create(['name' => 'Hà Nội', 'location' => 'Hà Nội']);
        \App\Models\Office::create(['name' => 'Vũng Tàu', 'location' => 'Bà Rịa - Vũng Tàu']);
    }
}
