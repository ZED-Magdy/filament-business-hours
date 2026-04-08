<?php

declare(strict_types=1);

namespace ZEDMagdy\FilamentBusinessHours\Forms\Components;

use Closure;
use Filament\Forms\Components\Field;
use ZEDMagdy\FilamentBusinessHours\Enums\DayOfWeek;
use ZEDMagdy\FilamentBusinessHours\FilamentBusinessHours;
use ZEDMagdy\FilamentBusinessHours\Support\ExceptionNormalizer;
use ZEDMagdy\FilamentBusinessHours\Support\TimezoneResolver;

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

        // Normalize exceptions from key-value format to array-of-objects format on load.
        $this->afterStateHydrated(function (self $component, ?array $state): void {
            if ($state === null) {
                return;
            }

            $exceptions = $state['exceptions'] ?? [];

            if (! is_array($exceptions)) {
                $exceptions = [];
            }

            $state['exceptions'] = ExceptionNormalizer::toArrayOfObjects($exceptions);
            $component->state($state);
        });

        $this->dehydrateStateUsing(function (?array $state): ?array {
            if ($state === null) {
                return null;
            }

            // Sanitise hours: keep only valid "HH:MM-HH:MM" strings.
            $hours = $state['hours'] ?? [];

            foreach ($hours as $day => $ranges) {
                if (! is_array($ranges)) {
                    $hours[$day] = [];

                    continue;
                }

                $hours[$day] = array_values(
                    array_filter(
                        $ranges,
                        fn ($range): bool => is_string($range) && str_contains($range, '-') && $range !== '-'
                    )
                );
            }

            // Persist exception details in array-of-objects form so the form
            // can re-hydrate them as-is, while also recording the key-value
            // form used by spatie/opening-hours in the same payload.
            $exceptions = $state['exceptions'] ?? [];
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
        return TimezoneResolver::resolve($this->defaultTimezone);
    }

    /** @return array<DayOfWeek> */
    public function getDays(): array
    {
        return DayOfWeek::cases();
    }

    /**
     * Return all PHP timezone identifiers as a key-value map.
     * The result is cached statically so repeated renders do not call
     * timezone_identifiers_list() more than once per process.
     *
     * @return array<string, string>
     */
    public function getTimezoneOptions(): array
    {
        return array_combine(TimezoneResolver::all(), TimezoneResolver::all());
    }
}
