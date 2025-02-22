<?php

declare(strict_types=1);

namespace App\Livewire\Settings;

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

    public array $cities_options = [];

    public array $cities_selected = [];

    /**
     * Prepare the component.
     *
     * @return void
     */
    public function mount(WeatherSettings $weatherSettings): void
    {
        $cities = auth()->user()->cities;

        $this->state = $weatherSettings->getSettings();
        $this->cities_selected = $cities->pluck('id')->toArray();
        $this->cities_options = $cities->map(fn($item) => [
            'label' => $item->name,
            'value' => $item->id
        ])->toArray();
    }

    /**
     * Updates the user's settings
     */
    public function updateWeatherSettings(WeatherSettings $weatherSettings): void
    {
        $this->resetErrorBag();

        $weatherSettings->saveSettings($this->state);
        auth()->user()->cities()->sync($this->cities_selected);

        $this->dispatch('saved');
    }

    /**
     * TODO add settings keys validation
     *
     * @param WeatherSettings $weatherSettings
     * @param mixed $value
     * @param string $key
     * @return void
     */
    public function updatedState(WeatherSettings $weatherSettings, mixed $value, string $key): void
    {
        if ($key === 'pause_enabled') {
            $this->state['pause_enabled'] = now()->nowWithSameTz()->addHours($value);
        }
        $weatherSettings->saveSettings($this->state);
        Log::info('Updated state:', ['state' => $this->state]);
        $this->dispatch('saved');
    }

    public function resumeNotifications(WeatherSettings $weatherSettings): void
    {
        $this->state['pause_enabled'] = null;
        $weatherSettings->saveSettings($this->state);
    }

    /**
     * Render the component.
     *
     * @return View
     */
    public function render(): View
    {
        return view('settings.update-settings-form')->with([
            'pause_options' => WeatherPauseConditionEnum::getOptions(),
            'pop_options' => WeatherPopConditionsEnum::getOptions(),
            'uvi_options' => WeatherUviConditionsEnum::getOptions(),
        ]);
    }
}
