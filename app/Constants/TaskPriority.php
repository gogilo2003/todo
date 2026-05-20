<?php

namespace App\Constants;

/**
 * Task priority constants - PHP 8.0 compatible class.
 */
abstract class TaskPriority
{
    public const LOW = 'low';
    public const MEDIUM = 'medium';
    public const HIGH = 'high';

    /**
     * Get all priority values as array.
     */
    public static function values(): array
    {
        return [
            self::LOW,
            self::MEDIUM,
            self::HIGH,
        ];
    }

    /**
     * Get display label for priority.
     */
    public static function label(string $priority): string
    {
        return match ($priority) {
            self::LOW => 'Low',
            self::MEDIUM => 'Medium',
            self::HIGH => 'High',
            default => 'Medium',
        };
    }

    /**
     * Check if priority is valid.
     */
    public static function isValid(string $priority): bool
    {
        return in_array($priority, self::values(), true);
    }
}
