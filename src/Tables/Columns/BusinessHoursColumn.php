<?php

declare(strict_types=1);

namespace ZEDMagdy\FilamentBusinessHours\Tables\Columns;

use Carbon\Carbon;
use Closure;
use Filament\Tables\Columns\Column;
use Spatie\OpeningHours\OpeningHours;
use ZEDMagdy\FilamentBusinessHours\Support\ScheduleFormatter;
use ZEDMagdy\FilamentBusinessHours\Support\TimezoneResolver;

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

        return $openingHours->isOpenAt(Carbon::now($this->resolveTimezone()));
    }

    /** @return array<string, array<string>> */
    public function getFormattedSchedule(): array
    {
        $openingHours = $this->resolveOpeningHours();

        if (! $openingHours) {
            return [];
        }

        return ScheduleFormatter::format($openingHours);
    }

    protected function resolveOpeningHours(): ?OpeningHours
    {
        $state = $this->getState();

        if (empty($state)) {
            return null;
        }

        return ScheduleFormatter::resolveOpeningHours($state);
    }

    protected function resolveTimezone(): string
    {
        $state = $this->getState();

        return TimezoneResolver::resolve($state['timezone'] ?? null);
    }
}
