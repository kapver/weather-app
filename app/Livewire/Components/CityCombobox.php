<?php

namespace app\Livewire\Components;

use App\Models\City;
use Illuminate\View\View;
use Livewire\Attributes\Modelable;
use Livewire\Component;

class CityCombobox extends Component
{
    /**
     * The list of options available in the combobox.
     *
     * @var array
     */
    public $options = [];

    /**
     * The list of selected option values.
     *
     * @var array
     */
    #[Modelable]
    public $selected = [];
    
    /**
     * Search for cities based on the provided term and update the options.
     *
     * @param string $term The search term input.
     * @return void
     */
    public function search(string $term): void
    {
        $term = strtolower($term);
        $results = City::query()->whereRaw('LOWER(name) like ?', ["%{$term}%"])
            ->where('type', 'primary')
            ->limit(100)
            ->orderBy('name')
            ->get();

        $preserve = collect($this->options)->filter(function ($option) {
            return in_array($option['value'], $this->selected);
        })->unique();

        $this->options = collect($results)
            ->map(fn($item) => [
                'label' => $item->name,
                'value' => $item->id
            ])
            ->merge($preserve)
            ->unique();

        $this->dispatch('select-options-updated', $this->options);
    }
    

    /**
     * Render the CityCombobox component view.
     *
     * @return View
     */
    public function render(): View
    {
        return view('livewire.components.city-combobox');
    }
}
