@php
    $modification = $details['modif-req'];
    $status_type = strtoupper($modification->status);
    $status_color = $modification->getStatusColor();
    $timesheet_date = date_format(date_create($modification->timesheet_date), 'l, F j, Y');
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
<span style="font-size: 25px; font-weight: bolder; color: {{ $status_color }};">
{{ $status_type }}
</span><br>
<span>Timesheet Date:</span><br>
<span style="font-size: 20px; font-weight: bold;">{{ $timesheet_date }}</span>
</div>

@component('mail::button', ['url' => $button_route])
{{ $button_label }}
@endcomponent

Regards,<br>
{{ config('app.name') }}
@endcomponent