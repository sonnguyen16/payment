<?php

namespace Tests\Feature;

use App\Models\PaymentRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ApprovalWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create roles
        Role::create(['name' => 'employee']);
        Role::create(['name' => 'department_head']);
        Role::create(['name' => 'accountant']);
        Role::create(['name' => 'ceo']);
    }

    public function test_department_head_can_approve_request(): void
    {
        $manager = User::factory()->create();
        $manager->assignRole('department_head');
        
        $request = PaymentRequest::factory()->create([
            'status' => 'pending_department_head',
            'current_approver_id' => $manager->id,
        ]);

        $response = $this->actingAs($manager)->post(route('approvals.approve', $request));

        $response->assertRedirect();
        $this->assertDatabaseHas('payment_requests', [
            'id' => $request->id,
            'status' => 'pending_accountant',
        ]);
    }

    public function test_approver_can_reject_request(): void
    {
        $manager = User::factory()->create();
        $manager->assignRole('department_head');
        
        $request = PaymentRequest::factory()->create([
            'status' => 'pending_department_head',
            'current_approver_id' => $manager->id,
        ]);

        $response = $this->actingAs($manager)->post(route('approvals.reject', $request), [
            'reason' => 'Not approved',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('payment_requests', [
            'id' => $request->id,
            'status' => 'rejected',
        ]);
    }
}
