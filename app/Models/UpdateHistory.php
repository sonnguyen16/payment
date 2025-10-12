<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UpdateHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_request_id',
        'user_id',
        'reason',
        'changes',
    ];

    protected $casts = [
        'changes' => 'array',
    ];

    public function paymentRequest(): BelongsTo
    {
        return $this->belongsTo(PaymentRequest::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
