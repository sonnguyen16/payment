<?php

namespace App\Events;

use App\Models\PaymentRequest;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentRequestCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public PaymentRequest $paymentRequest
    ) {}
}
