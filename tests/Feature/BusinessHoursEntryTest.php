<?php

declare(strict_types=1);

use ZEDMagdy\FilamentBusinessHours\Infolists\Components\BusinessHoursEntry;

it('can be created with make', function (): void {
    $entry = BusinessHoursEntry::make('business_hours');

    expect($entry)->toBeInstanceOf(BusinessHoursEntry::class);
});

it('defaults to full display mode', function (): void {
    $entry = BusinessHoursEntry::make('business_hours');

    expect($entry->getDisplayMode())->toBe('full');
});

it('can be set to status mode', function (): void {
    $entry = BusinessHoursEntry::make('business_hours')
        ->statusMode();

    expect($entry->getDisplayMode())->toBe('status');
});

it('can be set to compact mode', function (): void {
    $entry = BusinessHoursEntry::make('business_hours')
        ->compactMode();

    expect($entry->getDisplayMode())->toBe('compact');
});

it('can be set back to full mode', function (): void {
    $entry = BusinessHoursEntry::make('business_hours')
        ->compactMode()
        ->fullMode();

    expect($entry->getDisplayMode())->toBe('full');
});

it('can set custom display mode', function (): void {
    $entry = BusinessHoursEntry::make('business_hours')
        ->displayMode('weekly');

    expect($entry->getDisplayMode())->toBe('weekly');
});
