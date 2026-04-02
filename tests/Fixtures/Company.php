<?php

declare(strict_types=1);

namespace ZEDMagdy\FilamentBusinessHours\Tests\Fixtures;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use ZEDMagdy\FilamentBusinessHours\Traits\HasBusinessHours;

class Company extends Model
{
    use HasBusinessHours;
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'business_hours' => 'array',
            'business_hours_exceptions' => 'array',
        ];
    }

    protected static function newFactory(): CompanyFactory
    {
        return CompanyFactory::new();
    }
}
