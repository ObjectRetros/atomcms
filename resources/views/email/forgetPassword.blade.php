<!doctype html>
<html lang="{{ app()->getLocale() }}"><body>
<p>{{ __('You requested a password reset.') }}</p>
<p><a href="{{ $resetUrl }}">{{ __('Reset your password') }}</a></p>
<p>{{ __('If you did not request this, you can ignore this email.') }}</p>
</body></html>
