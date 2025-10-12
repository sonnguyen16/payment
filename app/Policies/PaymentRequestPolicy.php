<?php

namespace App\Policies;

use App\Models\PaymentRequest;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PaymentRequestPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PaymentRequest $paymentRequest): bool
    {
        // Owner can always view
        if ($user->id === $paymentRequest->user_id) {
            return true;
        }

        // CEO can view all
        if ($user->hasRole('ceo')) {
            return true;
        }

        // Accountant can view requests from their office
        if ($user->hasRole('accountant')) {
            return $paymentRequest->user->office_id === $user->office_id;
        }

        // Department head can view requests from their department
        if ($user->hasRole('department_head')) {
            return $paymentRequest->user->department_id === $user->department_id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_payment_request');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PaymentRequest $paymentRequest): bool
    {
        // Only creator can update, and only if draft or rejected
        return $user->id === $paymentRequest->user_id
            && in_array($paymentRequest->status->value, ['draft', 'rejected', 'pending_department_head']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PaymentRequest $paymentRequest): bool
    {
        // Approvers can delete
        if ($user->hasPermissionTo('delete_payment_request')) {
            return true;
        }

        // Creator can cancel (not delete)
        return false;
    }

    public function cancel(User $user, PaymentRequest $paymentRequest): bool
    {
        // Only creator can cancel, not if already cancelled, paid or deleted
        return $user->id === $paymentRequest->user_id
            && !in_array($paymentRequest->status->value, ['cancelled', 'paid', 'deleted']);
    }

    public function approve(User $user, PaymentRequest $paymentRequest): bool
    {
        // Không thể approve phiếu đã cancelled, rejected, paid, hoặc deleted
        if (in_array($paymentRequest->status->value, ['cancelled', 'rejected', 'paid', 'deleted'])) {
            return false;
        }

        return $user->hasPermissionTo('approve_payment_request')
            && $user->id === $paymentRequest->current_approver_id;
    }

    public function reject(User $user, PaymentRequest $paymentRequest): bool
    {
        // Không thể reject phiếu đã cancelled, rejected, paid, hoặc deleted
        if (in_array($paymentRequest->status->value, ['cancelled', 'rejected', 'paid', 'deleted'])) {
            return false;
        }

        return $user->hasPermissionTo('approve_payment_request')
            && $user->id === $paymentRequest->current_approver_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PaymentRequest $paymentRequest): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PaymentRequest $paymentRequest): bool
    {
        return false;
    }
    public function uploadDocument(User $user, PaymentRequest $paymentRequest): bool
    {
        // Owner can upload documents at any time
        // After paid, owner should upload invoices/receipts
        if ($paymentRequest->user_id === $user->id) {
            return true;
        }

        return false;
    }
}
