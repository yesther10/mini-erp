<?php

namespace App\Enums;

enum AssetCategory: string
{
    case Notebook = 'notebook';
    case Desktop = 'desktop';
    case Smartphone = 'smartphone';
    case Printer = 'printer';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(static fn (self $category): string => $category->value, self::cases());
    }
}
