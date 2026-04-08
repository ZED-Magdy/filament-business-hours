<?php

declare(strict_types=1);

namespace ZEDMagdy\FilamentBusinessHours\Support;

use Spatie\OpeningHours\OpeningHours;
use ZEDMagdy\FilamentBusinessHours\Enums\DayOfWeek;

class ScheduleFormatter
{
    /**
     * Format an OpeningHours instance into a day-keyed array of time-range strings.
     *
     * @return array<string, array<string>>
     */
    public static function format(OpeningHours $openingHours): array
    {
        $schedule = [];

        foreach (DayOfWeek::cases() as $day) {
            $ranges = [];

            foreach ($openingHours->forDay($day->value) as $range) {
                $ranges[] = (string) $range;
            }

            $schedule[$day->value] = $ranges;
        }

        return $schedule;
    }

    /**
     * Build an OpeningHours instance from a state array (as stored in the
     * model's JSON column or returned by the form field).
     *
     * Returns null when the state is empty or invalid.
     *
     * @param  array<string, mixed>  $state
     */
    public static function resolveOpeningHours(array $state): ?OpeningHours
    {
        if (empty($state)) {
            return null;
        }

        $hours = $state['hours'] ?? $state;
        $exceptions = $state['exceptions'] ?? [];
        $timezone = $state['timezone'] ?? null;

        $config = is_array($hours) ? $hours : [];

        if (! empty($exceptions)) {
            $config['exceptions'] = $exceptions;
        }

        if ($timezone) {
            $config['timezone'] = $timezone;
        }

        return OpeningHours::create($config);
    }
}
