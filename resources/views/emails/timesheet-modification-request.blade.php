
@component('mail::message')
# {{ $details['greetings'] }}

<div>
<span style="font-size: 13px;">{{ $details['from'][0] }}</span>
<span style="font-size: 15px; font-weight: bolder;">{{ $details['from'][1] }}</span>
</div>
<div>
<span style="font-size: 13px;">Email:</span>
<span style="font-size: 15px; font-weight: bolder;">{{ $details['email'] }}</span>
</div>

<div style="margin-top: 15px; margin-left: 55px;">
<span>Modification Request ID:</span>
<span style="font-weight: bold;">{{ $details['id'] }}</span>
</div>
<div style="margin-left: 55px;">
<span>Timesheet Date:</span>
<span style="font-weight: bold;">{{ $details['timesheet-date'] }}</span>
</div>
@component('mail::table')
@if ($details['type'] === 'edit')
|                   |Actual                             |Modification                       |
| ----------------- |:---------------------------------:|:---------------------------------:|
| Clock In          |{{ $details['time-in'][0] }}       |{{ $details['time-in'][1] }}       |
| Clock Out         |{{ $details['time-out'][0] }}      |{{ $details['time-out'][1] }}      |
| Lunch Break Start |{{ $details['lunch-start'][0] }}   |{{ $details['lunch-start'][1] }}   |
| Lunch Break End   |{{ $details['lunch-end'][0] }}     |{{ $details['lunch-end'][1] }}     |
@if(!is_null($details['overtime-start'][1]))
| Overtime Start    |{{ $details['overtime-start'][0] }}|{{ $details['overtime-start'][1] }}|
| Overtime End      |{{ $details['overtime-start'][0] }}|{{ $details['overtime-end'][1] }}  |
@endif
@else
|                   |Time                            |
| ----------------- |:------------------------------:|
| Clock In          |{{ $details['time-in'] }}       |
| Clock Out         |{{ $details['time-out'] }}      |
| Lunch Break Start |{{ $details['lunch-start'] }}   |
| Lunch Break End   |{{ $details['lunch-end'] }}     |
@if(!is_null($details['overtime-start']))
| Overtime Start    |{{ $details['overtime-start'] }}|
| Overtime End      |{{ $details['overtime-end'] }}  |
@endif
@endif
@endcomponent
<div style="margin-left: 50px; margin-right: 50px; background-color: rgba(128, 128, 128, 0.055); border-radius: 10px; ">
<p style="font-size: 15px; text-indent: 50px; margin-top: 25px; margin-bottom: 25px;">
{{ $details['notes'] }}
</p>
</div>
<div style="margin-left: 55px; margin-bottom: 20px;">
<span>Date Requested:</span>
<span style="font-weight: bold;">{{ $details['date-request'] }}</span>
</div>

@component('mail::button', ['url' => $details['button']->route])
{{ $details['button']->label }}
@endcomponent

Regards,<br>
{{ config('app.name') }}
@endcomponent