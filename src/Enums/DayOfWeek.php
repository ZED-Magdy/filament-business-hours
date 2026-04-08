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

    /**
     * Whether this day falls on the weekend (Saturday or Sunday).
     */
    public function isWeekend(): bool
    {
        return match ($this) {
            self::Saturday, self::Sunday => true,
            default => false,
        };
    }

    /**
     * Whether this day falls on a weekday (Monday through Friday).
     */
    public function isWeekday(): bool
    {
        return ! $this->isWeekend();
    }

    /**
     * ISO-8601 numeric representation of the day (Monday = 1, Sunday = 7).
     */
    public function toIsoNumeric(): int
    {
        return match ($this) {
            self::Monday => 1,
            self::Tuesday => 2,
            self::Wednesday => 3,
            self::Thursday => 4,
            self::Friday => 5,
            self::Saturday => 6,
            self::Sunday => 7,
        };
    }
}
