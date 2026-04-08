<?php

declare(strict_types=1);

namespace ZEDMagdy\FilamentBusinessHours\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as Orchestra;
use ZEDMagdy\FilamentBusinessHours\FilamentBusinessHours;
use ZEDMagdy\FilamentBusinessHours\FilamentBusinessHoursServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            FilamentBusinessHoursServiceProvider::class,
        ];
    }

    protected function defineDatabaseMigrations(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('business_hours')->nullable();
            $table->json('business_hours_exceptions')->nullable();
            $table->string('business_hours_timezone')->nullable();
            $table->timestamps();
        });
    }

    public function getEnvironmentSetUp($app): void
    {
        config()->set('database.default', 'testing');
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Flush the config cache so that runtime config()->set() overrides in
        // individual tests are always respected.
        FilamentBusinessHours::flushCache();
    }
}
