@component('mail::message')
# {{ $details->greetings }}

<div style="text-align: center; margin-bottom: 20px;">
<span style="font-weight: bold;">{{ $details->date_range }}</span>
</div>


@foreach ($details->departments as $department_name => $department)

<div style="text-align: center;">
<span style="font-weight: bold;">{{ $department_name }} Department</span>
</div>

@component('mail::table')
| Attendance Type       |Monday                                  |Tuesday                                   |Wednesday                                   |Thursday                                   |Friday                                   |Saturday                                   |
| --------------------- |:--------------------------------------:|:----------------------------------------:|:------------------------------------------:|:-----------------------------------------:|:---------------------------------------:|:-----------------------------------------:|
@foreach ($department as $attendance_type => $attendance)
@php
$monday = $attendance->monday ?? (object)[];
$tuesday = $attendance->tuesday ?? (object)[];
$wednesday = $attendance->wednesday ?? (object)[];
$thursday = $attendance->thursday ?? (object)[];
$friday = $attendance->friday ?? (object)[];
$saturday = $attendance->saturday ?? (object)[];
@endphp
|{{ $attendance_type }}|{{ $monday->attendance_type_count ?? 0 }}|{{ $tuesday->attendance_type_count ?? 0 }}|{{ $wednesday->attendance_type_count ?? 0 }}|{{ $thursday->attendance_type_count ?? 0 }}|{{ $friday->attendance_type_count ?? 0 }}|{{ $saturday->attendance_type_count ?? 0 }}|
@endforeach
@endcomponent

@endforeach


{{-- @component('mail::button', ['url' => ''])
Button Text
@endcomponent --}}

Regards,<br>
{{ config('app.name') }}
@endcomponent