<?php

namespace Tests\Feature;

use App\Models\PaymentRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_payment_request(): void
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->post(route('payment-requests.store'), [
            'type' => 'advance',
            'amount' => 1000000,
            'description' => 'Test payment request',
            'reason' => 'Test reason',
            'expected_date' => now()->addDays(7)->format('Y-m-d'),
            'priority' => 'normal',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('payment_requests', [
            'user_id' => $user->id,
            'amount' => 1000000,
        ]);
    }

    public function test_user_can_view_own_payment_requests(): void
    {
        $user = User::factory()->create();
        $request = PaymentRequest::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('payment-requests.show', $request));

        $response->assertOk();
    }

    public function test_user_can_submit_draft_request(): void
    {
        $user = User::factory()->create();
        $request = PaymentRequest::factory()->create([
            'user_id' => $user->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($user)->post(route('payment-requests.submit', $request));

        $response->assertRedirect();
        $this->assertDatabaseHas('payment_requests', [
            'id' => $request->id,
            'status' => 'pending_department_head',
        ]);
    }
}
