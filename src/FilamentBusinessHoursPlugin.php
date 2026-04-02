<?php

declare(strict_types=1);

namespace ZEDMagdy\FilamentBusinessHours;

use Filament\Contracts\Plugin;
use Filament\Panel;

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

    public function timezone(?string $timezone): static
    {
        $this->timezone = $timezone;

        return $this;
    }

    public function getTimezone(): string
    {
        return $this->timezone
            ?? config('filament-business-hours.timezone')
            ?? config('app.timezone', 'UTC');
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
