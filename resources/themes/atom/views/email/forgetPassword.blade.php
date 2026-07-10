<h1>{{ __('Reset your password') }}</h1>

{{ __('Click the link to reset your password:') }}
<a href="{{ route('reset.password.get', $token) }}">{{ __('Reset Password') }}</a>