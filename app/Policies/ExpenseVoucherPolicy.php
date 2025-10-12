<?php

namespace App\Policies;

use App\Models\ExpenseVoucher;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ExpenseVoucherPolicy
{
    /**
     * Determine whether the user can view any models.
     * Everyone except admin can view expense vouchers
     */
    public function viewAny(User $user): bool
    {
        return !$user->hasRole('admin');
    }

    /**
     * Determine whether the user can view the model.
     * Everyone can view
     */
    public function view(User $user, ExpenseVoucher $expenseVoucher): bool
    {
        return !$user->hasRole('admin');
    }

    /**
     * Determine whether the user can create models.
     * Everyone except admin can create
     */
    public function create(User $user): bool
    {
        return !$user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the model.
     * Only creator can update
     */
    public function update(User $user, ExpenseVoucher $expenseVoucher): bool
    {
        return $user->id === $expenseVoucher->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     * Only creator can delete
     */
    public function delete(User $user, ExpenseVoucher $expenseVoucher): bool
    {
        return $user->id === $expenseVoucher->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ExpenseVoucher $expenseVoucher): bool
    {
        return $user->id === $expenseVoucher->user_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ExpenseVoucher $expenseVoucher): bool
    {
        return $user->id === $expenseVoucher->user_id;
    }
}
