@component('mail::message')
# Hello!

You are receiving this email because you are registered as new user at {{ config('app.name') }}.


Your User Details:

Name: {{ $details['name'] }}

Email: {{ $details['email'] }}

Telephone: {{ $details['telephone'] }}

Mobile: {{ $details['mobile'] }}

Jurisdiction: {{ $details['jurisdiction'] }}

Role: {{ $details['role'] }}

Password: {{ $details['password'] }}


@component('mail::button', ['url' => config('app.url')])
Go to Login Page
@endcomponent

Regards,<br>
{{ config('app.name') }}
@endcomponent
