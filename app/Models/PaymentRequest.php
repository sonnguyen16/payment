<?php

namespace App\Models;

use App\Enums\PaymentRequestStatus;
use App\Enums\PaymentRequestType;
use App\Enums\Priority;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class PaymentRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'type',
        'category_id',
        'amount',
        'description',
        'reason',
        'expected_date',
        'priority',
        'status',
        'project_id',
        'current_approver_id',
        'rejection_reason',
        'payment_code',
        'paid_at',
    ];

    protected $casts = [
        'type' => PaymentRequestType::class,
        'status' => PaymentRequestStatus::class,
        'priority' => Priority::class,
        'expected_date' => 'date',
        'paid_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function currentApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'current_approver_id');
    }

    public function approvalHistories(): HasMany
    {
        return $this->hasMany(ApprovalHistory::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // Scopes
    public function scopePending(Builder $query): Builder
    {
        return $query->whereIn('status', [
            PaymentRequestStatus::PENDING_DEPARTMENT_HEAD,
            PaymentRequestStatus::PENDING_ACCOUNTANT,
            PaymentRequestStatus::PENDING_CEO,
            PaymentRequestStatus::PENDING_PAYMENT,
        ]);
    }

    public function scopeByStatus(Builder $query, PaymentRequestStatus $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeByPriority(Builder $query, Priority $priority): Builder
    {
        return $query->where('priority', $priority);
    }

    public function scopeForUser(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }

    public function scopeForApprover(Builder $query, User $user): Builder
    {
        return $query->where('current_approver_id', $user->id);
    }
}
