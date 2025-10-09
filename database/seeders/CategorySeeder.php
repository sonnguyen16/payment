<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Tạm ứng',
                'description' => 'Phiếu tạm ứng tiền cho nhân viên',
                'color' => '#17a2b8',
                'is_active' => true,
            ],
            [
                'name' => 'Đề xuất thanh toán',
                'description' => 'Phiếu đề xuất thanh toán cho nhà cung cấp',
                'color' => '#28a745',
                'is_active' => true,
            ],
            [
                'name' => 'Chi phí văn phòng',
                'description' => 'Chi phí văn phòng phẩm, thiết bị văn phòng',
                'color' => '#ffc107',
                'is_active' => true,
            ],
            [
                'name' => 'Chi phí đi lại',
                'description' => 'Chi phí đi lại, xăng xe, taxi',
                'color' => '#fd7e14',
                'is_active' => true,
            ],
            [
                'name' => 'Chi phí ăn uống',
                'description' => 'Chi phí ăn uống, tiếp khách',
                'color' => '#e83e8c',
                'is_active' => true,
            ],
            [
                'name' => 'Chi phí marketing',
                'description' => 'Chi phí quảng cáo, marketing',
                'color' => '#6f42c1',
                'is_active' => true,
            ],
            [
                'name' => 'Chi phí đào tạo',
                'description' => 'Chi phí đào tạo nhân viên',
                'color' => '#20c997',
                'is_active' => true,
            ],
            [
                'name' => 'Chi phí khác',
                'description' => 'Các chi phí khác không thuộc danh mục trên',
                'color' => '#6c757d',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        $this->command->info('Categories seeded successfully!');
    }
}
