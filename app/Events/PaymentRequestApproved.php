<?php

namespace App\Events;

use App\Models\PaymentRequest;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentRequestApproved
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public PaymentRequest $paymentRequest,
        public ?string $note = null
    ) {}
}
