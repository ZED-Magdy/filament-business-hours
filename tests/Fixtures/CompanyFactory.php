<?php

declare(strict_types=1);

namespace ZEDMagdy\FilamentBusinessHours\Tests\Fixtures;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Company>
 */
class CompanyFactory extends Factory
{
    protected $model = Company::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'business_hours' => [
                'hours' => [
                    'monday' => ['09:00-17:00'],
                    'tuesday' => ['09:00-17:00'],
                    'wednesday' => ['09:00-17:00'],
                    'thursday' => ['09:00-17:00'],
                    'friday' => ['09:00-17:00'],
                    'saturday' => [],
                    'sunday' => [],
                ],
                'exceptions' => [],
                'timezone' => 'UTC',
            ],
        ];
    }

    public function withLunchBreak(): static
    {
        return $this->state(fn (): array => [
            'business_hours' => [
                'hours' => [
                    'monday' => ['09:00-12:00', '13:00-17:00'],
                    'tuesday' => ['09:00-12:00', '13:00-17:00'],
                    'wednesday' => ['09:00-12:00', '13:00-17:00'],
                    'thursday' => ['09:00-12:00', '13:00-17:00'],
                    'friday' => ['09:00-12:00', '13:00-17:00'],
                    'saturday' => [],
                    'sunday' => [],
                ],
                'exceptions' => [],
                'timezone' => 'UTC',
            ],
        ]);
    }

    public function closedAllWeek(): static
    {
        return $this->state(fn (): array => [
            'business_hours' => [
                'hours' => [
                    'monday' => [],
                    'tuesday' => [],
                    'wednesday' => [],
                    'thursday' => [],
                    'friday' => [],
                    'saturday' => [],
                    'sunday' => [],
                ],
                'exceptions' => [],
                'timezone' => 'UTC',
            ],
        ]);
    }

    public function openAllWeek(): static
    {
        return $this->state(fn (): array => [
            'business_hours' => [
                'hours' => [
                    'monday' => ['00:00-24:00'],
                    'tuesday' => ['00:00-24:00'],
                    'wednesday' => ['00:00-24:00'],
                    'thursday' => ['00:00-24:00'],
                    'friday' => ['00:00-24:00'],
                    'saturday' => ['00:00-24:00'],
                    'sunday' => ['00:00-24:00'],
                ],
                'exceptions' => [],
                'timezone' => 'UTC',
            ],
        ]);
    }

    public function withExceptions(): static
    {
        return $this->state(fn (): array => [
            'business_hours' => [
                'hours' => [
                    'monday' => ['09:00-17:00'],
                    'tuesday' => ['09:00-17:00'],
                    'wednesday' => ['09:00-17:00'],
                    'thursday' => ['09:00-17:00'],
                    'friday' => ['09:00-17:00'],
                    'saturday' => [],
                    'sunday' => [],
                ],
                'exceptions' => [
                    '12-25' => [],
                    '01-01' => [],
                    '2026-04-10' => ['09:00-12:00'],
                ],
                'timezone' => 'UTC',
            ],
        ]);
    }

    public function withTimezone(string $timezone): static
    {
        return $this->state(function (array $attributes) use ($timezone): array {
            $hours = $attributes['business_hours'] ?? [];
            $hours['timezone'] = $timezone;

            return ['business_hours' => $hours];
        });
    }
}
