<?php

namespace App\Listeners;

use App\Enums\ApprovalAction;
use App\Events\PaymentRequestApproved;
use App\Events\PaymentRequestCancelled;
use App\Events\PaymentRequestCreated;
use App\Events\PaymentRequestRejected;
use App\Events\PaymentRequestSubmitted;
use App\Events\PaymentRequestUpdated;
use App\Models\ApprovalHistory;

class LogApprovalHistory
{
    public function handleCreated(PaymentRequestCreated $event): void
    {
        ApprovalHistory::create([
            'payment_request_id' => $event->paymentRequest->id,
            'user_id' => auth()->id(),
            'action' => ApprovalAction::CREATED,
            'to_status' => $event->paymentRequest->status->value,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);
    }

    public function handleUpdated(PaymentRequestUpdated $event): void
    {
        ApprovalHistory::create([
            'payment_request_id' => $event->paymentRequest->id,
            'user_id' => auth()->id(),
            'action' => ApprovalAction::UPDATED,
            'reason' => $event->reason,
            'changes' => $event->changes,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);
    }

    public function handleSubmitted(PaymentRequestSubmitted $event): void
    {
        ApprovalHistory::create([
            'payment_request_id' => $event->paymentRequest->id,
            'user_id' => auth()->id(),
            'action' => ApprovalAction::SUBMITTED,
            'from_status' => 'draft',
            'to_status' => $event->paymentRequest->status->value,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);
    }

    public function handleApproved(PaymentRequestApproved $event): void
    {
        ApprovalHistory::create([
            'payment_request_id' => $event->paymentRequest->id,
            'user_id' => auth()->id(),
            'action' => ApprovalAction::APPROVED,
            'to_status' => $event->paymentRequest->status->value,
            'reason' => $event->note,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);
    }

    public function handleRejected(PaymentRequestRejected $event): void
    {
        ApprovalHistory::create([
            'payment_request_id' => $event->paymentRequest->id,
            'user_id' => auth()->id(),
            'action' => ApprovalAction::REJECTED,
            'to_status' => $event->paymentRequest->status->value,
            'reason' => $event->reason,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);
    }

    public function handleCancelled(PaymentRequestCancelled $event): void
    {
        ApprovalHistory::create([
            'payment_request_id' => $event->paymentRequest->id,
            'user_id' => auth()->id(),
            'action' => ApprovalAction::CANCELLED,
            'to_status' => $event->paymentRequest->status->value,
            'reason' => $event->reason,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);
    }
}
