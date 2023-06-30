
@component('mail::message')
# {{ $details->greetings }}

<div style="margin-top: 40px; text-align: center;">
<span style="font-size: 25px; font-weight: bolder; color: green;">REMINDER!</span><br>
<span style="font-size: 20px; font-weight: bolder;">{{ $details->message }}</span><br>
</div>

@component('mail::button', ['url' => route('bundy')])
Go to Bundy
@endcomponent

Regards,<br>
{{ config('app.name') }}
@endcomponent