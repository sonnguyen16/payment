<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Office;
use App\Models\Department;
use App\Models\Project;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class PaymentRequestTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo roles nếu chưa có
        $roles = ['employee', 'department_head', 'accountant', 'ceo', 'admin'];
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        // Tạo 2 văn phòng
        $office1 = Office::create([
            'name' => 'Văn phòng Hà Nội',
            'location' => 'Hà Nội',
        ]);

        $office2 = Office::create([
            'name' => 'Văn phòng Hồ Chí Minh',
            'location' => 'TP. Hồ Chí Minh',
        ]);

        // Tạo 4 phòng ban (2 phòng/văn phòng)
        $dept1 = Department::create([
            'name' => 'Phòng Kinh doanh HN',
            'office_id' => $office1->id,
        ]);

        $dept2 = Department::create([
            'name' => 'Phòng Kỹ thuật HN',
            'office_id' => $office1->id,
        ]);

        $dept3 = Department::create([
            'name' => 'Phòng Kinh doanh HCM',
            'office_id' => $office2->id,
        ]);

        $dept4 = Department::create([
            'name' => 'Phòng Kỹ thuật HCM',
            'office_id' => $office2->id,
        ]);

        $departments = [$dept1, $dept2, $dept3, $dept4];

        // Tạo 1 Giám đốc
        $ceo = User::create([
            'name' => 'Nguyễn Văn CEO',
            'email' => 'ceo@test.com',
            'password' => Hash::make('password'),
            'office_id' => null,
            'department_id' => null,
        ]);
        $ceo->assignRole('ceo');

        // Tạo 4 Kế toán (1 kế toán/văn phòng, nhưng tạo 2 cho mỗi văn phòng để test)
        $accountants = [];

        $acc1 = User::create([
            'name' => 'Kế toán HN 1',
            'email' => 'accountant.hn1@test.com',
            'password' => Hash::make('password'),
            'office_id' => $office1->id,
            'department_id' => null,
        ]);
        $acc1->assignRole('accountant');
        $accountants[] = $acc1;

        $acc2 = User::create([
            'name' => 'Kế toán HN 2',
            'email' => 'accountant.hn2@test.com',
            'password' => Hash::make('password'),
            'office_id' => $office1->id,
            'department_id' => null,
        ]);
        $acc2->assignRole('accountant');
        $accountants[] = $acc2;

        $acc3 = User::create([
            'name' => 'Kế toán HCM 1',
            'email' => 'accountant.hcm1@test.com',
            'password' => Hash::make('password'),
            'office_id' => $office2->id,
            'department_id' => null,
        ]);
        $acc3->assignRole('accountant');
        $accountants[] = $acc3;

        $acc4 = User::create([
            'name' => 'Kế toán HCM 2',
            'email' => 'accountant.hcm2@test.com',
            'password' => Hash::make('password'),
            'office_id' => $office2->id,
            'department_id' => null,
        ]);
        $acc4->assignRole('accountant');
        $accountants[] = $acc4;

        // Tạo 4 Trưởng phòng (1 trưởng phòng/phòng ban)
        $deptHeads = [];
        foreach ($departments as $index => $dept) {
            $head = User::create([
                'name' => "Trưởng phòng {$dept->name}",
                'email' => "head.dept{$index}@test.com",
                'password' => Hash::make('password'),
                'office_id' => $dept->office_id,
                'department_id' => $dept->id,
            ]);
            $head->assignRole('department_head');
            $deptHeads[$dept->id] = $head;
            $dept->update([
                'head_user_id' => $head->id,
            ]);
        }

        // Tạo 8 Nhân viên (2 nhân viên/phòng ban)
        $employees = [];
        foreach ($departments as $index => $dept) {
            for ($i = 1; $i <= 2; $i++) {
                $emp = User::create([
                    'name' => "Nhân viên {$dept->name} {$i}",
                    'email' => "employee.dept{$index}.{$i}@test.com",
                    'password' => Hash::make('password'),
                    'office_id' => $dept->office_id,
                    'department_id' => $dept->id,
                ]);
                $emp->assignRole('employee');
                $employees[] = $emp;
            }
        }

        $this->command->info('✅ Test data seeded successfully!');
        $this->command->info('📊 Summary:');
        $this->command->info("- Văn phòng: 2");
        $this->command->info("- Phòng ban: 4");
        $this->command->info("- Giám đốc: 1 (ceo@test.com)");
        $this->command->info("- Kế toán: 4 (2/văn phòng)");
        $this->command->info("- Trưởng phòng: 4 (1/phòng ban)");
        $this->command->info("- Nhân viên: 8 (2/phòng ban)");
        $this->command->info('🔑 All passwords: password');
    }
}
