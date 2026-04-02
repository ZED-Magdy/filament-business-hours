<?php

declare(strict_types=1);

namespace ZEDMagdy\FilamentBusinessHours\Enums;

enum DayOfWeek: string
{
    case Monday = 'monday';
    case Tuesday = 'tuesday';
    case Wednesday = 'wednesday';
    case Thursday = 'thursday';
    case Friday = 'friday';
    case Saturday = 'saturday';
    case Sunday = 'sunday';

    public function label(): string
    {
        return match ($this) {
            self::Monday => __('Monday'),
            self::Tuesday => __('Tuesday'),
            self::Wednesday => __('Wednesday'),
            self::Thursday => __('Thursday'),
            self::Friday => __('Friday'),
            self::Saturday => __('Saturday'),
            self::Sunday => __('Sunday'),
        };
    }

    public function shortLabel(): string
    {
        return match ($this) {
            self::Monday => __('Mon'),
            self::Tuesday => __('Tue'),
            self::Wednesday => __('Wed'),
            self::Thursday => __('Thu'),
            self::Friday => __('Fri'),
            self::Saturday => __('Sat'),
            self::Sunday => __('Sun'),
        };
    }
}
