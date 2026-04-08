<?php

declare(strict_types=1);

namespace ZEDMagdy\FilamentBusinessHours;

class FilamentBusinessHours
{
    /** @var array<string, mixed>|null */
    private static ?array $config = null;

    /**
     * Return the full package config, cached after the first read.
     *
     * @return array<string, mixed>
     */
    private static function config(): array
    {
        return self::$config ??= config('filament-business-hours', []);
    }

    /**
     * Flush the cached config. Useful in tests when the config is swapped.
     */
    public static function flushCache(): void
    {
        self::$config = null;
    }

    public static function getHoursColumn(): string
    {
        return self::config()['columns']['hours'] ?? 'business_hours';
    }

    public static function getExceptionsColumn(): string
    {
        return self::config()['columns']['exceptions'] ?? 'business_hours_exceptions';
    }

    public static function getTimezoneColumn(): string
    {
        return self::config()['columns']['timezone'] ?? 'business_hours_timezone';
    }

    /** @return array<string, array<string>> */
    public static function getDefaultHours(): array
    {
        return self::config()['defaults'] ?? [];
    }

    public static function getTimeFormat(): string
    {
        return self::config()['display']['time_format'] ?? 'H:i';
    }

    public static function getClosedLabel(): string
    {
        return self::config()['display']['closed_label'] ?? 'Closed';
    }

    public static function getOpenLabel(): string
    {
        return self::config()['display']['open_label'] ?? 'Open';
    }
}
