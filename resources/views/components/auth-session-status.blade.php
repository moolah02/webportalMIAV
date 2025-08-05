@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'success-message']) }}>
        {{ $status }}
    </div>
@endif

