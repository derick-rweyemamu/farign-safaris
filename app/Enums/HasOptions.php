<?php

namespace App\Enums;

trait HasOptions
{
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn($case) => [
            $case->value => method_exists($case, 'label') ? $case->label() : ucfirst($case->value)
        ])->toArray();
    }

    public static function values(): array
    {
        return collect(self::cases())->pluck('value')->toArray();
    }

    /**
     * Safely parse a value into an Enum, with an optional default.
     */
    public static function fromValue(?string $value, ?self $default = null): ?self
    {
        if (empty($value)) return $default;
        return self::tryFrom($value) ?? $default;
    }
}
