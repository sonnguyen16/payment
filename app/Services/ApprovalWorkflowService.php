<?php

namespace App\Services;

use App\Enums\PaymentRequestStatus;
use App\Events\PaymentRequestApproved;
use App\Events\PaymentRequestRejected;
use App\Models\PaymentRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ApprovalWorkflowService
{
    public function approve(PaymentRequest $paymentRequest, ?string $note = null): PaymentRequest
    {
        return DB::transaction(function () use ($paymentRequest, $note) {
            $currentStatus = $paymentRequest->status;
            $nextStatus = $this->getNextStatus($currentStatus, $paymentRequest);
            
            if (!$currentStatus->canTransitionTo($nextStatus)) {
                throw new \Exception('Invalid status transition');
            }
            
            $nextApproverId = $this->getNextApprover($nextStatus, $paymentRequest);
            
            $updates = [
                'status' => $nextStatus,
                'current_approver_id' => $nextApproverId,
            ];
            
            // Validate that we found an approver when needed
            if (in_array($nextStatus, [
                PaymentRequestStatus::PENDING_DEPARTMENT_HEAD,
                PaymentRequestStatus::PENDING_ACCOUNTANT,
                PaymentRequestStatus::PENDING_CEO,
                PaymentRequestStatus::PENDING_PAYMENT
            ]) && !$nextApproverId) {
                $errorMessage = match($nextStatus) {
                    PaymentRequestStatus::PENDING_DEPARTMENT_HEAD => "No department head found for office: {$paymentRequest->user->office->name}",
                    PaymentRequestStatus::PENDING_ACCOUNTANT => "No accountant found for office: {$paymentRequest->user->office->name}",
                    PaymentRequestStatus::PENDING_CEO => "No CEO found in system",
                    PaymentRequestStatus::PENDING_PAYMENT => "No accountant found for payment processing in office: {$paymentRequest->user->office->name}",
                    default => "No approver found for status: {$nextStatus->value}"
                };
                throw new \Exception($errorMessage);
            }
            
            // If marking as paid, set paid_at and payment_code
            if ($nextStatus === PaymentRequestStatus::PAID) {
                $updates['paid_at'] = now();
                $updates['payment_code'] = 'PAY-' . $paymentRequest->id . '-' . time();
            }
            
            $paymentRequest->update($updates);
            
            // Update project spent amount when payment is processed
            if ($nextStatus === PaymentRequestStatus::PAID && $paymentRequest->project_id) {
                $paymentRequest->project->increment('spent', $paymentRequest->amount);
            }
            
            event(new PaymentRequestApproved($paymentRequest, $note));
            
            return $paymentRequest;
        });
    }

    public function reject(PaymentRequest $paymentRequest, string $reason): PaymentRequest
    {
        return DB::transaction(function () use ($paymentRequest, $reason) {
            $paymentRequest->update([
                'status' => PaymentRequestStatus::REJECTED,
                'rejection_reason' => $reason,
                'current_approver_id' => null,
            ]);
            
            event(new PaymentRequestRejected($paymentRequest, $reason));
            
            return $paymentRequest;
        });
    }

    public function canApprove(User $user, PaymentRequest $paymentRequest): bool
    {
        if ($paymentRequest->current_approver_id !== $user->id) {
            return false;
        }
        
        $requiredRole = $paymentRequest->status->nextApprover();
        
        return $user->hasRole($requiredRole);
    }

    public function canReject(User $user, PaymentRequest $paymentRequest): bool
    {
        return $this->canApprove($user, $paymentRequest);
    }

    protected function getNextStatus(PaymentRequestStatus $currentStatus, PaymentRequest $paymentRequest = null): PaymentRequestStatus
    {
        return match($currentStatus) {
            PaymentRequestStatus::PENDING_DEPARTMENT_HEAD => PaymentRequestStatus::PENDING_ACCOUNTANT,
            PaymentRequestStatus::PENDING_ACCOUNTANT => PaymentRequestStatus::PENDING_CEO,
            PaymentRequestStatus::PENDING_CEO => PaymentRequestStatus::PENDING_PAYMENT,
            PaymentRequestStatus::PENDING_PAYMENT => PaymentRequestStatus::PAID,
            default => throw new \Exception('Cannot determine next status'),
        };
    }

    protected function getNextApprover(PaymentRequestStatus $status, PaymentRequest $paymentRequest): ?int
    {
        return match($status) {
            PaymentRequestStatus::PENDING_ACCOUNTANT => $this->getAccountant($paymentRequest),
            PaymentRequestStatus::PENDING_CEO => $this->getCEO(),
            PaymentRequestStatus::PENDING_PAYMENT => $this->getAccountant($paymentRequest),
            default => null,
        };
    }

    protected function getAccountant(PaymentRequest $paymentRequest): ?int
    {
        $accountant = User::role('accountant')
            ->where('office_id', $paymentRequest->user->office_id)
            ->first();
            
        if (!$accountant) {
            \Log::warning('No accountant found for office', [
                'office_id' => $paymentRequest->user->office_id,
                'request_id' => $paymentRequest->id
            ]);
        }
        
        return $accountant?->id;
    }

    protected function getCEO(): ?int
    {
        return User::role('ceo')->first()?->id;
    }
}
