<?php

namespace App\Services;

use App\Models\ExpenseVoucher;
use Illuminate\Support\Facades\DB;

class ExpenseVoucherService
{
    public function create(array $data): ExpenseVoucher
    {
        return DB::transaction(function () use ($data) {
            $data['user_id'] = auth()->id();
            
            return ExpenseVoucher::create($data);
        });
    }

    public function update(ExpenseVoucher $expenseVoucher, array $data, string $reason): ExpenseVoucher
    {
        return DB::transaction(function () use ($expenseVoucher, $data, $reason) {
            // Track changes
            $changes = [];
            foreach ($data as $key => $value) {
                if ($expenseVoucher->{$key} != $value) {
                    $changes[$key] = [
                        'old' => $this->formatChangeValue($key, $expenseVoucher->{$key}),
                        'new' => $this->formatChangeValue($key, $value),
                    ];
                }
            }
            
            $expenseVoucher->update($data);
            
            // Save to update_histories
            if (!empty($changes)) {
                $expenseVoucher->updateHistories()->create([
                    'user_id' => auth()->id(),
                    'reason' => $reason,
                    'changes' => json_encode($changes),
                ]);
            }
            
            return $expenseVoucher;
        });
    }

    public function delete(ExpenseVoucher $expenseVoucher): bool
    {
        return DB::transaction(function () use ($expenseVoucher) {
            return $expenseVoucher->delete();
        });
    }

    protected function formatChangeValue(string $field, $value): string
    {
        if ($value === null) {
            return 'Trống';
        }
        
        // Format expense_category_id
        if ($field === 'expense_category_id' && $value) {
            $category = \App\Models\ExpenseCategory::find($value);
            return $category ? $category->name : $value;
        }
        
        // Format project_id
        if ($field === 'project_id' && $value) {
            $project = \App\Models\Project::find($value);
            return $project ? $project->name : $value;
        }
        
        // Format date
        if ($field === 'expense_date' && $value) {
            return \Carbon\Carbon::parse($value)->format('d/m/Y');
        }
        
        // Format money
        if ($field === 'amount' && $value) {
            return number_format($value, 0, ',', '.') . ' ₫';
        }
        
        return (string) $value;
    }
}
