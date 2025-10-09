<?php

namespace App\Enums;

enum PaymentRequestStatus: string
{
    case DRAFT = 'draft';
    case PENDING_DEPARTMENT_HEAD = 'pending_department_head';
    case PENDING_ACCOUNTANT = 'pending_accountant';
    case PENDING_CEO = 'pending_ceo';
    case PENDING_PAYMENT = 'pending_payment';
    case PAID = 'paid';
    case REJECTED = 'rejected';
    case CANCELLED = 'cancelled';
    case DELETED = 'deleted';
    
    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'Nháp',
            self::PENDING_DEPARTMENT_HEAD => 'Chờ Trưởng bộ phận',
            self::PENDING_ACCOUNTANT => 'Chờ Kế toán',
            self::PENDING_CEO => 'Chờ Tổng giám đốc',
            self::PENDING_PAYMENT => 'Chờ thanh toán',
            self::PAID => 'Đã thanh toán',
            self::REJECTED => 'Bị từ chối',
            self::CANCELLED => 'Đã hủy',
            self::DELETED => 'Đã xóa',
        };
    }
    
    public function canTransitionTo(self $newStatus): bool
    {
        return match($this) {
            self::DRAFT => in_array($newStatus, [
                self::PENDING_DEPARTMENT_HEAD,
                self::CANCELLED
            ]),
            self::PENDING_DEPARTMENT_HEAD => in_array($newStatus, [
                self::PENDING_ACCOUNTANT,
                self::REJECTED,
                self::DELETED
            ]),
            self::PENDING_ACCOUNTANT => in_array($newStatus, [
                self::PENDING_CEO,
                self::PENDING_PAYMENT, // For amounts < 50M, skip CEO
                self::REJECTED,
                self::DELETED
            ]),
            self::PENDING_CEO => in_array($newStatus, [
                self::PENDING_PAYMENT,
                self::REJECTED,
                self::DELETED
            ]),
            self::PENDING_PAYMENT => in_array($newStatus, [
                self::PAID
            ]),
            self::REJECTED => in_array($newStatus, [
                self::PENDING_DEPARTMENT_HEAD, // Can resubmit after editing
                self::CANCELLED,
                self::DELETED
            ]),
            default => false,
        };
    }
    
    public function nextApprover(): ?string
    {
        return match($this) {
            self::PENDING_DEPARTMENT_HEAD => 'department_head',
            self::PENDING_ACCOUNTANT => 'accountant',
            self::PENDING_CEO => 'ceo',
            self::PENDING_PAYMENT => 'accountant', // Accountant handles payment
            default => null,
        };
    }
}
