<?php

namespace App\Enums;

class OrderStatusClassification
{
    /**
     * Status classifications mapping.
     * Each classification contains an array of statuses.
     */
    public const CLASSIFICATIONS = [
        'processing' => ['pending', 'assigned', 'worker_on_way', 'in_progress'],
        'finished' => ['completed', 'cancelled'],
    ];

    /**
     * All valid individual statuses.
     */
    public const STATUSES = [
        'pending',
        'assigned',
        'worker_on_way',
        'in_progress',
        'completed',
        'cancelled',
    ];

    /**
     * Get statuses for a given classification.
     * 
     * @param string $classification
     * @return array|null
     */
    public static function getStatuses(string $classification): ?array
    {
        return self::CLASSIFICATIONS[$classification] ?? null;
    }

    /**
     * Check if a classification exists.
     * 
     * @param string $classification
     * @return bool
     */
    public static function isValidClassification(string $classification): bool
    {
        return array_key_exists($classification, self::CLASSIFICATIONS);
    }

    /**
     * Check if a status is valid.
     * 
     * @param string $status
     * @return bool
     */
    public static function isValidStatus(string $status): bool
    {
        return in_array($status, self::STATUSES);
    }

    /**
     * Get all valid values (classifications + statuses).
     * 
     * @return array
     */
    public static function getAllValidValues(): array
    {
        return array_merge(
            array_keys(self::CLASSIFICATIONS),
            self::STATUSES
        );
    }

    /**
     * Get validation rule string for Laravel.
     * 
     * @return string
     */
    public static function getValidationRule(): string
    {
        return 'in:' . implode(',', self::getAllValidValues());
    }
}
