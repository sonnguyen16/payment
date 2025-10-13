<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExpenseVoucher extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'expense_date',
        'description',
        'amount',
        'expense_category_id',
        'project_id',
        'user_id',
        'recipient',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2',
    ];

    /**
     * Get the user that created the voucher.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category.
     */
    public function expenseCategory(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class);
    }

    /**
     * Get the project.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the update histories.
     */
    public function updateHistories(): HasMany
    {
        return $this->hasMany(ExpenseVoucherUpdateHistory::class)->latest();
    }
}
