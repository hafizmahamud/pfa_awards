@component('mail::message')
# Hello!

You are receiving this email because we received a password reset request for your account.

Your new password is : {{ $rPassword }}

@component('mail::button', ['url' => '/login'])
Button Text
@endcomponent

Regards,<br>
{{ config('app.name') }}
@endcomponent
