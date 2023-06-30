@php
    $button = $details['button'];
    $button_route = $button->route;
    $button_label = $button->label;
@endphp

@component('mail::message')
# {{ $details['greetings'] }}

@if(!is_null($details['from'][0]))
<div>
<span style="font-size: 13px;">{{ $details['from'][0] }}</span>
<span style="font-size: 15px; font-weight: bolder;">{{ $details['from'][1] }}</span>
</div>
<div>
<span style="font-size: 13px;">Email:</span>
<span style="font-size: 15px; font-weight: bolder;">{{ $details['email'] }}</span>
</div>
@endif

<div style="margin-top: 20px; text-align: center;">
<span style="font-size: 25px; font-weight: bolder; color: {{ $details['leave-request']->getStatusColor() }};">
{{ strtoupper($details['leave-request']->status) }}
</span><br>
<span>Leave Type:</span><br>
<span style="font-size: 15px; font-weight: bold;">{{ $details['leave-type'] }}</span><br>
<span>Date Range:</span><br>
<span style="font-size: 15px; font-weight: bold;">{{ $details['date-range'] }}</span>
</div>
@if (isset($details['leave-reason']))
<br><span>Leave Reason:</span><br><br>
<div style="font-size: 15px; font-weight: bold; text-indent: 50px; text-align: justify;">{{ $details['leave-reason'] }}</div><br>
@endif
@component('mail::button', ['url' => $button_route])
{{ $button_label }}
@endcomponent

Regards,<br>
{{ config('app.name') }}
@endcomponent