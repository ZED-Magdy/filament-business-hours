<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div
        x-data="{
            state: $wire.$entangle('{{ $getStatePath() }}'),
            enabled: true,
            showExceptionForm: false,
            newException: { date: '', start: '', end: '', label: '' },

            init() {
                this.ensureState();
                this.normalizeExceptions();
                this.enabled = this.state?.enabled !== false;

                this.$watch('state', (val) => {
                    if (val && typeof val === 'object' && !val.hours) {
                        this.ensureState();
                        this.normalizeExceptions();
                    }
                });
            },

            normalizeExceptions() {
                if (!this.state?.exceptions) return;
                if (!Array.isArray(this.state.exceptions)) {
                    const arr = [];
                    for (const [date, hours] of Object.entries(this.state.exceptions)) {
                        const range = Array.isArray(hours) && hours.length > 0 ? hours[0] : '';
                        const parts = range ? range.split('-') : ['', ''];
                        arr.push({ date, start: parts[0] || '', end: parts[1] || '', label: '' });
                    }
                    this.state.exceptions = arr;
                }
            },

            toggleEnabled() {
                this.enabled = !this.enabled;
                this.state.enabled = this.enabled;
            },

            ensureState() {
                if (!this.state || typeof this.state !== 'object') {
                    this.state = {
                        hours: @js(\ZEDMagdy\FilamentBusinessHours\FilamentBusinessHours::getDefaultHours()),
                        exceptions: [],
                        timezone: @js($getDefaultTimezone()),
                        enabled: true,
                    };
                }
                if (!this.state.hours) {
                    this.state.hours = @js(\ZEDMagdy\FilamentBusinessHours\FilamentBusinessHours::getDefaultHours());
                }
                if (!this.state.exceptions) {
                    this.state.exceptions = [];
                }
            },

            isDayEnabled(day) {
                if (!this.state || !this.state.hours) return false;
                const hours = this.state.hours[day];
                return Array.isArray(hours) && hours.length > 0;
            },

            toggleDay(day) {
                this.ensureState();
                if (this.isDayEnabled(day)) {
                    this.state.hours[day] = [];
                } else {
                    this.state.hours[day] = ['09:00-17:00'];
                }
            },

            addTimeSlot(day) {
                this.ensureState();
                if (!Array.isArray(this.state.hours[day])) {
                    this.state.hours[day] = [];
                }
                this.state.hours[day].push('09:00-17:00');
            },

            removeTimeSlot(day, index) {
                this.ensureState();
                this.state.hours[day].splice(index, 1);
            },

            updateTimeRange(day, index, type, value) {
                this.ensureState();
                const current = this.state.hours[day][index] || '09:00-17:00';
                const parts = current.split('-');
                if (type === 'open') {
                    this.state.hours[day][index] = value + '-' + (parts[1] || '17:00');
                } else {
                    this.state.hours[day][index] = (parts[0] || '09:00') + '-' + value;
                }
            },

            getOpenTime(day, index) {
                if (!this.state?.hours?.[day]) return '09:00';
                const range = this.state.hours[day]?.[index] || '09:00-17:00';
                return range.split('-')[0] || '09:00';
            },

            getCloseTime(day, index) {
                if (!this.state?.hours?.[day]) return '17:00';
                const range = this.state.hours[day]?.[index] || '09:00-17:00';
                return range.split('-')[1] || '17:00';
            },

            addException() {
                if (!Array.isArray(this.state.exceptions)) {
                    this.state.exceptions = [];
                }
                this.state.exceptions.push({
                    date: this.newException.date,
                    start: this.newException.start,
                    end: this.newException.end,
                    label: this.newException.label,
                });
                this.newException = { date: '', start: '', end: '', label: '' };
                this.showExceptionForm = false;
            },

            removeException(index) {
                this.state.exceptions.splice(index, 1);
            },

            formatExceptionDisplay(ex) {
                let text = ex.date;
                if (ex.start && ex.end) {
                    text += ' - ' + ex.start + '-' + ex.end;
                } else {
                    text += ' - {{ __('All day') }}';
                }
                return text;
            },
        }"
    >
        <style>
            .bh-card {
                border-radius: 0.75rem;
                border: 1px solid #e5e7eb;
                background: #fff;
                overflow: hidden;
            }
            .dark .bh-card {
                border-color: rgba(255,255,255,0.1);
                background: rgb(17 24 39);
            }
            .bh-header {
                display: flex;
                align-items: center;
                gap: 0.75rem;
                padding: 1rem 1.5rem;
                border-bottom: 1px solid #e5e7eb;
            }
            .dark .bh-header {
                border-color: rgba(255,255,255,0.1);
            }
            .bh-header-icon { color: #9ca3af; flex-shrink: 0; }
            .bh-header-title { font-size: 1rem; font-weight: 600; color: #111827; }
            .dark .bh-header-title { color: #fff; }
            .bh-header-desc { font-size: 0.875rem; color: #6b7280; }
            .bh-body { padding: 1.25rem 1.5rem; }
            .bh-toggle {
                position: relative;
                display: inline-flex;
                height: 1.5rem;
                width: 2.75rem;
                flex-shrink: 0;
                cursor: pointer;
                border-radius: 9999px;
                border: 2px solid transparent;
                transition: background-color 200ms ease-in-out;
                padding: 0;
                background: none;
            }
            .bh-toggle[data-enabled="true"] {
                background-color: rgb(var(--primary-500, 35 158 160));
            }
            .bh-toggle[data-enabled="false"] {
                background-color: #d1d5db;
            }
            .dark .bh-toggle[data-enabled="false"] {
                background-color: #4b5563;
            }
            .bh-toggle-knob {
                pointer-events: none;
                display: inline-block;
                height: 1.25rem;
                width: 1.25rem;
                border-radius: 9999px;
                background: #fff;
                box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                transition: transform 200ms ease-in-out;
            }
            .bh-toggle[data-enabled="true"] .bh-toggle-knob {
                transform: translateX(1.25rem);
            }
            .bh-toggle[data-enabled="false"] .bh-toggle-knob {
                transform: translateX(0);
            }
            .bh-day-row {
                padding: 0.75rem 0;
                display: flex;
                align-items: flex-start;
                gap: 1rem;
            }
            .bh-day-row + .bh-day-row {
                border-top: 1px solid #f3f4f6;
            }
            .dark .bh-day-row + .bh-day-row {
                border-color: rgba(255,255,255,0.05);
            }
            .bh-day-name {
                width: 7rem;
                flex-shrink: 0;
                font-size: 0.875rem;
                font-weight: 500;
                color: #111827;
                margin-top: 0.125rem;
            }
            .dark .bh-day-name { color: #fff; }
            .bh-closed-label {
                display: flex;
                align-items: center;
                gap: 0.5rem;
                color: #9ca3af;
                font-size: 0.875rem;
                margin-top: 0.125rem;
            }
            .bh-time-group {
                display: inline-flex;
                align-items: stretch;
                border-radius: 0.5rem;
                border: 1px solid #d1d5db;
                overflow: hidden;
            }
            .dark .bh-time-group {
                border-color: rgba(255,255,255,0.2);
            }
            .bh-time-prefix {
                display: flex;
                align-items: center;
                padding: 0 0.625rem;
                font-size: 0.75rem;
                font-weight: 500;
                color: #6b7280;
                background: #f9fafb;
                border-right: 1px solid #d1d5db;
            }
            .dark .bh-time-prefix {
                background: rgb(31 41 55);
                border-color: rgba(255,255,255,0.2);
                color: #9ca3af;
            }
            .bh-time-input {
                border: none;
                background: transparent;
                padding: 0.375rem 0.5rem;
                font-size: 0.875rem;
                color: #111827;
                width: 7rem;
                outline: none;
            }
            .dark .bh-time-input { color: #fff; }
            .bh-time-input:focus { outline: none; box-shadow: none; }
            .bh-delete-btn {
                display: flex;
                align-items: center;
                padding: 0.25rem;
                color: #ef4444;
                cursor: pointer;
                background: none;
                border: none;
                flex-shrink: 0;
            }
            .bh-delete-btn:hover { color: #dc2626; }
            .bh-add-time-btn {
                display: inline-flex;
                align-items: center;
                border-radius: 0.5rem;
                border: 1px solid #d1d5db;
                background: #fff;
                padding: 0.375rem 0.75rem;
                font-size: 0.75rem;
                font-weight: 500;
                color: #374151;
                cursor: pointer;
            }
            .dark .bh-add-time-btn {
                border-color: rgba(255,255,255,0.1);
                background: rgb(31 41 55);
                color: #d1d5db;
            }
            .bh-add-time-btn:hover { background: #f9fafb; }
            .dark .bh-add-time-btn:hover { background: rgb(55 65 81); }
            .bh-exception-item {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 0.75rem 1rem;
                border-radius: 0.5rem;
                border: 1px solid #e5e7eb;
                background: #fafafa;
            }
            .dark .bh-exception-item {
                border-color: rgba(255,255,255,0.1);
                background: rgba(31,41,55,0.5);
            }
            .bh-setup-btn {
                display: inline-flex;
                align-items: center;
                border-radius: 0.5rem;
                border: 1px solid #d1d5db;
                background: #fff;
                padding: 0.5rem 1rem;
                font-size: 0.875rem;
                font-weight: 500;
                color: #374151;
                cursor: pointer;
            }
            .dark .bh-setup-btn {
                border-color: rgba(255,255,255,0.1);
                background: rgb(31 41 55);
                color: #d1d5db;
            }
            .bh-setup-btn:hover { background: #f9fafb; }
            .bh-section-title {
                font-size: 1rem;
                font-weight: 600;
                color: #111827;
            }
            .dark .bh-section-title { color: #fff; }
            .bh-form-label {
                display: block;
                font-size: 0.75rem;
                font-weight: 500;
                color: #374151;
                margin-bottom: 0.25rem;
            }
            .dark .bh-form-label { color: #d1d5db; }
            .bh-form-input {
                display: block;
                width: 100%;
                border-radius: 0.5rem;
                border: 1px solid #d1d5db;
                padding: 0.5rem 0.75rem;
                font-size: 0.875rem;
                color: #111827;
                background: #fff;
            }
            .dark .bh-form-input {
                border-color: rgba(255,255,255,0.2);
                background: rgb(31 41 55);
                color: #fff;
            }
            .bh-form-input:focus {
                outline: 2px solid rgb(var(--primary-500, 35 158 160));
                outline-offset: -1px;
            }
            .bh-form-input::placeholder { color: #9ca3af; }
            .dark .bh-form-input::placeholder { color: #6b7280; }
            .bh-btn-primary {
                display: inline-flex;
                align-items: center;
                border-radius: 0.5rem;
                padding: 0.375rem 0.75rem;
                font-size: 0.75rem;
                font-weight: 500;
                color: #fff;
                cursor: pointer;
                border: none;
                background: rgb(var(--primary-500, 35 158 160));
            }
            .bh-btn-primary:disabled { opacity: 0.5; cursor: not-allowed; }
        </style>

        <div class="bh-card">
            {{-- Section Header --}}
            <div class="bh-header">
                <div class="bh-header-icon">
                    <svg style="width:1.25rem;height:1.25rem" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
                <div>
                    <div class="bh-header-title">{{ __('Business Hours') }}</div>
                    <div class="bh-header-desc">{{ __('Manage available hours for each weekday') }}</div>
                </div>
            </div>

            <div class="bh-body">
                <div style="display:flex;flex-direction:column;gap:1.5rem">
                    {{-- Enable Toggle --}}
                    <div style="display:flex;align-items:center;gap:0.75rem">
                        <button
                            type="button"
                            x-on:click="toggleEnabled()"
                            class="bh-toggle"
                            :data-enabled="enabled ? 'true' : 'false'"
                            role="switch"
                            :aria-checked="enabled"
                        >
                            <span class="bh-toggle-knob"></span>
                        </button>
                        <div>
                            <span style="font-size:0.875rem;font-weight:500;color:#111827">{{ __('Enable') }}</span>
                            <p style="font-size:0.75rem;color:#6b7280;margin:0">{{ __('Quickly enable or disable business hours') }}</p>
                        </div>
                    </div>

                    <div x-show="enabled" x-collapse>
                        <div style="display:flex;flex-direction:column;gap:1.5rem">
                            {{-- Timezone Selector --}}
                            @if($hasTimezone())
                                <div>
                                    <label class="bh-form-label" style="font-size:0.875rem;margin-bottom:0.375rem">
                                        {{ __('Timezone') }}
                                    </label>
                                    <select x-on:change="ensureState(); state.timezone = $event.target.value"
                                        x-effect="if (state?.timezone) $el.value = state.timezone" class="bh-form-input" style="padding-right:2rem">
                                        @foreach($getTimezoneOptions() as $tz => $label)
                                            <option value="{{ $tz }}">({{ $tz }}) {{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            {{-- Days of the Week --}}
                            <div>
                                @foreach($getDays() as $day)
                                    <div class="bh-day-row">
                                        {{-- Toggle --}}
                                        <button
                                            type="button"
                                            x-on:click="toggleDay('{{ $day->value }}')"
                                            class="bh-toggle"
                                            :data-enabled="isDayEnabled('{{ $day->value }}') ? 'true' : 'false'"
                                            role="switch"
                                            :aria-checked="isDayEnabled('{{ $day->value }}')"
                                            style="margin-top:0.125rem"
                                        >
                                            <span class="bh-toggle-knob"></span>
                                        </button>

                                        {{-- Day Name --}}
                                        <span class="bh-day-name">{{ $day->label() }}</span>

                                        {{-- Content --}}
                                        <div style="flex:1;min-width:0">
                                            {{-- Closed State --}}
                                            <template x-if="!isDayEnabled('{{ $day->value }}')">
                                                <div class="bh-closed-label">
                                                    <svg style="width:1rem;height:1rem" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />
                                                    </svg>
                                                    <span>{{ __('Closed') }}</span>
                                                </div>
                                            </template>

                                            {{-- Open State --}}
                                            <template x-if="isDayEnabled('{{ $day->value }}')">
                                                <div style="display:flex;flex-direction:column;gap:0.5rem">
                                                    <template x-for="(range, slotIndex) in (state?.hours?.['{{ $day->value }}'] || [])" :key="'{{ $day->value }}-' + slotIndex">
                                                        <div style="display:flex;align-items:center;gap:0.5rem">
                                                            <div class="bh-time-group">
                                                                <span class="bh-time-prefix">{{ __('From') }}</span>
                                                                <input
                                                                    type="time"
                                                                    :value="getOpenTime('{{ $day->value }}', slotIndex)"
                                                                    x-on:change="updateTimeRange('{{ $day->value }}', slotIndex, 'open', $event.target.value)"
                                                                    class="bh-time-input"
                                                                />
                                                            </div>

                                                            <div class="bh-time-group">
                                                                <span class="bh-time-prefix">{{ __('To') }}</span>
                                                                <input
                                                                    type="time"
                                                                    :value="getCloseTime('{{ $day->value }}', slotIndex)"
                                                                    x-on:change="updateTimeRange('{{ $day->value }}', slotIndex, 'close', $event.target.value)"
                                                                    class="bh-time-input"
                                                                />
                                                            </div>

                                                            <button
                                                                type="button"
                                                                x-on:click="removeTimeSlot('{{ $day->value }}', slotIndex)"
                                                                class="bh-delete-btn"
                                                            >
                                                                <svg style="width:1.25rem;height:1.25rem" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </template>

                                                    <div style="display:flex;justify-content:center;padding-top:0.25rem">
                                                        <button
                                                            type="button"
                                                            x-on:click="addTimeSlot('{{ $day->value }}')"
                                                            class="bh-add-time-btn"
                                                        >
                                                            {{ __('Add time to :day', ['day' => $day->label()]) }}
                                                        </button>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Exceptions --}}
                            @if($hasExceptions())
                                <div style="border-top:1px solid #e5e7eb;padding-top:1.25rem">
                                    <div class="bh-section-title" style="margin-bottom:1rem">{{ __('Exceptions') }}</div>

                                    {{-- Exception List --}}
                                    <div style="display:flex;flex-direction:column;gap:0.5rem;margin-bottom:1rem">
                                        <template x-for="(exception, exIndex) in (state?.exceptions || [])" :key="'ex-' + exIndex">
                                            <div class="bh-exception-item">
                                                <span style="font-size:0.875rem;color:#6b7280" x-text="formatExceptionDisplay(exception)"></span>
                                                <div style="display:flex;align-items:center;gap:0.75rem">
                                                    <span style="font-size:0.875rem;color:#9ca3af" x-text="exception.label || '{{ __('Closed') }}'"></span>
                                                    <button
                                                        type="button"
                                                        x-on:click="removeException(exIndex)"
                                                        class="bh-delete-btn"
                                                    >
                                                        <svg style="width:1.25rem;height:1.25rem" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </template>
                                    </div>

                                    {{-- Add Exception Form --}}
                                    <div x-show="showExceptionForm" x-collapse style="margin-bottom:1rem">
                                        <div style="border-radius:0.5rem;border:1px solid #e5e7eb;background:#fafafa;padding:1rem;display:flex;flex-direction:column;gap:0.75rem">
                                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem">
                                                <div>
                                                    <label class="bh-form-label">{{ __('Date or range') }}</label>
                                                    <input
                                                        type="text"
                                                        x-model="newException.date"
                                                        placeholder="12-25, 2026-12-25, 06-25 to 07-01"
                                                        class="bh-form-input"
                                                    />
                                                </div>
                                                <div>
                                                    <label class="bh-form-label">{{ __('Label / Note') }}</label>
                                                    <input
                                                        type="text"
                                                        x-model="newException.label"
                                                        placeholder="{{ __('e.g. Closed for Christmas') }}"
                                                        class="bh-form-input"
                                                    />
                                                </div>
                                            </div>
                                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem">
                                                <div>
                                                    <label class="bh-form-label">{{ __('Start time (empty = all day)') }}</label>
                                                    <div class="bh-time-group" style="width:100%">
                                                        <span class="bh-time-prefix">{{ __('From') }}</span>
                                                        <input
                                                            type="time"
                                                            x-model="newException.start"
                                                            class="bh-time-input"
                                                            style="width:100%"
                                                        />
                                                    </div>
                                                </div>
                                                <div>
                                                    <label class="bh-form-label">{{ __('End time (empty = all day)') }}</label>
                                                    <div class="bh-time-group" style="width:100%">
                                                        <span class="bh-time-prefix">{{ __('To') }}</span>
                                                        <input
                                                            type="time"
                                                            x-model="newException.end"
                                                            class="bh-time-input"
                                                            style="width:100%"
                                                        />
                                                    </div>
                                                </div>
                                            </div>
                                            <div style="display:flex;align-items:center;gap:0.75rem">
                                                <button
                                                    type="button"
                                                    x-on:click="addException()"
                                                    :disabled="!newException.date"
                                                    class="bh-btn-primary"
                                                >
                                                    {{ __('Add') }}
                                                </button>
                                                <button
                                                    type="button"
                                                    x-on:click="showExceptionForm = false; newException = { date: '', start: '', end: '', label: '' }"
                                                    class="bh-add-time-btn"
                                                >
                                                    {{ __('Cancel') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Set Up Row --}}
                                    <div style="display:flex;align-items:center;justify-content:space-between;border-top:1px solid #e5e7eb;padding-top:1rem">
                                        <p style="font-size:0.875rem;color:#6b7280;margin:0">{{ __('Create exceptions for dates that should be closed') }}</p>
                                        <button
                                            type="button"
                                            x-show="!showExceptionForm"
                                            x-on:click="showExceptionForm = true"
                                            class="bh-setup-btn"
                                        >
                                            {{ __('Set up') }}
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-dynamic-component>
