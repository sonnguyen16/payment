<?php

namespace App\Enums;

enum PaymentRequestType: string
{
    case ADVANCE = 'advance';
    case PAYMENT_PROPOSAL = 'payment_proposal';
    case OTHER_EXPENSE = 'other_expense';
    
    public function label(): string
    {
        return match($this) {
            self::ADVANCE => 'Tạm ứng',
            self::PAYMENT_PROPOSAL => 'Đề xuất thanh toán',
            self::OTHER_EXPENSE => 'Chi phí khác',
        };
    }
}
