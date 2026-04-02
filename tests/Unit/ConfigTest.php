<?php

declare(strict_types=1);

use ZEDMagdy\FilamentBusinessHours\FilamentBusinessHours;

it('resolves column names from config', function (): void {
    expect(FilamentBusinessHours::getHoursColumn())->toBe('business_hours')
        ->and(FilamentBusinessHours::getExceptionsColumn())->toBe('business_hours_exceptions')
        ->and(FilamentBusinessHours::getTimezoneColumn())->toBe('business_hours_timezone');
});

it('resolves custom column names from config', function (): void {
    config()->set('filament-business-hours.columns.hours', 'opening_hours');
    config()->set('filament-business-hours.columns.exceptions', 'opening_exceptions');
    config()->set('filament-business-hours.columns.timezone', 'tz');

    expect(FilamentBusinessHours::getHoursColumn())->toBe('opening_hours')
        ->and(FilamentBusinessHours::getExceptionsColumn())->toBe('opening_exceptions')
        ->and(FilamentBusinessHours::getTimezoneColumn())->toBe('tz');
});

it('resolves default hours from config', function (): void {
    $defaults = FilamentBusinessHours::getDefaultHours();

    expect($defaults)->toBeArray()
        ->and($defaults)->toHaveKey('monday')
        ->and($defaults)->toHaveKey('sunday')
        ->and($defaults['monday'])->toBe(['09:00-17:00'])
        ->and($defaults['sunday'])->toBe([]);
});

it('resolves display settings from config', function (): void {
    expect(FilamentBusinessHours::getTimeFormat())->toBe('H:i')
        ->and(FilamentBusinessHours::getClosedLabel())->toBe('Closed')
        ->and(FilamentBusinessHours::getOpenLabel())->toBe('Open');
});

it('resolves custom display settings from config', function (): void {
    config()->set('filament-business-hours.display.time_format', 'g:i A');
    config()->set('filament-business-hours.display.closed_label', 'Fermé');
    config()->set('filament-business-hours.display.open_label', 'Ouvert');

    expect(FilamentBusinessHours::getTimeFormat())->toBe('g:i A')
        ->and(FilamentBusinessHours::getClosedLabel())->toBe('Fermé')
        ->and(FilamentBusinessHours::getOpenLabel())->toBe('Ouvert');
});
