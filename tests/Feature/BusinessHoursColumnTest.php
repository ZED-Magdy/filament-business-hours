<?php

declare(strict_types=1);

use ZEDMagdy\FilamentBusinessHours\Tables\Columns\BusinessHoursColumn;

it('can be created with make', function (): void {
    $column = BusinessHoursColumn::make('business_hours');

    expect($column)->toBeInstanceOf(BusinessHoursColumn::class);
});

it('defaults to status display mode', function (): void {
    $column = BusinessHoursColumn::make('business_hours');

    expect($column->getDisplayMode())->toBe('status');
});

it('can be set to schedule mode', function (): void {
    $column = BusinessHoursColumn::make('business_hours')
        ->scheduleMode();

    expect($column->getDisplayMode())->toBe('schedule');
});

it('can be set to status mode', function (): void {
    $column = BusinessHoursColumn::make('business_hours')
        ->scheduleMode()
        ->statusMode();

    expect($column->getDisplayMode())->toBe('status');
});

it('can set custom display mode', function (): void {
    $column = BusinessHoursColumn::make('business_hours')
        ->displayMode('custom');

    expect($column->getDisplayMode())->toBe('custom');
});
