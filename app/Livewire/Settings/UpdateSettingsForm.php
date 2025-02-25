<?php

declare(strict_types=1);

namespace App\Livewire\Settings;

use App\Services\Weather\WeatherService;
use Illuminate\Support\Str;
use Livewire\Component;
use Illuminate\View\View;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Log;

use App\Enums\WeatherPopConditionsEnum;
use App\Enums\WeatherUviConditionsEnum;
use App\Enums\WeatherPauseConditionEnum;
use App\Services\Weather\WeatherSettings;

class UpdateSettingsForm extends Component
{
    use WithFileUploads;

    /**
     * The component's state.
     *
     * @var array
     */
    public array $state = [];

    public array $cities = [];

    /**
     * Prepare the component.
     *
     * @param WeatherSettings $weatherSettings
     * @return void
     */
    public function mount(WeatherSettings $weatherSettings): void
    {
        $this->state = $weatherSettings->getSettings();

        if (!$this->state['telegram_verification_code']) {
            $code = Str::ulid()->toString();
            $this->state['telegram_verification_code'] = $code;
            $weatherSettings->saveSettings(['telegram_verification_code' => $code]);
        }

        $this->cities = auth()->user()->cities->pluck('id')->toArray();
    }

    /**
     * Updates the user's settings
     */
    public function updateWeatherSettings(WeatherSettings $weatherSettings): void
    {
        $this->resetErrorBag();

        if ($this->state['pause_enabled']) {
            $timestamp = now()->addHours((int) $this->state['pause_enabled'])->toDateTimeString();
            $this->state['pause_enabled'] = $timestamp;
        }

        $weatherSettings->saveSettings($this->state);
        auth()->user()->cities()->sync($this->cities);

        $this->dispatch('saved');
    }

    public function testNotifications(WeatherService $weatherService): void
    {
        $weatherService->process();

        $this->dispatch('sent');
    }

    /**
     * TODO add settings keys validation
     *
     * @param WeatherSettings $weatherSettings
     * @param mixed $value
     * @param string $key
     * @return void
     */
    public function updatedState(WeatherSettings $weatherSettings, mixed $value, ?string $key): void
    {
        if ($key === 'pause_enabled') {
            $timestamp = now()->addHours((int) $value)->toDateTimeString();
            $this->state['pause_enabled'] = $timestamp;
        }

        $weatherSettings->saveSettings($this->state);

        $this->dispatch('saved');
    }

    public function resumeNotifications(WeatherSettings $weatherSettings): void
    {
        $this->state['pause_enabled'] = null;

        $weatherSettings->saveSettings($this->state);

        $this->dispatch('saved');
    }

    public function unlinkTelegram(WeatherSettings $weatherSettings): void
    {
        $this->state['telegram_enabled'] = false;
        $this->state['telegram_chat_id'] = null;
        $this->state['telegram_verification_code'] = null;

        $weatherSettings->saveSettings($this->state);

        $this->dispatch('saved');
    }

    /**
     * Render the component.
     *
     * @return View
     */
    public function render(): View
    {
        $cities_options = auth()->user()->cities->map(fn($item) => [
            'label' => $item->name,
            'value' => $item->id
        ])->toArray();

        return view('settings.update-settings-form')->with([
            'pause_options' => WeatherPauseConditionEnum::getOptions(),
            'pop_options' => WeatherPopConditionsEnum::getOptions(),
            'uvi_options' => WeatherUviConditionsEnum::getOptions(),
            'cities_options' => $cities_options,
        ]);
    }
}
