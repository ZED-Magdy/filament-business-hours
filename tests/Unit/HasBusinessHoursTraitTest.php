<?php

declare(strict_types=1);

use Carbon\Carbon;
use Spatie\OpeningHours\OpeningHours;
use Spatie\OpeningHours\OpeningHoursForDay;
use ZEDMagdy\FilamentBusinessHours\Tests\Fixtures\Company;

it('creates an opening hours instance from nested data', function (): void {
    $company = Company::factory()->create();

    $openingHours = $company->getOpeningHours();

    expect($openingHours)->toBeInstanceOf(OpeningHours::class);
});

it('reports open during business hours', function (): void {
    $company = Company::factory()->create();

    // Monday at 10:00 UTC
    $mondayMorning = Carbon::create(2026, 4, 6, 10, 0, 0, 'UTC');

    expect($company->isOpen($mondayMorning))->toBeTrue()
        ->and($company->isClosed($mondayMorning))->toBeFalse();
});

it('reports closed outside business hours', function (): void {
    $company = Company::factory()->create();

    // Monday at 20:00 UTC (after 17:00)
    $mondayEvening = Carbon::create(2026, 4, 6, 20, 0, 0, 'UTC');

    expect($company->isOpen($mondayEvening))->toBeFalse()
        ->and($company->isClosed($mondayEvening))->toBeTrue();
});

it('reports closed on weekends', function (): void {
    $company = Company::factory()->create();

    // Saturday at 10:00 UTC
    $saturday = Carbon::create(2026, 4, 4, 10, 0, 0, 'UTC');

    expect($company->isOpen($saturday))->toBeFalse();
});

it('checks if open on a specific day', function (): void {
    $company = Company::factory()->create();

    expect($company->isOpenOn('monday'))->toBeTrue()
        ->and($company->isOpenOn('sunday'))->toBeFalse()
        ->and($company->isClosedOn('sunday'))->toBeTrue()
        ->and($company->isClosedOn('monday'))->toBeFalse();
});

it('handles lunch break schedules', function (): void {
    $company = Company::factory()->withLunchBreak()->create();

    // Monday at 10:00 UTC (morning session)
    $morning = Carbon::create(2026, 4, 6, 10, 0, 0, 'UTC');
    expect($company->isOpen($morning))->toBeTrue();

    // Monday at 12:30 UTC (lunch break)
    $lunch = Carbon::create(2026, 4, 6, 12, 30, 0, 'UTC');
    expect($company->isOpen($lunch))->toBeFalse();

    // Monday at 14:00 UTC (afternoon session)
    $afternoon = Carbon::create(2026, 4, 6, 14, 0, 0, 'UTC');
    expect($company->isOpen($afternoon))->toBeTrue();
});

it('handles closed all week', function (): void {
    $company = Company::factory()->closedAllWeek()->create();

    $monday = Carbon::create(2026, 4, 6, 10, 0, 0, 'UTC');

    expect($company->isOpen($monday))->toBeFalse()
        ->and($company->isOpenOn('monday'))->toBeFalse();
});

it('handles open 24/7', function (): void {
    $company = Company::factory()->openAllWeek()->create();

    $saturday = Carbon::create(2026, 4, 4, 3, 0, 0, 'UTC');

    expect($company->isOpen($saturday))->toBeTrue()
        ->and($company->isOpenOn('saturday'))->toBeTrue();
});

it('calculates next open time', function (): void {
    $company = Company::factory()->create();

    // Saturday at 10:00 UTC (closed)
    $saturday = Carbon::create(2026, 4, 4, 10, 0, 0, 'UTC');
    $nextOpen = $company->nextOpen($saturday);

    expect($nextOpen)->toBeInstanceOf(Carbon::class)
        ->and($nextOpen->dayOfWeek)->toBe(Carbon::MONDAY);
});

it('calculates next close time', function (): void {
    $company = Company::factory()->create();

    // Monday at 10:00 UTC (open)
    $monday = Carbon::create(2026, 4, 6, 10, 0, 0, 'UTC');
    $nextClose = $company->nextClose($monday);

    expect($nextClose)->toBeInstanceOf(Carbon::class)
        ->and($nextClose->hour)->toBe(17)
        ->and($nextClose->minute)->toBe(0);
});

it('handles exceptions for holidays', function (): void {
    $company = Company::factory()->withExceptions()->create();

    // Christmas day (recurring 12-25 exception = closed)
    $christmas = Carbon::create(2026, 12, 25, 10, 0, 0, 'UTC');
    expect($company->isOpen($christmas))->toBeFalse();

    // New Year's Day (recurring 01-01 exception = closed)
    $newYear = Carbon::create(2026, 1, 1, 10, 0, 0, 'UTC');
    expect($company->isOpen($newYear))->toBeFalse();

    // Specific date exception with custom hours (2026-04-10 09:00-12:00)
    $specialDay = Carbon::create(2026, 4, 10, 10, 0, 0, 'UTC');
    expect($company->isOpen($specialDay))->toBeTrue();

    $specialDayAfternoon = Carbon::create(2026, 4, 10, 14, 0, 0, 'UTC');
    expect($company->isOpen($specialDayAfternoon))->toBeFalse();
});

it('resolves business timezone', function (): void {
    $company = Company::factory()->withTimezone('Asia/Riyadh')->create();

    expect($company->getBusinessTimezone())->toBe('Asia/Riyadh');
});

it('falls back to config timezone', function (): void {
    config()->set('filament-business-hours.timezone', 'Europe/London');

    $company = Company::factory()->create([
        'business_hours' => [
            'hours' => ['monday' => ['09:00-17:00']],
            'exceptions' => [],
        ],
    ]);

    expect($company->getBusinessTimezone())->toBe('Europe/London');
});

it('returns opening hours for a specific day', function (): void {
    $company = Company::factory()->create();

    $mondayHours = $company->openingHoursForDay('monday');

    expect($mondayHours)->toBeInstanceOf(OpeningHoursForDay::class)
        ->and($mondayHours->count())->toBe(1);
});

it('returns business hours casts', function (): void {
    $casts = Company::getBusinessHoursCasts();

    expect($casts)->toBeArray()
        ->and($casts)->toHaveKey('business_hours')
        ->and($casts['business_hours'])->toBe('array');
});

it('supports multi-column storage format', function (): void {
    $company = Company::factory()->create([
        'business_hours' => [
            'monday' => ['09:00-17:00'],
            'tuesday' => ['09:00-17:00'],
            'wednesday' => ['09:00-17:00'],
            'thursday' => ['09:00-17:00'],
            'friday' => ['09:00-17:00'],
            'saturday' => [],
            'sunday' => [],
        ],
        'business_hours_exceptions' => [
            '12-25' => [],
        ],
        'business_hours_timezone' => 'America/New_York',
    ]);

    expect($company->isOpenOn('monday'))->toBeTrue()
        ->and($company->isOpenOn('sunday'))->toBeFalse()
        ->and($company->getBusinessTimezone())->toBe('America/New_York');

    $christmas = Carbon::create(2026, 12, 25, 10, 0, 0, 'America/New_York');
    expect($company->isOpen($christmas))->toBeFalse();
});
