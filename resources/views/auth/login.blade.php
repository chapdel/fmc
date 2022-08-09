<x-mailcoach::layout-auth :title="__('Log in')">
    <h1 class="markup-h2">{{ __('Log in') }}</h1>

    <form class="form-grid" method="POST" action="{{ route('mailcoach.login') }}">
        @csrf

        <p>
            <a class="link" href="{{ route('mailcoach.forgot-password') }}">
                {{ __('Forgot password?') }}
            </a>
        </p>

        <div class="form-field">
            @error('email')
                <p class="form-error" role="alert">
                {{ $message }}
                </p>
            @enderror

            <label for="email" class="label">{{ __('Email') }}</label>

            <input id="email" type="email" class="input @error('email') is-invalid @enderror" name="email"
                    value="{{ old('email') }}" required autocomplete="email" autofocus>
        </div>

        <div class="form-field">
            @error('password')
                <p class="form-error" role="alert">
                {{ $message }}
                </p>
            @enderror

            <label for="password" class="label">{{ __('Password') }}</label>

            <input id="password" type="password" class="input @error('password') is-invalid @enderror"
                name="password" required autocomplete="current-password">
        </div>

        <div class="form-field">
            <label class="checkbox-label" for="remember">
                <input class="checkbox" type="checkbox" name="remember" id="remember"
                    {{ old('remember') ? 'checked' : '' }}>

                    {{ __('Remember me next time') }}
            </label>
        </div>

        <x-mailcoach::form-buttons>
            <x-mailcoach::button :label="__('Log in')" />

            @if (Route::has('mailcoach.forgot-password'))
            <a class="link" href="{{ route('mailcoach.forgot-password') }}">
                {{ __('Forgot Your Password?') }}
            </a>
            @endif
        </x-mailcoach::form-buttons>
    </form>
</x-mailcoach::layout-app>
