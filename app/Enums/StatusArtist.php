<?php

namespace App\Enums;

enum StatusArtist
{
    case available;
    case occupied;
    case inactive;


    /**
     * @return array
     */
    public static function toArray(): array
    {
        return array_map(
            fn($value) => $value->name,
            self::cases()
        );
    }
}
