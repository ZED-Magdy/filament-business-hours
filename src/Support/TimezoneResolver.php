<?php

declare(strict_types=1);

namespace ZEDMagdy\FilamentBusinessHours\Support;

class TimezoneResolver
{
    /** @var array<string>|null */
    private static ?array $validTimezones = null;

    /**
     * Resolve a timezone string, falling back to config and then app timezone.
     */
    public static function resolve(?string $timezone): string
    {
        return $timezone
            ?? config('filament-business-hours.timezone')
            ?? config('app.timezone', 'UTC');
    }

    /**
     * Determine whether a timezone string is a valid PHP timezone identifier.
     */
    public static function isValid(string $timezone): bool
    {
        return in_array($timezone, self::all(), strict: true);
    }

    /**
     * Return all valid timezone identifiers (cached after first call).
     *
     * @return array<string>
     */
    public static function all(): array
    {
        return self::$validTimezones ??= timezone_identifiers_list();
    }
}
