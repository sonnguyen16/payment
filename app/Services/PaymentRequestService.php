<?php

namespace App\Services;

use App\Enums\PaymentRequestStatus;
use App\Events\PaymentRequestCancelled;
use App\Events\PaymentRequestCreated;
use App\Events\PaymentRequestSubmitted;
use App\Events\PaymentRequestUpdated;
use App\Models\PaymentRequest;
use Illuminate\Support\Facades\DB;

class PaymentRequestService
{
    public function create(array $data): PaymentRequest
    {
        return DB::transaction(function () use ($data) {
            $data['status'] = PaymentRequestStatus::DRAFT;
            $data['user_id'] = auth()->id();
            
            $paymentRequest = PaymentRequest::create($data);
            
            event(new PaymentRequestCreated($paymentRequest));
            
            return $paymentRequest;
        });
    }

    public function update(PaymentRequest $paymentRequest, array $data, string $reason): PaymentRequest
    {
        return DB::transaction(function () use ($paymentRequest, $data, $reason) {
            $changes = [];
            foreach ($data as $key => $value) {
                if ($paymentRequest->{$key} != $value) {
                    $changes[] = [
                        'field' => $key,
                        'old_value' => $paymentRequest->{$key},
                        'new_value' => $value,
                    ];
                }
            }
            
            $paymentRequest->update($data);
            
            event(new PaymentRequestUpdated($paymentRequest, $changes, $reason));
            
            return $paymentRequest;
        });
    }

    public function submit(PaymentRequest $paymentRequest): PaymentRequest
    {
        return DB::transaction(function () use ($paymentRequest) {
            $paymentRequest->update([
                'status' => PaymentRequestStatus::PENDING_DEPARTMENT_HEAD,
                'current_approver_id' => $this->getDepartmentHead($paymentRequest),
            ]);
            
            event(new PaymentRequestSubmitted($paymentRequest));
            
            return $paymentRequest;
        });
    }

    public function cancel(PaymentRequest $paymentRequest, string $reason): PaymentRequest
    {
        return DB::transaction(function () use ($paymentRequest, $reason) {
            $paymentRequest->update([
                'status' => PaymentRequestStatus::CANCELLED,
            ]);
            
            event(new PaymentRequestCancelled($paymentRequest, $reason));
            
            return $paymentRequest;
        });
    }

    public function delete(PaymentRequest $paymentRequest): bool
    {
        return DB::transaction(function () use ($paymentRequest) {
            $paymentRequest->update([
                'status' => PaymentRequestStatus::DELETED,
            ]);
            
            $paymentRequest->delete();
            
            return true;
        });
    }

    protected function getDepartmentHead(PaymentRequest $paymentRequest): ?int
    {
        return $paymentRequest->user->department?->head_user_id;
    }
}
