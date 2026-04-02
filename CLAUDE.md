# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Overview

`zedmagdy/filament-business-hours` is a Laravel Filament package that provides business hours management with timezone support, multiple time slots per day, and exception handling (holidays).

- **Namespace:** `ZEDMagdy\FilamentBusinessHours`
- **PHP:** ^8.3
- **Filament:** ^4.3.1 || ^5.0
- **Laravel:** ^11.0 || ^12.0

## Commands

```bash
# Run tests
composer test                  # or: vendor/bin/pest

# Run a single test
vendor/bin/pest --filter=testName

# Format code
composer format                # or: vendor/bin/pint

# Format only changed files
vendor/bin/pint --dirty

# Static analysis
composer analyse               # or: vendor/bin/phpstan analyse

# Test with coverage
composer test-coverage
```

## Architecture

This is a Filament Plugin package built with [spatie/laravel-package-tools](https://github.com/spatie/laravel-package-tools).

### Key entry points

- `src/FilamentBusinessHoursServiceProvider.php` — Package service provider. Registers config and views.
- `src/FilamentBusinessHoursPlugin.php` — Implements `Filament\Contracts\Plugin`. Registered in panel providers.
- `src/FilamentBusinessHours.php` — Static config accessor class.
- `config/filament-business-hours.php` — Package configuration (publishable).

### Components

- `src/Forms/Components/BusinessHoursField.php` — Custom Filament form field for editing weekly schedules.
- `src/Tables/Columns/BusinessHoursColumn.php` — Custom Filament table column (status/schedule modes).
- `src/Infolists/Components/BusinessHoursEntry.php` — Custom Filament infolist entry (full/status/compact modes).
- `src/Traits/HasBusinessHours.php` — Eloquent model trait. Integrates `spatie/opening-hours` for isOpen/isClosed queries.
- `src/Enums/DayOfWeek.php` — Backed string enum for days of the week.

### Data Format

Business hours are stored as JSON on the consumer's model. No migrations needed. The recommended single-column format:

```json
{
  "hours": { "monday": ["09:00-17:00"], ... },
  "exceptions": { "12-25": [], "2026-04-10": ["09:00-12:00"] },
  "timezone": "Asia/Riyadh"
}
```

### Testing

- Uses **Pest v4** with **Orchestra Testbench** for package testing.
- `tests/TestCase.php` — Base test case that boots the service provider and creates a test `companies` table.
- `tests/Pest.php` — Binds `TestCase` to all tests.
- `tests/Fixtures/` — Company model and factory for testing.

### Package conventions

- Config tag: `filament-business-hours-config`
- Views tag: `filament-business-hours-views`
- No migrations (data stored as JSON on consumer models).
