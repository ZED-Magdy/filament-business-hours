<?php

declare(strict_types=1);

namespace ZEDMagdy\FilamentBusinessHours;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentBusinessHoursServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-business-hours';

    public function configurePackage(Package $package): void
    {
        $package
            ->name(static::$name)
            ->hasConfigFile()
            ->hasViews();
    }
}
