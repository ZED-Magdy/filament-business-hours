<?php

declare(strict_types=1);

use ZEDMagdy\FilamentBusinessHours\Enums\DayOfWeek;
use ZEDMagdy\FilamentBusinessHours\Forms\Components\BusinessHoursField;

it('can be created with make', function (): void {
    $field = BusinessHoursField::make('business_hours');

    expect($field)->toBeInstanceOf(BusinessHoursField::class);
});

it('has timezone enabled by default', function (): void {
    $field = BusinessHoursField::make('business_hours');

    expect($field->hasTimezone())->toBeTrue();
});

it('can disable timezone', function (): void {
    $field = BusinessHoursField::make('business_hours')
        ->timezone(false);

    expect($field->hasTimezone())->toBeFalse();
});

it('has exceptions enabled by default', function (): void {
    $field = BusinessHoursField::make('business_hours');

    expect($field->hasExceptions())->toBeTrue();
});

it('can disable exceptions', function (): void {
    $field = BusinessHoursField::make('business_hours')
        ->allowExceptions(false);

    expect($field->hasExceptions())->toBeFalse();
});

it('is collapsible by default', function (): void {
    $field = BusinessHoursField::make('business_hours');

    expect($field->isCollapsible())->toBeTrue();
});

it('can disable collapsible', function (): void {
    $field = BusinessHoursField::make('business_hours')
        ->collapsible(false);

    expect($field->isCollapsible())->toBeFalse();
});

it('returns all days of the week', function (): void {
    $field = BusinessHoursField::make('business_hours');

    $days = $field->getDays();

    expect($days)->toHaveCount(7)
        ->and($days[0])->toBe(DayOfWeek::Monday)
        ->and($days[6])->toBe(DayOfWeek::Sunday);
});

it('returns timezone options', function (): void {
    $field = BusinessHoursField::make('business_hours');

    $options = $field->getTimezoneOptions();

    expect($options)->toBeArray()
        ->and($options)->toHaveKey('UTC')
        ->and($options)->toHaveKey('Asia/Riyadh')
        ->and($options)->toHaveKey('America/New_York');
});

it('uses app timezone as default', function (): void {
    config()->set('app.timezone', 'Asia/Riyadh');
    config()->set('filament-business-hours.timezone', null);

    $field = BusinessHoursField::make('business_hours');

    expect($field->getDefaultTimezone())->toBe('Asia/Riyadh');
});

it('can set a custom default timezone', function (): void {
    $field = BusinessHoursField::make('business_hours')
        ->defaultTimezone('Europe/London');

    expect($field->getDefaultTimezone())->toBe('Europe/London');
});
