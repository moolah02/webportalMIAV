<!-- Updated primary-button.blade.php -->
<button {{ $attributes->merge(['type' => 'submit', 'class' => 'login-button']) }}>
    {{ $slot }}
</button>