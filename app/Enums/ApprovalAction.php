<?php

namespace App\Enums;

enum ApprovalAction: string
{
    case CREATED = 'created';
    case SUBMITTED = 'submitted';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case CANCELLED = 'cancelled';
    case DELETED = 'deleted';
    case UPDATED = 'updated';
    case PAYMENT_PROCESSED = 'payment_processed';
    
    public function label(): string
    {
        return match($this) {
            self::CREATED => 'Tạo phiếu',
            self::SUBMITTED => 'Gửi duyệt',
            self::APPROVED => 'Phê duyệt',
            self::REJECTED => 'Từ chối',
            self::CANCELLED => 'Hủy',
            self::DELETED => 'Xóa',
            self::UPDATED => 'Chỉnh sửa',
            self::PAYMENT_PROCESSED => 'Đã thanh toán',
        };
    }
}
