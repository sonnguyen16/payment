<?php

namespace App\Enums;

enum Priority: string
{
    case URGENT = 'urgent';
    case NORMAL = 'normal';
    
    public function label(): string
    {
        return match($this) {
            self::URGENT => 'Gấp',
            self::NORMAL => 'Bình thường',
        };
    }
    
    public function badgeClass(): string
    {
        return match($this) {
            self::URGENT => 'badge-danger',
            self::NORMAL => 'badge-secondary',
        };
    }
}
