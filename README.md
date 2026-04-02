# Filament Business Hours

A Filament plugin for managing business hours with timezone support, multiple time slots per day, and exception handling.

## Installation

```bash
composer require zedmagdy/filament-business-hours
```

## Setup

### 1. Register the Plugin

In your Filament panel provider:

```php
use ZEDMagdy\FilamentBusinessHours\FilamentBusinessHoursPlugin;

->plugins([
    FilamentBusinessHoursPlugin::make()
        ->timezone('Asia/Riyadh'), // optional
])
```

### 2. Add Database Columns

Add a JSON column to your model's table:

```php
Schema::table('branches', function (Blueprint $table) {
    $table->json('business_hours')->nullable();
});
```

### 3. Configure Your Model

```php
use ZEDMagdy\FilamentBusinessHours\Traits\HasBusinessHours;

class Branch extends Model
{
    use HasBusinessHours;

    protected function casts(): array
    {
        return [
            'business_hours' => 'array',
        ];
    }
}
```

## Usage

### Form Field

```php
use ZEDMagdy\FilamentBusinessHours\Forms\Components\BusinessHoursField;

BusinessHoursField::make('business_hours')
    ->timezone()              // show timezone selector (default: true)
    ->allowExceptions()       // show exceptions section (default: true)
    ->collapsible()           // collapsible day sections (default: true)
    ->defaultTimezone('UTC')  // default timezone for new records
    ->columnSpanFull()
```

### Table Column

```php
use ZEDMagdy\FilamentBusinessHours\Tables\Columns\BusinessHoursColumn;

// Status badge (Open/Closed)
BusinessHoursColumn::make('business_hours')
    ->statusMode()

// Weekly schedule grid
BusinessHoursColumn::make('business_hours')
    ->scheduleMode()
```

### Infolist Entry

```php
use ZEDMagdy\FilamentBusinessHours\Infolists\Components\BusinessHoursEntry;

// Full schedule with exceptions
BusinessHoursEntry::make('business_hours')
    ->fullMode()

// Status badge only
BusinessHoursEntry::make('business_hours')
    ->statusMode()

// Compact single-line
BusinessHoursEntry::make('business_hours')
    ->compactMode()
```

### Model Methods

```php
$branch = Branch::first();

$branch->isOpen();              // true/false (current time)
$branch->isClosed();            // true/false
$branch->isOpenOn('monday');    // true/false
$branch->isClosedOn('sunday');  // true/false
$branch->nextOpen();            // Carbon instance
$branch->nextClose();           // Carbon instance
$branch->getBusinessTimezone(); // string
```

## Configuration

Publish the config file:

```bash
php artisan vendor:publish --tag="filament-business-hours-config"
```

## License

MIT
