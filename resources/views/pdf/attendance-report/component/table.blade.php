<h4>EMPLOYEES TOTAL WORK HOURS</h4>
<h5>{{ $report['date-range'][0] . ' - '  . $report['date-range'][1] }}</h5>
<table class="report" cellspacing="3px" cellpadding="10px">
    <thead>
        <tr>
            <th class="avatar" style="width: 20;"></th>
            <th>Name</th>
            <th>Department</th>
            <th>Position</th>
            <th>Employment Type</th>
            <th>Timesheet Date</th>
            <th>Clock In</th>
            <th>Clock Out</th>
            <th>Lunch Start</th>
            <th>Lunch End</th>
            <th>Overtime Start</th>
            <th>Overtime End</th>
            <th>Total Work Hours</th>
            <th>Note</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($report['records'] as $record)
            @php
                $initialRow = true;
                // timesheetCount should be atleast x >= 2
                $timesheetCount = count($record['timesheets']);
            @endphp
            @foreach ($record['timesheets'] as $date => $timesheet)
                @if (isset($timesheet['day-total-hrs']))
                    @if ($initialRow)
                        <tr>
                            <td class="avatar" style="border-bottom: 0;">
                                <img style="width: 20px;" src="{{ $record['employee']->user->getAvatar() }}">
                            </td>
                            <td style="border-bottom: 0; border-top: 0;">
                                {{ $record['employee']->getFullName() }}
                            </td>
                            <td style="border-bottom: 0; border-top: 0;">{{
                                $record['employee']->department ? $record['employee']->department->department : ''
                            }}</td>
                            <td style="border-bottom: 0; border-top: 0;">{{
                                $record['employee']->position ? $record['employee']->position->position : ''
                            }}</td>
                            <td style="border-bottom: 0; border-top: 0;">{{
                                $record['employee']->employment_type
                            }}</td>

                            <td>{{ $date }}</td>
                            <td>{{ $timesheet['formatted-clock']['time-in'] }}</td>
                            <td>{{ $timesheet['formatted-clock']['time-out'] }}</td>
                            <td>{{ $timesheet['formatted-clock']['lunch-start'] }}</td>
                            <td>{{ $timesheet['formatted-clock']['lunch-end'] }}</td>
                            <td>{{ $timesheet['formatted-clock']['overtime-start'] }}</td>
                            <td>{{ $timesheet['formatted-clock']['overtime-end'] }}</td>
                            <td>
                                <div>
                                    <div>{{ $timesheet['day-total-hrs'][0] ?? null }}</div>
                                    <div>{{ $timesheet['day-total-hrs'][1] ?? null }}</div>
                                </div>
                            </td>
                            <td>{{ $timesheet['note'] }}</td>
                        </tr>
                        @php
                            $initialRow = false;
                        @endphp
                    @else
                        <tr>
                            <td class="avatar" style="border-bottom: 0; border-top: 0;"></td>
                            <td style="border-bottom: 0; border-top: 0;"></td>
                            <td style="border-bottom: 0; border-top: 0;"></td>
                            <td style="border-bottom: 0; border-top: 0;"></td>
                            <td style="border-bottom: 0; border-top: 0;"></td>

                            <td>{{ $date }}</td>
                            <td>{{ $timesheet['formatted-clock']['time-in'] }}</td>
                            <td>{{ $timesheet['formatted-clock']['time-out'] }}</td>
                            <td>{{ $timesheet['formatted-clock']['lunch-start'] }}</td>
                            <td>{{ $timesheet['formatted-clock']['lunch-end'] }}</td>
                            <td>{{ $timesheet['formatted-clock']['overtime-start'] }}</td>
                            <td>{{ $timesheet['formatted-clock']['overtime-end'] }}</td>
                            <td>
                                <div>
                                    <div>{{ $timesheet['day-total-hrs'][0] ?? null }}</div>
                                    <div>{{ $timesheet['day-total-hrs'][1] ?? null }}</div>
                                </div>
                            </td>
                            <td>{{ $timesheet['note'] }}</td>
                        </tr>
                    @endif
                @endif
            @endforeach
            {{-- when no timesheet exist --}}
            @if ($timesheetCount == 1)
                {{-- <tr>
                    <td class="avatar">
                        <img style="width: 20px;" src="{{ $record['employee']->user->getAvatar() }}">
                    </td>
                    <td>{{ $record['employee']['first_name'] . ' ' . $record['employee']['last_name'] }}</td>
                    <td>full time</td>
                    <td colspan="10">NO TIMESHEET</td>
                </tr> --}}
            {{-- when there is timesheet, show total hours --}}
            @else
                <tr>
                    <td class="avatar" style="border-right: 0;"></td>
                    <td colspan="11" style="border-left: 0;"></td>
                    <td class="w-28" style="vertical-align: middle;">
                        <div class="overall-total">
                            <div style="font-weight: bolder">Total</div>
                            <div>{{ $record['timesheets']['overall-total-hours'][0] ?? null }}</div>
                            <div>{{ $record['timesheets']['overall-total-hours'][1] ?? null }}</div>
                        </div>
                    </td>
                    <td></td>
                </tr>
            @endif
        @endforeach
    </tbody>
</table>