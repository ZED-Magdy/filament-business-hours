<?php

declare(strict_types=1);

namespace ZEDMagdy\FilamentBusinessHours\Infolists\Components;

use Carbon\Carbon;
use Closure;
use Filament\Infolists\Components\Entry;
use Spatie\OpeningHours\OpeningHours;
use ZEDMagdy\FilamentBusinessHours\Support\ScheduleFormatter;
use ZEDMagdy\FilamentBusinessHours\Support\TimezoneResolver;

class BusinessHoursEntry extends Entry
{
    protected string $view = 'filament-business-hours::infolists.components.business-hours-entry';

    protected string|Closure $displayMode = 'full';

    public function fullMode(): static
    {
        $this->displayMode = 'full';

        return $this;
    }

    public function statusMode(): static
    {
        $this->displayMode = 'status';

        return $this;
    }

    public function compactMode(): static
    {
        $this->displayMode = 'compact';

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

    /** @return array<string, array<string>> */
    public function getExceptions(): array
    {
        $state = $this->getState();

        if (empty($state)) {
            return [];
        }

        return $state['exceptions'] ?? [];
    }

    public function getTimezone(): ?string
    {
        $state = $this->getState();

        return $state['timezone'] ?? null;
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
