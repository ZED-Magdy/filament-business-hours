<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div
        x-data="{
            state: $wire.$entangle('{{ $getStatePath() }}'),
            collapsed: {},
            showExceptions: false,

            init() {
                if (!this.state || typeof this.state !== 'object') {
                    this.state = {
                        hours: @js(\ZEDMagdy\FilamentBusinessHours\FilamentBusinessHours::getDefaultHours()),
                        exceptions: [],
                        timezone: @js($getDefaultTimezone()),
                    };
                }

                if (!this.state.hours) {
                    this.state.hours = @js(\ZEDMagdy\FilamentBusinessHours\FilamentBusinessHours::getDefaultHours());
                }

                if (!this.state.exceptions) {
                    this.state.exceptions = [];
                }

                if (!Array.isArray(this.state.exceptions)) {
                    const arr = [];
                    for (const [date, hours] of Object.entries(this.state.exceptions)) {
                        arr.push({ date, hours: Array.isArray(hours) ? hours : [] });
                    }
                    this.state.exceptions = arr;
                }

                @if($isCollapsible())
                    @foreach($getDays() as $day)
                        this.collapsed['{{ $day->value }}'] = true;
                    @endforeach
                @endif
            },

            isDayEnabled(day) {
                const hours = this.state.hours[day];
                return Array.isArray(hours) && hours.length > 0;
            },

            toggleDay(day) {
                if (this.isDayEnabled(day)) {
                    this.state.hours[day] = [];
                } else {
                    this.state.hours[day] = ['09:00-17:00'];
                }
            },

            addTimeSlot(day) {
                if (!Array.isArray(this.state.hours[day])) {
                    this.state.hours[day] = [];
                }
                this.state.hours[day].push('09:00-17:00');
            },

            removeTimeSlot(day, index) {
                this.state.hours[day].splice(index, 1);
            },

            updateTimeRange(day, index, type, value) {
                const current = this.state.hours[day][index] || '09:00-17:00';
                const parts = current.split('-');
                if (type === 'open') {
                    this.state.hours[day][index] = value + '-' + (parts[1] || '17:00');
                } else {
                    this.state.hours[day][index] = (parts[0] || '09:00') + '-' + value;
                }
            },

            getOpenTime(day, index) {
                const range = this.state.hours[day]?.[index] || '09:00-17:00';
                return range.split('-')[0] || '09:00';
            },

            getCloseTime(day, index) {
                const range = this.state.hours[day]?.[index] || '09:00-17:00';
                return range.split('-')[1] || '17:00';
            },

            toggleCollapse(day) {
                this.collapsed[day] = !this.collapsed[day];
            },

            addException() {
                if (!Array.isArray(this.state.exceptions)) {
                    this.state.exceptions = [];
                }
                this.state.exceptions.push({ date: '', hours: [] });
            },

            removeException(index) {
                this.state.exceptions.splice(index, 1);
            },

            toggleExceptionClosed(index) {
                if (this.state.exceptions[index].hours.length > 0) {
                    this.state.exceptions[index].hours = [];
                } else {
                    this.state.exceptions[index].hours = ['09:00-17:00'];
                }
            },

            addExceptionSlot(index) {
                this.state.exceptions[index].hours.push('09:00-17:00');
            },

            removeExceptionSlot(exIndex, slotIndex) {
                this.state.exceptions[exIndex].hours.splice(slotIndex, 1);
            },

            updateExceptionRange(exIndex, slotIndex, type, value) {
                const current = this.state.exceptions[exIndex].hours[slotIndex] || '09:00-17:00';
                const parts = current.split('-');
                if (type === 'open') {
                    this.state.exceptions[exIndex].hours[slotIndex] = value + '-' + (parts[1] || '17:00');
                } else {
                    this.state.exceptions[exIndex].hours[slotIndex] = (parts[0] || '09:00') + '-' + value;
                }
            },

            getExceptionOpenTime(exIndex, slotIndex) {
                const range = this.state.exceptions[exIndex]?.hours?.[slotIndex] || '09:00-17:00';
                return range.split('-')[0] || '09:00';
            },

            getExceptionCloseTime(exIndex, slotIndex) {
                const range = this.state.exceptions[exIndex]?.hours?.[slotIndex] || '09:00-17:00';
                return range.split('-')[1] || '17:00';
            },
        }"
        class="space-y-4"
    >
        {{-- Timezone Selector --}}
        @if($hasTimezone())
            <div>
                <label class="fi-fo-field-wrp-label inline-flex items-center gap-x-3">
                    <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white">
                        {{ __('Timezone') }}
                    </span>
                </label>
                <select
                    x-model="state.timezone"
                    class="fi-select-input block w-full rounded-lg border-none bg-transparent py-1.5 pe-8 text-base text-gray-950 transition duration-75 focus:ring-2 focus:ring-primary-600 disabled:text-gray-500 disabled:[-webkit-text-fill-color:theme(colors.gray.500)] dark:text-white dark:focus:ring-primary-500 sm:text-sm sm:leading-6 [&_optgroup]:bg-white [&_optgroup]:dark:bg-gray-900 [&_option]:bg-white [&_option]:dark:bg-gray-900 shadow-sm ring-1 ring-gray-950/10 dark:ring-white/20"
                >
                    @foreach($getTimezoneOptions() as $tz => $label)
                        <option value="{{ $tz }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        @endif

        {{-- Days of the Week --}}
        <div class="space-y-2">
            @foreach($getDays() as $day)
                <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                    {{-- Day Header --}}
                    <div
                        class="flex items-center justify-between px-4 py-3 cursor-pointer"
                        @if($isCollapsible())
                            x-on:click="toggleCollapse('{{ $day->value }}')"
                        @endif
                    >
                        <div class="flex items-center gap-3">
                            @if($isCollapsible())
                                <button
                                    type="button"
                                    class="text-gray-400 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400"
                                    x-on:click.stop="toggleCollapse('{{ $day->value }}')"
                                >
                                    <svg
                                        class="h-5 w-5 transform transition-transform duration-200"
                                        :class="{ '-rotate-90': collapsed['{{ $day->value }}'] }"
                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                    >
                                        <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            @endif

                            <span class="text-sm font-medium text-gray-950 dark:text-white">
                                {{ $day->label() }}
                            </span>

                            <template x-if="!isDayEnabled('{{ $day->value }}')">
                                <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-1 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                                    {{ \ZEDMagdy\FilamentBusinessHours\FilamentBusinessHours::getClosedLabel() }}
                                </span>
                            </template>
                        </div>

                        <button
                            type="button"
                            x-on:click.stop="toggleDay('{{ $day->value }}')"
                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary-600 focus:ring-offset-2 dark:focus:ring-offset-gray-900"
                            :class="isDayEnabled('{{ $day->value }}') ? 'bg-primary-600' : 'bg-gray-200 dark:bg-gray-700'"
                            role="switch"
                            :aria-checked="isDayEnabled('{{ $day->value }}')"
                        >
                            <span
                                class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                :class="isDayEnabled('{{ $day->value }}') ? 'translate-x-5' : 'translate-x-0'"
                            ></span>
                        </button>
                    </div>

                    {{-- Time Slots --}}
                    <div
                        x-show="isDayEnabled('{{ $day->value }}') && !collapsed['{{ $day->value }}']"
                        x-collapse
                        class="border-t border-gray-200 px-4 py-3 dark:border-white/10"
                    >
                        <template x-for="(range, slotIndex) in (state.hours['{{ $day->value }}'] || [])" :key="'{{ $day->value }}-' + slotIndex">
                            <div class="mb-2 flex items-center gap-2">
                                <input
                                    type="time"
                                    :value="getOpenTime('{{ $day->value }}', slotIndex)"
                                    x-on:change="updateTimeRange('{{ $day->value }}', slotIndex, 'open', $event.target.value)"
                                    class="fi-input block w-full rounded-lg border-none bg-transparent py-1.5 text-base text-gray-950 transition duration-75 focus:ring-2 focus:ring-primary-600 dark:text-white sm:text-sm sm:leading-6 shadow-sm ring-1 ring-gray-950/10 dark:ring-white/20"
                                />
                                <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('to') }}</span>
                                <input
                                    type="time"
                                    :value="getCloseTime('{{ $day->value }}', slotIndex)"
                                    x-on:change="updateTimeRange('{{ $day->value }}', slotIndex, 'close', $event.target.value)"
                                    class="fi-input block w-full rounded-lg border-none bg-transparent py-1.5 text-base text-gray-950 transition duration-75 focus:ring-2 focus:ring-primary-600 dark:text-white sm:text-sm sm:leading-6 shadow-sm ring-1 ring-gray-950/10 dark:ring-white/20"
                                />
                                <button
                                    type="button"
                                    x-on:click="removeTimeSlot('{{ $day->value }}', slotIndex)"
                                    class="flex-shrink-0 text-danger-600 hover:text-danger-500 dark:text-danger-400 dark:hover:text-danger-300"
                                >
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 0 0 6 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 1 0 .23 1.482l.149-.022.841 10.518A2.75 2.75 0 0 0 7.596 19h4.807a2.75 2.75 0 0 0 2.742-2.53l.841-10.52.149.023a.75.75 0 0 0 .23-1.482A41.03 41.03 0 0 0 14 4.193V3.75A2.75 2.75 0 0 0 11.25 1h-2.5ZM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4ZM8.58 7.72a.75.75 0 0 0-1.5.06l.3 7.5a.75.75 0 1 0 1.5-.06l-.3-7.5Zm4.34.06a.75.75 0 1 0-1.5-.06l-.3 7.5a.75.75 0 1 0 1.5.06l.3-7.5Z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </template>

                        <button
                            type="button"
                            x-on:click="addTimeSlot('{{ $day->value }}')"
                            class="inline-flex items-center gap-1 text-sm font-medium text-primary-600 hover:text-primary-500 dark:text-primary-400 dark:hover:text-primary-300"
                        >
                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z" />
                            </svg>
                            {{ __('Add time slot') }}
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Exceptions --}}
        @if($hasExceptions())
            <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div
                    class="flex items-center justify-between px-4 py-3 cursor-pointer"
                    x-on:click="showExceptions = !showExceptions"
                >
                    <div class="flex items-center gap-3">
                        <button
                            type="button"
                            class="text-gray-400 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400"
                        >
                            <svg
                                class="h-5 w-5 transform transition-transform duration-200"
                                :class="{ '-rotate-90': !showExceptions }"
                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                            >
                                <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <span class="text-sm font-medium text-gray-950 dark:text-white">
                            {{ __('Exceptions') }}
                        </span>
                        <span
                            class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-400"
                            x-text="state.exceptions?.length || 0"
                        ></span>
                    </div>
                </div>

                <div
                    x-show="showExceptions"
                    x-collapse
                    class="border-t border-gray-200 px-4 py-3 space-y-3 dark:border-white/10"
                >
                    <template x-for="(exception, exIndex) in (state.exceptions || [])" :key="'ex-' + exIndex">
                        <div class="rounded-lg bg-gray-50 p-3 dark:bg-gray-800/50">
                            <div class="flex items-center gap-2 mb-2">
                                <input
                                    type="date"
                                    x-model="exception.date"
                                    class="fi-input block w-full rounded-lg border-none bg-transparent py-1.5 text-base text-gray-950 transition duration-75 focus:ring-2 focus:ring-primary-600 dark:text-white sm:text-sm sm:leading-6 shadow-sm ring-1 ring-gray-950/10 dark:ring-white/20 dark:[color-scheme:dark]"
                                    placeholder="YYYY-MM-DD or MM-DD"
                                />

                                <button
                                    type="button"
                                    x-on:click="toggleExceptionClosed(exIndex)"
                                    class="flex-shrink-0 inline-flex items-center rounded-md px-2.5 py-1.5 text-xs font-medium transition-colors"
                                    :class="exception.hours.length === 0
                                        ? 'bg-danger-100 text-danger-700 dark:bg-danger-500/20 dark:text-danger-400'
                                        : 'bg-success-100 text-success-700 dark:bg-success-500/20 dark:text-success-400'"
                                >
                                    <span x-text="exception.hours.length === 0 ? '{{ __('Closed') }}' : '{{ __('Custom hours') }}'"></span>
                                </button>

                                <button
                                    type="button"
                                    x-on:click="removeException(exIndex)"
                                    class="flex-shrink-0 text-danger-600 hover:text-danger-500 dark:text-danger-400 dark:hover:text-danger-300"
                                >
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 0 0 6 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 1 0 .23 1.482l.149-.022.841 10.518A2.75 2.75 0 0 0 7.596 19h4.807a2.75 2.75 0 0 0 2.742-2.53l.841-10.52.149.023a.75.75 0 0 0 .23-1.482A41.03 41.03 0 0 0 14 4.193V3.75A2.75 2.75 0 0 0 11.25 1h-2.5ZM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4ZM8.58 7.72a.75.75 0 0 0-1.5.06l.3 7.5a.75.75 0 1 0 1.5-.06l-.3-7.5Zm4.34.06a.75.75 0 1 0-1.5-.06l-.3 7.5a.75.75 0 1 0 1.5.06l.3-7.5Z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>

                            {{-- Exception time slots --}}
                            <template x-if="exception.hours.length > 0">
                                <div class="space-y-2 mt-2">
                                    <template x-for="(slot, slotIndex) in exception.hours" :key="'ex-' + exIndex + '-slot-' + slotIndex">
                                        <div class="flex items-center gap-2">
                                            <input
                                                type="time"
                                                :value="getExceptionOpenTime(exIndex, slotIndex)"
                                                x-on:change="updateExceptionRange(exIndex, slotIndex, 'open', $event.target.value)"
                                                class="fi-input block w-full rounded-lg border-none bg-transparent py-1.5 text-base text-gray-950 transition duration-75 focus:ring-2 focus:ring-primary-600 dark:text-white sm:text-sm sm:leading-6 shadow-sm ring-1 ring-gray-950/10 dark:ring-white/20"
                                            />
                                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('to') }}</span>
                                            <input
                                                type="time"
                                                :value="getExceptionCloseTime(exIndex, slotIndex)"
                                                x-on:change="updateExceptionRange(exIndex, slotIndex, 'close', $event.target.value)"
                                                class="fi-input block w-full rounded-lg border-none bg-transparent py-1.5 text-base text-gray-950 transition duration-75 focus:ring-2 focus:ring-primary-600 dark:text-white sm:text-sm sm:leading-6 shadow-sm ring-1 ring-gray-950/10 dark:ring-white/20"
                                            />
                                            <button
                                                type="button"
                                                x-on:click="removeExceptionSlot(exIndex, slotIndex)"
                                                class="flex-shrink-0 text-danger-600 hover:text-danger-500 dark:text-danger-400 dark:hover:text-danger-300"
                                            >
                                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </template>

                                    <button
                                        type="button"
                                        x-on:click="addExceptionSlot(exIndex)"
                                        class="inline-flex items-center gap-1 text-xs font-medium text-primary-600 hover:text-primary-500 dark:text-primary-400 dark:hover:text-primary-300"
                                    >
                                        <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z" />
                                        </svg>
                                        {{ __('Add time slot') }}
                                    </button>
                                </div>
                            </template>
                        </div>
                    </template>

                    <button
                        type="button"
                        x-on:click="addException()"
                        class="inline-flex items-center gap-1 text-sm font-medium text-primary-600 hover:text-primary-500 dark:text-primary-400 dark:hover:text-primary-300"
                    >
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z" />
                        </svg>
                        {{ __('Add exception') }}
                    </button>
                </div>
            </div>
        @endif
    </div>
</x-dynamic-component>
