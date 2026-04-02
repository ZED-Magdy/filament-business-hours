<?php

declare(strict_types=1);

use ZEDMagdy\FilamentBusinessHours\Enums\DayOfWeek;

it('has all seven days', function (): void {
    expect(DayOfWeek::cases())->toHaveCount(7);
});

it('has correct values', function (): void {
    expect(DayOfWeek::Monday->value)->toBe('monday')
        ->and(DayOfWeek::Tuesday->value)->toBe('tuesday')
        ->and(DayOfWeek::Wednesday->value)->toBe('wednesday')
        ->and(DayOfWeek::Thursday->value)->toBe('thursday')
        ->and(DayOfWeek::Friday->value)->toBe('friday')
        ->and(DayOfWeek::Saturday->value)->toBe('saturday')
        ->and(DayOfWeek::Sunday->value)->toBe('sunday');
});

it('returns labels', function (): void {
    expect(DayOfWeek::Monday->label())->toBe('Monday')
        ->and(DayOfWeek::Sunday->label())->toBe('Sunday');
});

it('returns short labels', function (): void {
    expect(DayOfWeek::Monday->shortLabel())->toBe('Mon')
        ->and(DayOfWeek::Wednesday->shortLabel())->toBe('Wed')
        ->and(DayOfWeek::Sunday->shortLabel())->toBe('Sun');
});

it('can be created from string value', function (): void {
    expect(DayOfWeek::from('monday'))->toBe(DayOfWeek::Monday)
        ->and(DayOfWeek::from('friday'))->toBe(DayOfWeek::Friday);
});

it('returns null for invalid value with tryFrom', function (): void {
    expect(DayOfWeek::tryFrom('invalid'))->toBeNull();
});
