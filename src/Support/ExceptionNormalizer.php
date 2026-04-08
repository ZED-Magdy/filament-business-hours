<?php

declare(strict_types=1);

namespace ZEDMagdy\FilamentBusinessHours\Support;

class ExceptionNormalizer
{
    /**
     * Convert exceptions from the array-of-objects form used by the form field
     * to the key-value form expected by spatie/opening-hours.
     *
     * Input:  [['date' => '12-25', 'start' => '09:00', 'end' => '12:00']]
     * Output: ['12-25' => ['09:00-12:00']]
     *
     * Input:  [['date' => '12-25', 'start' => '', 'end' => '']]
     * Output: ['12-25' => []]
     *
     * @param  array<int|string, mixed>  $exceptions
     * @return array<string, array<string>>
     */
    public static function toKeyValue(array $exceptions): array
    {
        $normalized = [];

        foreach ($exceptions as $key => $value) {
            if (is_array($value) && isset($value['date'])) {
                $date = (string) $value['date'];
                $start = $value['start'] ?? '';
                $end = $value['end'] ?? '';

                $normalized[$date] = ($start !== '' && $end !== '')
                    ? [$start.'-'.$end]
                    : [];
            } else {
                $normalized[(string) $key] = is_array($value) ? $value : [];
            }
        }

        return $normalized;
    }

    /**
     * Convert exceptions from the key-value form (spatie/opening-hours) to the
     * array-of-objects form used by the form field.
     *
     * Already-normalised arrays (first element is an associative array with a
     * 'date' key) are returned unchanged.
     *
     * Input:  ['12-25' => []]
     * Output: [['date' => '12-25', 'start' => '', 'end' => '', 'label' => '']]
     *
     * @param  array<int|string, mixed>  $exceptions
     * @return array<int, array<string, string>>
     */
    public static function toArrayOfObjects(array $exceptions): array
    {
        if ($exceptions !== [] && isset($exceptions[0]) && is_array($exceptions[0]) && isset($exceptions[0]['date'])) {
            return $exceptions;
        }

        $normalized = [];

        foreach ($exceptions as $key => $value) {
            if (is_int($key) && is_array($value) && isset($value['date'])) {
                $normalized[] = $value;

                continue;
            }

            $date = (string) $key;
            $ranges = is_array($value) ? $value : [];
            $range = $ranges[0] ?? '';
            $parts = $range !== '' ? explode('-', $range, 2) : ['', ''];

            $normalized[] = [
                'date' => $date,
                'start' => $parts[0],
                'end' => $parts[1] ?? '',
                'label' => '',
            ];
        }

        return $normalized;
    }
}
