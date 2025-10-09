<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'budget',
        'spent',
        'status',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'budget' => 'decimal:2',
        'spent' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function paymentRequests(): HasMany
    {
        return $this->hasMany(PaymentRequest::class);
    }

    // Accessors
    public function getRemainingBudgetAttribute(): float
    {
        return $this->budget - $this->spent;
    }

    public function getBudgetUtilizationPercentageAttribute(): float
    {
        if ($this->budget == 0) return 0;
        return ($this->spent / $this->budget) * 100;
    }

    public function getIsOverBudgetAttribute(): bool
    {
        return $this->spent > $this->budget;
    }

    // Scopes
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }
}
