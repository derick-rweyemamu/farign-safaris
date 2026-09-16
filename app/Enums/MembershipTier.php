<?php

namespace App\Enums;

enum MembershipTier: string
{
    use HasOptions;

    case BRONZE = 'bronze';
    case SILVER = 'silver';
    case GOLD = 'gold';
    case PLATINUM = 'platinum';

    public function label(): string
    {
        return match ($this) {
            self::BRONZE => 'Bronze',
            self::SILVER => 'Silver',
            self::GOLD => 'Gold',
            self::PLATINUM => 'Platinum',
        };
    }
}
