<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpenseVoucherUpdateHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'expense_voucher_id',
        'user_id',
        'reason',
        'changes',
    ];

    protected $casts = [
        'changes' => 'array',
    ];

    /**
     * Get the expense voucher.
     */
    public function expenseVoucher(): BelongsTo
    {
        return $this->belongsTo(ExpenseVoucher::class);
    }

    /**
     * Get the user who made the update.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
