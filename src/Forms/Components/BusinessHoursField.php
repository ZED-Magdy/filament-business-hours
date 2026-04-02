<?php

declare(strict_types=1);

namespace ZEDMagdy\FilamentBusinessHours\Forms\Components;

use Closure;
use Filament\Forms\Components\Field;
use ZEDMagdy\FilamentBusinessHours\Enums\DayOfWeek;
use ZEDMagdy\FilamentBusinessHours\FilamentBusinessHours;

class BusinessHoursField extends Field
{
    protected string $view = 'filament-business-hours::forms.components.business-hours-field';

    protected bool|Closure $hasTimezone = true;

    protected bool|Closure $hasExceptions = true;

    protected bool|Closure $isCollapsible = true;

    protected ?string $defaultTimezone = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->default(function (): array {
            return [
                'enabled' => true,
                'hours' => FilamentBusinessHours::getDefaultHours(),
                'exceptions' => [],
                'timezone' => $this->getDefaultTimezone(),
            ];
        });

        // Normalize exceptions from key-value format to array format on load
        $this->afterStateHydrated(function (self $component, ?array $state): void {
            if ($state === null) {
                return;
            }

            $exceptions = $state['exceptions'] ?? [];

            if (! is_array($exceptions)) {
                $exceptions = [];
            }

            // Already in array-of-objects format
            if ($exceptions !== [] && isset($exceptions[0]) && is_array($exceptions[0])) {
                return;
            }

            // Convert key-value format { "12-25": [] } to array format
            $normalized = [];

            foreach ($exceptions as $key => $value) {
                if (is_int($key) && is_array($value) && isset($value['date'])) {
                    $normalized[] = $value;

                    continue;
                }

                $date = (string) $key;
                $ranges = is_array($value) ? $value : [];
                $range = $ranges[0] ?? '';
                $parts = $range !== '' ? explode('-', $range, 2) : ['', ''];

                $normalized[] = [
                    'date' => $date,
                    'start' => $parts[0] ?? '',
                    'end' => $parts[1] ?? '',
                    'label' => '',
                ];
            }

            $state['exceptions'] = $normalized;
            $component->state($state);
        });

        $this->dehydrateStateUsing(function (?array $state): ?array {
            if ($state === null) {
                return null;
            }

            $hours = $state['hours'] ?? [];

            foreach ($hours as $day => $ranges) {
                if (! is_array($ranges)) {
                    $hours[$day] = [];

                    continue;
                }

                $hours[$day] = array_values(array_filter($ranges, fn ($range): bool => is_string($range) && str_contains($range, '-') && $range !== '-'
                ));
            }

            $exceptions = $state['exceptions'] ?? [];
            $normalized = [];

            foreach ($exceptions as $exception) {
                if (! is_array($exception) || empty($exception['date'] ?? '')) {
                    continue;
                }

                $date = $exception['date'];
                $start = $exception['start'] ?? '';
                $end = $exception['end'] ?? '';

                if ($start !== '' && $end !== '') {
                    $normalized[$date] = [$start.'-'.$end];
                } else {
                    $normalized[$date] = [];
                }
            }

            // Preserve the full exception data for the form to read back
            $exceptionDetails = [];

            foreach ($exceptions as $exception) {
                if (! is_array($exception) || empty($exception['date'] ?? '')) {
                    continue;
                }

                $exceptionDetails[] = [
                    'date' => $exception['date'],
                    'start' => $exception['start'] ?? '',
                    'end' => $exception['end'] ?? '',
                    'label' => $exception['label'] ?? '',
                ];
            }

            return [
                'enabled' => $state['enabled'] ?? true,
                'hours' => $hours,
                'exceptions' => $exceptionDetails,
                'timezone' => $state['timezone'] ?? $this->getDefaultTimezone(),
            ];
        });
    }

    public function timezone(bool|Closure $condition = true): static
    {
        $this->hasTimezone = $condition;

        return $this;
    }

    public function allowExceptions(bool|Closure $condition = true): static
    {
        $this->hasExceptions = $condition;

        return $this;
    }

    public function collapsible(bool|Closure $condition = true): static
    {
        $this->isCollapsible = $condition;

        return $this;
    }

    public function defaultTimezone(?string $timezone): static
    {
        $this->defaultTimezone = $timezone;

        return $this;
    }

    public function hasTimezone(): bool
    {
        return (bool) $this->evaluate($this->hasTimezone);
    }

    public function hasExceptions(): bool
    {
        return (bool) $this->evaluate($this->hasExceptions);
    }

    public function isCollapsible(): bool
    {
        return (bool) $this->evaluate($this->isCollapsible);
    }

    public function getDefaultTimezone(): string
    {
        return $this->defaultTimezone
            ?? config('filament-business-hours.timezone')
            ?? config('app.timezone', 'UTC');
    }

    /** @return array<DayOfWeek> */
    public function getDays(): array
    {
        return DayOfWeek::cases();
    }

    /** @return array<string, string> */
    public function getTimezoneOptions(): array
    {
        return collect(timezone_identifiers_list())
            ->mapWithKeys(fn (string $tz): array => [$tz => $tz])
            ->all();
    }
}
