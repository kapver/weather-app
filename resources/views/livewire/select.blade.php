@once
    @push('css')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    @endpush
    @push('js')
        <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    @endpush
@endonce

@props([
 'options' => [],
 'selected' => [],
])

{{--{{ dd($options, $selected) }}--}}

<div style="width: 300px" wire:ignore x-data="{
    options: {{ json_encode($options) }},
    selected: $wire.entangle('selected'),
    debounce: null,
  }" x-init="() => {
  $nextTick(() => {
      const choices = new Choices($refs.select, {
        removeItems: true,
        removeItemButton: true,
        duplicateItemsAllowed: false,
     });

     const refreshChoices = () => {
       choices.clearStore()
       choices.setChoices(options.map(({ value, label }) => {
            return {
             value,
             label,
             selected: selected.includes(value),
           };
       }))
     }

     $refs.select.addEventListener('change', () => {
       selected = choices.getValue(true)
     })

     $refs.select.addEventListener('search', async (e) => {
       if (e.detail.value) {
         clearTimeout(debounce)
         debounce = setTimeout(() => {
           $wire.call('search', e.detail.value)
         }, 300)
       }
     })

     $wire.on('select-options-updated', (items) => {
       options = items[0];
     })

     $watch('selected', () => refreshChoices());
     $watch('options', () => refreshChoices());

     refreshChoices();
   })
  }">
    <label for="select"></label>
    <select id="select" x-ref="select" multiple></select>
</div>