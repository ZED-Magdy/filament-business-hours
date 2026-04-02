<?php

declare(strict_types=1);

namespace ZEDMagdy\FilamentBusinessHours;

class FilamentBusinessHours
{
    public static function getHoursColumn(): string
    {
        return config('filament-business-hours.columns.hours', 'business_hours');
    }

    public static function getExceptionsColumn(): string
    {
        return config('filament-business-hours.columns.exceptions', 'business_hours_exceptions');
    }

    public static function getTimezoneColumn(): string
    {
        return config('filament-business-hours.columns.timezone', 'business_hours_timezone');
    }

    /** @return array<string, array<string>> */
    public static function getDefaultHours(): array
    {
        return config('filament-business-hours.defaults', []);
    }

    public static function getTimeFormat(): string
    {
        return config('filament-business-hours.display.time_format', 'H:i');
    }

    public static function getClosedLabel(): string
    {
        return config('filament-business-hours.display.closed_label', 'Closed');
    }

    public static function getOpenLabel(): string
    {
        return config('filament-business-hours.display.open_label', 'Open');
    }
}
