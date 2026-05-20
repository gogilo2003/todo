<?php

namespace App\Constants;

/**
 * Task status constants - PHP 8.0 compatible class.
 */
abstract class TaskStatus
{
    public const PENDING = 'pending';
    public const COMPLETED = 'completed';

    /**
     * Get all status values as array.
     */
    public static function values(): array
    {
        return [
            self::PENDING,
            self::COMPLETED,
        ];
    }

    /**
     * Get display label for status.
     */
    public static function label(string $status): string
    {
        return match ($status) {
            self::PENDING => 'Pending',
            self::COMPLETED => 'Completed',
            default => 'Pending',
        };
    }

    /**
     * Check if status is valid.
     */
    public static function isValid(string $status): bool
    {
        return in_array($status, self::values(), true);
    }
}
