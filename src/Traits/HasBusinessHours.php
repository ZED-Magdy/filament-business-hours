<?php

declare(strict_types=1);

namespace ZEDMagdy\FilamentBusinessHours\Traits;

use Carbon\Carbon;
use DateTimeInterface;
use Spatie\OpeningHours\OpeningHours;
use Spatie\OpeningHours\OpeningHoursForDay;
use Spatie\OpeningHours\TimeRange;
use ZEDMagdy\FilamentBusinessHours\FilamentBusinessHours;
use ZEDMagdy\FilamentBusinessHours\Support\ExceptionNormalizer;
use ZEDMagdy\FilamentBusinessHours\Support\TimezoneResolver;

trait HasBusinessHours
{
    /**
     * Per-instance cache so that multiple calls within a single request do not
     * rebuild the OpeningHours object from scratch each time.
     */
    private ?OpeningHours $cachedOpeningHours = null;

    public function getOpeningHours(): OpeningHours
    {
        if ($this->cachedOpeningHours !== null) {
            return $this->cachedOpeningHours;
        }

        $hoursColumn = FilamentBusinessHours::getHoursColumn();
        $exceptionsColumn = FilamentBusinessHours::getExceptionsColumn();
        $timezoneColumn = FilamentBusinessHours::getTimezoneColumn();

        $data = $this->{$hoursColumn} ?? [];

        if (isset($data['hours'])) {
            $hours = $data['hours'] ?? [];
            $exceptions = $data['exceptions'] ?? $this->{$exceptionsColumn} ?? [];
            $timezone = $data['timezone'] ?? $this->{$timezoneColumn} ?? null;
        } else {
            $hours = $data;
            $exceptions = $this->{$exceptionsColumn} ?? [];
            $timezone = $this->{$timezoneColumn} ?? null;
        }

        $config = [];

        foreach ($hours as $day => $ranges) {
            $config[$day] = is_array($ranges) ? $ranges : [];
        }

        if (! empty($exceptions)) {
            $config['exceptions'] = ExceptionNormalizer::toKeyValue($exceptions);
        }

        if ($timezone) {
            $config['timezone'] = $timezone;
        }

        return $this->cachedOpeningHours = OpeningHours::create($config);
    }

    /**
     * Discard the cached OpeningHours instance, e.g. after the model is updated.
     */
    public function flushOpeningHoursCache(): static
    {
        $this->cachedOpeningHours = null;

        return $this;
    }

    public function isBusinessHoursEnabled(): bool
    {
        $hoursColumn = FilamentBusinessHours::getHoursColumn();
        $data = $this->{$hoursColumn} ?? [];

        return ($data['enabled'] ?? true) !== false;
    }

    public function isOpen(?DateTimeInterface $at = null): bool
    {
        if (! $this->isBusinessHoursEnabled()) {
            return false;
        }

        return $this->getOpeningHours()->isOpenAt(
            $at ?? Carbon::now($this->getBusinessTimezone())
        );
    }

    public function isClosed(?DateTimeInterface $at = null): bool
    {
        return ! $this->isOpen($at);
    }

    public function isOpenOn(string $day): bool
    {
        return $this->getOpeningHours()->isOpenOn($day);
    }

    public function isClosedOn(string $day): bool
    {
        return $this->getOpeningHours()->isClosedOn($day);
    }

    public function nextOpen(?DateTimeInterface $from = null): Carbon
    {
        return Carbon::instance(
            $this->getOpeningHours()->nextOpen(
                $from ?? Carbon::now($this->getBusinessTimezone())
            )
        );
    }

    public function nextClose(?DateTimeInterface $from = null): Carbon
    {
        return Carbon::instance(
            $this->getOpeningHours()->nextClose(
                $from ?? Carbon::now($this->getBusinessTimezone())
            )
        );
    }

    public function currentOpenRange(?DateTimeInterface $at = null): ?TimeRange
    {
        return $this->getOpeningHours()->currentOpenRange(
            $at ?? Carbon::now($this->getBusinessTimezone())
        );
    }

    public function openingHoursForDay(string $day): OpeningHoursForDay
    {
        return $this->getOpeningHours()->forDay($day);
    }

    public function getBusinessTimezone(): string
    {
        $hoursColumn = FilamentBusinessHours::getHoursColumn();
        $timezoneColumn = FilamentBusinessHours::getTimezoneColumn();
        $data = $this->{$hoursColumn} ?? [];

        return TimezoneResolver::resolve(
            $data['timezone'] ?? $this->{$timezoneColumn} ?? null
        );
    }

    /** @return array<string, string> */
    public static function getBusinessHoursCasts(): array
    {
        return [
            FilamentBusinessHours::getHoursColumn() => 'array',
            FilamentBusinessHours::getExceptionsColumn() => 'array',
        ];
    }
}
