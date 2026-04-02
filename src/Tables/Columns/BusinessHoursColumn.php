<?php

declare(strict_types=1);

namespace ZEDMagdy\FilamentBusinessHours\Tables\Columns;

use Carbon\Carbon;
use Closure;
use Filament\Tables\Columns\Column;
use Spatie\OpeningHours\OpeningHours;
use ZEDMagdy\FilamentBusinessHours\Enums\DayOfWeek;

class BusinessHoursColumn extends Column
{
    protected string $view = 'filament-business-hours::tables.columns.business-hours-column';

    protected string|Closure $displayMode = 'status';

    public function statusMode(): static
    {
        $this->displayMode = 'status';

        return $this;
    }

    public function scheduleMode(): static
    {
        $this->displayMode = 'schedule';

        return $this;
    }

    public function displayMode(string|Closure $mode): static
    {
        $this->displayMode = $mode;

        return $this;
    }

    public function getDisplayMode(): string
    {
        return $this->evaluate($this->displayMode);
    }

    public function isCurrentlyOpen(): bool
    {
        $openingHours = $this->resolveOpeningHours();

        if (! $openingHours) {
            return false;
        }

        $timezone = $this->resolveTimezone();

        return $openingHours->isOpenAt(Carbon::now($timezone));
    }

    /** @return array<string, array<string>> */
    public function getFormattedSchedule(): array
    {
        $openingHours = $this->resolveOpeningHours();

        if (! $openingHours) {
            return [];
        }

        $schedule = [];

        foreach (DayOfWeek::cases() as $day) {
            $hoursForDay = $openingHours->forDay($day->value);
            $ranges = [];

            foreach ($hoursForDay as $range) {
                $ranges[] = (string) $range;
            }

            $schedule[$day->value] = $ranges;
        }

        return $schedule;
    }

    protected function resolveOpeningHours(): ?OpeningHours
    {
        $state = $this->getState();

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

    protected function resolveTimezone(): string
    {
        $state = $this->getState();

        return $state['timezone']
            ?? config('filament-business-hours.timezone')
            ?? config('app.timezone', 'UTC');
    }
}
