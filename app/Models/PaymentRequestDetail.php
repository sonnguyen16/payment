<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentRequestDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_request_id',
        'description',
        'amount_before_tax',
        'tax_amount',
        'total_amount',
        'invoice_number',
    ];

    protected $casts = [
        'amount_before_tax' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function paymentRequest(): BelongsTo
    {
        return $this->belongsTo(PaymentRequest::class);
    }
}
