<?php

namespace App\Notifications;

use App\Models\PaymentRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public PaymentRequest $paymentRequest,
        public string $action,
        public ?string $message = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'payment_request_id' => $this->paymentRequest->id,
            'action' => $this->action,
            'message' => $this->message ?? $this->getDefaultMessage(),
            'amount' => $this->paymentRequest->amount,
            'user_name' => $this->paymentRequest->user->name,
            'url' => route('payment-requests.show', $this->paymentRequest->id),
        ];
    }

    private function getDefaultMessage(): string
    {
        return match($this->action) {
            'submitted' => "Phiếu #{$this->paymentRequest->id} đã được gửi duyệt",
            'approved' => "Phiếu #{$this->paymentRequest->id} đã được phê duyệt",
            'rejected' => "Phiếu #{$this->paymentRequest->id} đã bị từ chối",
            'cancelled' => "Phiếu #{$this->paymentRequest->id} đã bị hủy",
            default => "Phiếu #{$this->paymentRequest->id} có cập nhật mới",
        };
    }
}
