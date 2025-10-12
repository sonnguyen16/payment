<?php

namespace App\Services;

use App\Enums\PaymentRequestStatus;
use App\Events\PaymentRequestCancelled;
use App\Events\PaymentRequestCreated;
use App\Events\PaymentRequestSubmitted;
use App\Events\PaymentRequestUpdated;
use App\Models\PaymentRequest;
use Illuminate\Support\Facades\DB;

class PaymentRequestService
{
    public function create(array $data): PaymentRequest
    {
        return DB::transaction(function () use ($data) {
            // Extract details before creating payment request
            $details = $data['details'];
            unset($data['details']);
            
            // Calculate total amount from details
            $totalAmount = collect($details)->sum('total_amount');
            $data['amount'] = $totalAmount;
            $data['description'] = collect($details)->pluck('description')->join('; ');
            
            $data['status'] = PaymentRequestStatus::DRAFT;
            $data['user_id'] = auth()->id();
            
            $paymentRequest = PaymentRequest::create($data);
            
            // Create details
            foreach ($details as $detail) {
                $paymentRequest->details()->create($detail);
            }
            
            event(new PaymentRequestCreated($paymentRequest));
            
            return $paymentRequest;
        });
    }

    public function update(PaymentRequest $paymentRequest, array $data, string $reason): PaymentRequest
    {
        return DB::transaction(function () use ($paymentRequest, $data, $reason) {
            // Load old details before updating
            $oldDetails = $paymentRequest->details()->get()->toArray();
            
            // Extract details before updating payment request
            $details = $data['details'];
            unset($data['details']);
            
            // Calculate total amount from details
            $totalAmount = collect($details)->sum('total_amount');
            $data['amount'] = $totalAmount;
            $data['description'] = collect($details)->pluck('description')->join('; ');
            
            // Track changes for main fields
            $changes = [];
            foreach ($data as $key => $value) {
                if ($paymentRequest->{$key} != $value) {
                    $changes[$key] = [
                        'old' => $this->formatChangeValue($key, $paymentRequest->{$key}),
                        'new' => $this->formatChangeValue($key, $value),
                    ];
                }
            }
            
            // Track details changes
            $detailsChanges = $this->compareDetails($oldDetails, $details);
            if (!empty($detailsChanges)) {
                $changes['details'] = ['details' => $detailsChanges];
            }
            
            $paymentRequest->update($data);
            
            // Update details - delete old ones and create new ones
            $paymentRequest->details()->delete();
            foreach ($details as $detail) {
                $paymentRequest->details()->create($detail);
            }
            
            // Save to update_histories
            if (!empty($changes)) {
                $paymentRequest->updateHistories()->create([
                    'user_id' => auth()->id(),
                    'reason' => $reason,
                    'changes' => json_encode($changes),
                ]);
            }
            
            event(new PaymentRequestUpdated($paymentRequest, $changes, $reason));
            
            return $paymentRequest;
        });
    }
    
    protected function compareDetails(array $oldDetails, array $newDetails): array
    {
        $changes = [
            'added' => [],
            'removed' => [],
            'modified' => [],
        ];
        
        // Find added and modified
        foreach ($newDetails as $index => $newDetail) {
            $oldDetail = $oldDetails[$index] ?? null;
            
            if (!$oldDetail) {
                // New detail added
                $changes['added'][] = $newDetail;
            } else {
                // Check if modified
                $isModified = false;
                $modifications = [
                    'old' => [],
                    'new' => [],
                ];
                
                foreach (['description', 'amount_before_tax', 'tax_amount', 'total_amount', 'invoice_number'] as $field) {
                    if ($oldDetail[$field] != $newDetail[$field]) {
                        $isModified = true;
                        $modifications['old'][$field] = $oldDetail[$field];
                        $modifications['new'][$field] = $newDetail[$field];
                    } else {
                        $modifications['old'][$field] = $oldDetail[$field];
                        $modifications['new'][$field] = $newDetail[$field];
                    }
                }
                
                if ($isModified) {
                    $changes['modified'][] = $modifications;
                }
            }
        }
        
        // Find removed
        if (count($oldDetails) > count($newDetails)) {
            for ($i = count($newDetails); $i < count($oldDetails); $i++) {
                $changes['removed'][] = $oldDetails[$i];
            }
        }
        
        // Remove empty arrays
        $changes = array_filter($changes, function($value) {
            return !empty($value);
        });
        
        return $changes;
    }
    
    protected function formatChangeValue(string $field, $value): string
    {
        if ($value === null) {
            return 'Trống';
        }
        
        // Format category_id
        if ($field === 'category_id' && $value) {
            $category = \App\Models\Category::find($value);
            return $category ? $category->name : $value;
        }
        
        // Format project_id
        if ($field === 'project_id' && $value) {
            $project = \App\Models\Project::find($value);
            return $project ? $project->name : $value;
        }
        
        // Format priority
        if ($field === 'priority') {
            return $value === 'urgent' ? 'Gấp' : 'Bình thường';
        }
        
        // Format date
        if ($field === 'expected_date' && $value) {
            return \Carbon\Carbon::parse($value)->format('d/m/Y');
        }
        
        // Format money
        if ($field === 'amount' && $value) {
            return number_format($value, 0, ',', '.') . ' ₫';
        }
        
        return (string) $value;
    }

    public function submit(PaymentRequest $paymentRequest): PaymentRequest
    {
        return DB::transaction(function () use ($paymentRequest) {
            $paymentRequest->update([
                'status' => PaymentRequestStatus::PENDING_DEPARTMENT_HEAD,
                'current_approver_id' => $this->getDepartmentHead($paymentRequest),
            ]);
            
            event(new PaymentRequestSubmitted($paymentRequest));
            
            return $paymentRequest;
        });
    }

    public function cancel(PaymentRequest $paymentRequest, string $reason): PaymentRequest
    {
        return DB::transaction(function () use ($paymentRequest, $reason) {
            $paymentRequest->update([
                'status' => PaymentRequestStatus::CANCELLED,
            ]);
            
            event(new PaymentRequestCancelled($paymentRequest, $reason));
            
            return $paymentRequest;
        });
    }

    public function delete(PaymentRequest $paymentRequest): bool
    {
        return DB::transaction(function () use ($paymentRequest) {
            $paymentRequest->update([
                'status' => PaymentRequestStatus::DELETED,
            ]);
            
            $paymentRequest->delete();
            
            return true;
        });
    }

    protected function getDepartmentHead(PaymentRequest $paymentRequest): ?int
    {
        return $paymentRequest->user->department?->head_user_id;
    }
}
