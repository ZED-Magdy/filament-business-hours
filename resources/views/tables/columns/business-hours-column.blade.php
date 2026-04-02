@php
    $displayMode = $getDisplayMode();
    $isOpen = $isCurrentlyOpen();
    $schedule = $getFormattedSchedule();
    $openLabel = \ZEDMagdy\FilamentBusinessHours\FilamentBusinessHours::getOpenLabel();
    $closedLabel = \ZEDMagdy\FilamentBusinessHours\FilamentBusinessHours::getClosedLabel();
    $days = \ZEDMagdy\FilamentBusinessHours\Enums\DayOfWeek::cases();
@endphp

<div {{ $attributes->merge($getExtraAttributes())->class(['fi-ta-text']) }}>
    @if($displayMode === 'status')
        <span @class([
            'inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset',
            'bg-success-50 text-success-700 ring-success-600/20 dark:bg-success-400/10 dark:text-success-400 dark:ring-success-400/20' => $isOpen,
            'bg-danger-50 text-danger-700 ring-danger-600/20 dark:bg-danger-400/10 dark:text-danger-400 dark:ring-danger-400/20' => !$isOpen,
        ])>
            <span @class([
                'me-1.5 h-1.5 w-1.5 rounded-full',
                'bg-success-500' => $isOpen,
                'bg-danger-500' => !$isOpen,
            ])></span>
            {{ $isOpen ? $openLabel : $closedLabel }}
        </span>
    @elseif($displayMode === 'schedule')
        <div class="space-y-0.5">
            @foreach($days as $day)
                @php
                    $ranges = $schedule[$day->value] ?? [];
                @endphp
                <div class="flex items-center gap-2 text-xs">
                    <span class="w-8 font-medium text-gray-500 dark:text-gray-400">
                        {{ $day->shortLabel() }}
                    </span>
                    @if(empty($ranges))
                        <span class="text-gray-400 dark:text-gray-500">
                            {{ $closedLabel }}
                        </span>
                    @else
                        <span class="text-gray-700 dark:text-gray-300">
                            {{ implode(', ', $ranges) }}
                        </span>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
