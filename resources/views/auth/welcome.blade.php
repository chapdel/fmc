<x-mailcoach::layout-auth :title="__('Welcome')">
    <h1 class="markup-h2">{{ __('Welcome') }}</h1>

    <form class="form-grid" method="POST">
        @csrf

        <input type="hidden" name="email" value="{{ $user->email }}"/>

        <div class="form-field">
            @error('password')
            <p class="form-error" role="alert">
                {{ $message }}
            </p>
            @enderror

            <label for="password" class="label">{{ __('Password') }}</label>

            <input id="password" type="password" class="input @error('password') is-invalid @enderror"
                   name="password" required autocomplete="new-password">
        </div>

        <div class="form-field">
            @error('password_confirmation')
            <p class="form-error" role="alert">
                {{ $message }}
            </p>
            @enderror

            <label for="password_confirmation" class="label">{{ __('Confirm Password') }}</label>

            <input id="password_confirmation" type="password" class="input @error('password_confirmation') is-invalid @enderror"
                   name="password_confirmation" required autocomplete="new-password">
        </div>

        <x-mailcoach::form-buttons>
            <x-mailcoach::button :label="__('Save password and login')" />
        </x-mailcoach::form-buttons>
    </form>
</x-mailcoach::layout-app>
