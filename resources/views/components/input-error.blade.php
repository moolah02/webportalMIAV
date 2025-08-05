@props(['messages'])

@if ($messages)
    <div class="error-message">
        @foreach ((array) $messages as $message)
            <div>{{ $message }}</div>
        @endforeach
    </div>
@endif