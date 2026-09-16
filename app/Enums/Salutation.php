<?php

namespace App\Enums;

enum Salutation: string
{
    use HasOptions;

    case MR = 'Mr.';
    case MS = 'Ms.';
    case MRS = 'Mrs.';
    case MISS = 'Miss.';

    public function label(): string
    {
        return match ($this) {
            self::MR => 'Mr.',
            self::MS => 'Ms.',
            self::MRS => 'Mrs.',
            self::MISS => 'Miss.',
        };
    }
}
