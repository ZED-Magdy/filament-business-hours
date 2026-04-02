@php
    $displayMode = $getDisplayMode();
    $isOpen = $isCurrentlyOpen();
    $schedule = $getFormattedSchedule();
    $exceptions = $getExceptions();
    $timezone = $getTimezone();
    $openLabel = \ZEDMagdy\FilamentBusinessHours\FilamentBusinessHours::getOpenLabel();
    $closedLabel = \ZEDMagdy\FilamentBusinessHours\FilamentBusinessHours::getClosedLabel();
    $days = \ZEDMagdy\FilamentBusinessHours\Enums\DayOfWeek::cases();
@endphp

<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
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

    @elseif($displayMode === 'compact')
        <div class="flex items-center gap-3">
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

            @php
                $todayKey = strtolower(now($timezone)->format('l'));
                $todayRanges = $schedule[$todayKey] ?? [];
            @endphp

            @if(!empty($todayRanges))
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Today') }}: {{ implode(', ', $todayRanges) }}
                </span>
            @endif
        </div>

    @elseif($displayMode === 'full')
        <div class="space-y-4">
            {{-- Current Status --}}
            <div class="flex items-center gap-3">
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

                @if($timezone)
                    <span class="text-xs text-gray-500 dark:text-gray-400">
                        ({{ $timezone }})
                    </span>
                @endif
            </div>

            {{-- Weekly Schedule --}}
            <div class="rounded-lg border border-gray-200 dark:border-white/10">
                @foreach($days as $day)
                    @php
                        $ranges = $schedule[$day->value] ?? [];
                        $isToday = strtolower(now($timezone)->format('l')) === $day->value;
                    @endphp
                    <div @class([
                        'flex items-center justify-between px-3 py-2 text-sm',
                        'border-t border-gray-200 dark:border-white/10' => !$loop->first,
                        'bg-primary-50 dark:bg-primary-500/10' => $isToday,
                    ])>
                        <span @class([
                            'font-medium',
                            'text-primary-700 dark:text-primary-400' => $isToday,
                            'text-gray-700 dark:text-gray-300' => !$isToday,
                        ])>
                            {{ $day->label() }}
                            @if($isToday)
                                <span class="ml-1 text-xs text-primary-500 dark:text-primary-400">({{ __('Today') }})</span>
                            @endif
                        </span>

                        @if(empty($ranges))
                            <span class="text-gray-400 dark:text-gray-500">{{ $closedLabel }}</span>
                        @else
                            <span class="text-gray-700 dark:text-gray-300">
                                {{ implode(', ', $ranges) }}
                            </span>
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- Exceptions --}}
            @if(!empty($exceptions))
                <div>
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('Exceptions') }}
                    </h4>
                    <div class="space-y-1">
                        @foreach($exceptions as $date => $ranges)
                            <div class="flex items-center justify-between text-sm">
                                <span class="font-medium text-gray-600 dark:text-gray-400">
                                    {{ $date }}
                                </span>
                                @if(empty($ranges))
                                    <span class="text-danger-600 dark:text-danger-400">{{ $closedLabel }}</span>
                                @else
                                    <span class="text-gray-700 dark:text-gray-300">
                                        {{ implode(', ', $ranges) }}
                                    </span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @endif
</x-dynamic-component>
