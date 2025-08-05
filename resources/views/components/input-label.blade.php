<!-- Updated input-label.blade.php -->
@props(['value'])

<label {{ $attributes->merge(['class' => 'form-label']) }}>
    {{ $value ?? $slot }}
</label>

<!-- Updated text-input.blade.php -->
@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'form-input']) }}>
