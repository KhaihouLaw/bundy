@php
    $employee = $timesheet_adjustment_model->employee;
    $employee_full_name = $employee->getFullName();
    $original_timesheet_model = $timesheet_adjustment_model->timesheet;
    $timesheet_date = $timesheet_adjustment_model->getTimesheetDate();
    $adjustment_status = $timesheet_adjustment_model->getStatusBadge(2, '15px;');
@endphp
<div class="w-full">
    <div class="card-title flex justify-between items-center mb-4">
        <div class="font-black ">{{ $employee_full_name }}</div>
        <div class="text-lg">{{ $timesheet_date }}</div>
        <div>{!! $adjustment_status !!}</div>
    </div>
    <table class="table table-striped  text-center">
        <tr>
            <th></th>
            <th>Original</th>
            <th style="background-color: ">Changes</th>
        </tr>
        <tr>
            <td>Time In</td>
            <td class="bg-gradient" style="background-color: #f3f364; color: black;">{{ $original_timesheet_model->getClockIn() }}</td>
            <td class="bg-gradient" style="background-color: #00df00; color: black;"><strong>{{ $timesheet_adjustment_model->getClockIn() }}</strong></td>
        </tr>
        <tr>
            <td>Time Out</td>
            <td class="bg-gradient" style="background-color: #f3f364; color: black;">{{ $original_timesheet_model->getClockOut() }}</td>
            <td class="bg-gradient" style="background-color: #00df00; color: black;"><strong>{{ $timesheet_adjustment_model->getClockOut() }}</strong></td>
        </tr>
        <tr>
            <td>Lunch Start</td>
            <td class="bg-gradient" style="background-color: #f3f364; color: black;">{{ $original_timesheet_model->getLunchStart() }}</td>
            <td class="bg-gradient" style="background-color: #00df00; color: black;"><strong>{{ $timesheet_adjustment_model->getLunchStart() }}</strong></td>
        </tr>
        <tr>
            <td>Lunch End</td>
            <td class="bg-gradient" style="background-color: #f3f364; color: black;">{{ $original_timesheet_model->getLunchEnd() }}</td>
            <td class="bg-gradient" style="background-color: #00df00; color: black;"><strong>{{ $timesheet_adjustment_model->getLunchEnd() }}</strong></td>
        </tr>
        <tr>
            <td>OT Start</td>
            <td class="bg-gradient" style="background-color: #f3f364; color: black;">{{ $original_timesheet_model->getOvertimeStart() }}</td>
            <td class="bg-gradient" style="background-color: #00df00; color: black;"><strong>{{ $timesheet_adjustment_model->getOvertimeStart() }}</strong></td>
        </tr>
        <tr>
            <td>OT End</td>
            <td class="bg-gradient" style="background-color: #f3f364; color: black;">{{ $original_timesheet_model->getOvertimeEnd() }}</td>
            <td class="bg-gradient" style="background-color: #00df00; color: black;"><strong>{{ $timesheet_adjustment_model->getOvertimeEnd() }}</strong></td>
        </tr>
        <tr>
            <td colspan="3">
                <div>
                    @if ($timesheet_adjustment_model->hasNotes())
                        <strong>Justification Notes:</strong><br>
                        <div class="bg-gray-200 bg-gradient rounded-xl px-3 py-3" style="min-height: 20px !important; color: black;">
                            {{ $timesheet_adjustment_model->getNotes() }}
                        </div>
                    @endif
                </div>
            </td>
        </tr>
    </table>
    @if ($timesheet_adjustment_model->isPending())
        <div class="h-12">
            <div class="absolute left-0 right-0 ml-auto mr-auto" style="width: 89px;">
                <button type="button" class="btn btn-info approve shadow-sm text-white" data-model-id="{{ $timesheet_adjustment_model->id }}">
                    <i class="far fa-thumbs-up"></i>
                </button>
                <button type="button" class="btn btn-danger reject shadow-sm text-white" data-model-id="{{ $timesheet_adjustment_model->id }}">
                    <i class="far fa-thumbs-down"></i>
                </button>
            </div>
        </div>
    @endif
</div>