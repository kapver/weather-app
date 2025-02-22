<?php

namespace App\Livewire\Settings;

use App\Models\City;
use Livewire\Attributes\Modelable;
use Livewire\Component;

class Select extends Component
{
    public $options = [];

    #[Modelable]
    public $selected = [];

    public function render()
    {
        return view('livewire.select');
    }

    public function search(string $term): void
    {
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
}
