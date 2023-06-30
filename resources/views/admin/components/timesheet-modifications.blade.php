<div class="table-cont">
    <table id="datatable" class="display table table-striped dt-responsive nowrap text-center shadow" 
            style="width:100%;">
        <thead class="data-table-sticky-header">
            <tr>
                <th class="w-14"></th>
                <th>Employee</th>
                <th>Date</th>
                <th>Work</th>
                <th>Lunch</th>
                <th>Overtime</th>
                <th>Changes</th>
                <th>Requested At</th>
                <th class="no-export">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($timesheet_adjustments as $adjustment)
            @php
                $adjustment_id = $adjustment->id;
                $full_name = $adjustment->employee->getFullName();
                $timesheet_date = $adjustment->timesheet_date;
                $requested_at = date('F j, Y, h:i:s A', strtotime($adjustment->created_at));
                $original_timesheet = $adjustment->timesheet;

                // // UPDATE
                // $time_in = $adjustment->getClockIn();
                // $time_out = $adjustment->getClockOut();
                // $lunch_start = $adjustment->getLunchStart();
                // $lunch_end = $adjustment->getLunchEnd();
                // $ot_start = $adjustment->getOvertimeStart();
                // $ot_end = $adjustment->getOvertimeEnd();

                // // Original
                $time_in = $original_timesheet->getClockIn();
                $time_out = $original_timesheet->getClockOut();
                $lunch_start = $original_timesheet->getLunchStart();
                $lunch_end = $original_timesheet->getLunchEnd();
                $ot_start = $original_timesheet->getOvertimeStart();
                $ot_end = $original_timesheet->getOvertimeEnd();

                $isPending = $adjustment->isPending();
                $status = $adjustment->getStatusBadge(2, '17px');
            @endphp
            <tr>
                <td><img src="{{ $adjustment->employee->user->getAvatar() }}"></td>
                <td>{{ $full_name }}</td>
                <td>{{ $timesheet_date }}</td>
                <td>{{ $time_in }} - {{ $time_out }}</td>
                <td>{{ $lunch_start }} - {{ $lunch_end }}</td>
                <td>{{ $ot_start }} - {{ $ot_end }}</td>
                <td>
                    <button 
                        class="view-btn" 
                        data-toggle="modal" 
                        data-target="#modal" 
                        data-model-id="{{ $adjustment_id }}">
                        {!! $status !!}
                    </button>
                </td>
                <td>{{ $requested_at }}</td>
                <td class="w-44">
                    <div class="actions flex items-center justify-center w-32">
                        @if ($adjustment->isPending())
                            <div class="absolute">
                                <button type="button" class="btn btn-info approve shadow-sm text-white" data-model-id="{{ $adjustment_id }}">
                                    <i class="far fa-thumbs-up"></i>
                                </button>
                                <button type="button" class="btn btn-danger reject shadow-sm text-white" data-model-id="{{ $adjustment_id }}">
                                    <i class="far fa-thumbs-down"></i>
                                </button>
                            </div>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="">Timesheet Adjustment</h5>
                    <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body flex justify-center items-center">
                    <div class="content w-full">
                        {{-- dynamic content here --}}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>