<?php

declare(strict_types=1);

namespace ZEDMagdy\FilamentBusinessHours;

use Filament\Contracts\Plugin;
use Filament\Panel;
use InvalidArgumentException;
use ZEDMagdy\FilamentBusinessHours\Support\TimezoneResolver;

class FilamentBusinessHoursPlugin implements Plugin
{
    protected ?string $timezone = null;

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }

    public function getId(): string
    {
        return 'filament-business-hours';
    }

    /**
     * Set the timezone used for open/closed calculations.
     *
     * @throws InvalidArgumentException if the string is not a recognised PHP timezone identifier.
     */
    public function timezone(?string $timezone): static
    {
        if ($timezone !== null && ! TimezoneResolver::isValid($timezone)) {
            throw new InvalidArgumentException(
                "Invalid timezone \"{$timezone}\". Use a valid PHP timezone identifier (e.g. \"Europe/London\")."
            );
        }

        $this->timezone = $timezone;

        return $this;
    }

    public function getTimezone(): string
    {
        return TimezoneResolver::resolve($this->timezone);
    }

    public function register(Panel $panel): void
    {
        //
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
