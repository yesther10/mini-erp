<?php

namespace App\Enums;

enum AssetStatus: string
{
    case Available = 'available';
    case Allocated = 'allocated';
    case Maintenance = 'maintenance';
    case Retired = 'retired';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(static fn (self $status): string => $status->value, self::cases());
    }
}
