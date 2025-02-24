<x-form-section submit="updateWeatherSettings">
    <x-slot name="title">
        {{ __('Weather notifications') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Enable weather notifications, set city and conditions threshold information.') }}
    </x-slot>

    <x-slot name="form">
        <!-- Enable Notifications Section -->
        <div class="col-span-4 sm:col-span-4 flex items-center gap-2">
            <x-input id="alert_enabled" type="checkbox" wire:model.live="state.alert_enabled"/>
            <x-label for="alert_enabled" value="{{ __('Enable notifications') }}"/>
            <x-input-error for="alert_enabled" class="mt-2"/>
        </div>

        <!-- Pause Notifications Section -->
        <div class="col-span-4 sm:col-span-4">
            @if (!$state['pause_enabled'] || $state['pause_enabled'] < now()->toDateTimeString())
                <div>
                    <label for="dropdown">{{ __('Pause for ') }}</label>
                    <select id="dropdown" wire:model.live="state.pause_enabled" class="border rounded p-2">
                        <option value="">{{ __('Hours') }}</option>
                        @foreach ($pause_options as $option_value => $option_title)
                            <option value="{{ $option_value }}">{{ $option_title }}</option>
                        @endforeach
                    </select>
                </div>
            @else
                <div>
                    <p class="mt-2 text-gray-600">
                        {{ __('Paused until:') }} {{ \Carbon\Carbon::parse($state['pause_enabled'])->format('D M d, Y H:i') }}
                    </p>
                    <x-button wire:click.prevent="resumeNotifications">
                        {{ __('Resume notifications') }}
                    </x-button>
                </div>
            @endif
        </div>


        <!-- Cities Dropdown -->
        <div class="col-span-4 sm:col-span-4">
            <label for="cities">{{ __('Alert for ') }}</label>
            <livewire:settings.select
                    id="cities"
                    :options="$cities_options"
                    :selected="$cities_selected"
                    wire:model="cities_selected"
            />
        </div>

        <!-- Notifications channels -->
        <div class="col-span-4 sm:col-span-4">
            <div class="col-span-4 sm:col-span-4 flex items-center gap-2">
                <x-input id="email_enabled" type="checkbox" wire:model.live="state.email_enabled"/>
                <x-label for="email_enabled" value="{{ __('Notify via email') }}"/>
                <x-input-error for="email_enabled" class="mt-2"/>
            </div>
            <div class="col-span-4 sm:col-span-4 flex items-center gap-2">
                @if($state['telegram_chat_id'])
                    <x-input id="telegram_enabled" type="checkbox" wire:model.live="state.telegram_enabled"/>
                    <x-label for="telegram_enabled" value="{{ __('Notify via telegram') }}"/>
                    <x-input-error for="telegram_enabled" class="mt-2"/>

                    <x-button wire:click.prevent="unlinkTelegram">
                        {{ __('Unlink Telegram') }}
                    </x-button>
                @else
                    @php
                        $telegram_link = "https://t.me/UpdaterAppBot?start={$state['telegram_verification_code']}";
                    @endphp

                    <x-button type="button" onclick="window.open('{{ $telegram_link }}', '_blank')">
                        {{ __('Link Telegram') }}
                    </x-button>
                @endif
            </div>
        </div>


        <!-- Precipitation Alert Dropdown -->
        <div class="col-span-4 sm:col-span-4 flex flex-col">
            <label for="pop_threshold">{{ __('Alert for precipitation on') }}</label>
            <select id="pop_threshold" wire:model.live="state.pop_threshold" class="border rounded p-2">
                <option value="">{{ __('Choose') }}</option>
                @foreach ($pop_options as $option_value => $option_title)
                    <option value="{{ $option_value }}">{{ $option_title }}</option>
                @endforeach
            </select>
        </div>

        <!-- UV Index Alert Dropdown -->
        <div class="col-span-4 sm:col-span-4 flex flex-col">
            <label for="uvi_threshold">{{ __('Alert for UV Index on') }}</label>
            <select id="uvi_threshold" wire:model.live="state.uvi_threshold" class="border rounded p-2">
                <option value="">{{ __('Choose') }}</option>
                @foreach ($uvi_options as $option_value => $option_title)
                    <option value="{{ $option_value }}">{{ $option_title }}</option>
                @endforeach
            </select>
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-action-message class="me-3" on="saved">
            {{ __('Saved.') }}
        </x-action-message>

        <x-button wire:loading.attr="disabled" wire:target="photo">
            {{ __('Save') }}
        </x-button>
    </x-slot>
</x-form-section>