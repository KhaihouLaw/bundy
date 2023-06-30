@extends('user.advance.components.base')

@section('styles')
    @include('user.shared.data-table.style')
@endsection

@section('content')
    <div class="card mb-3">
        <div class="card-header">{{ $department->department }} Timesheet Adjustments</div>
        <div class="card-body">
            <div class="table-cont">
                <table id="datatable" class="
                            display table table-striped
                            dt-responsive nowrap text-center shadow-2xl" 
                        style="width:100%;">
                    <thead>
                        <tr>
                            <th class="w-14"></th>
                            <th>Employee</th>
                            <th>Date</th>
                            <th>Work</th>
                            <th>Lunch</th>
                            <th>Overtime</th>
                            <th>Status/Comparison</th>
                            <th class="no-export">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($department_timesheet_adjustments as $adjustment)
                        @php
                            $adjustment_id = $adjustment->id;
                            $full_name = $adjustment->employee->getFullName();
                            $timesheet_date = $adjustment->timesheet_date;
                            $time_in = $adjustment->getClockIn();
                            $time_out = $adjustment->getClockOut();
                            $lunch_start = $adjustment->getLunchStart();
                            $lunch_end = $adjustment->getLunchEnd();
                            $ot_start = $adjustment->getOvertimeStart();
                            $ot_end = $adjustment->getOvertimeEnd();
                            $isPending = $adjustment->isPending();
                            $status = $adjustment->getStatusBadge(1, '18px');
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
                                    data-coreui-toggle="modal" 
                                    data-coreui-target="#modal"
                                    data-model-id="{{ $adjustment_id }}">
                                    {!! $status !!}
                                </button>
                            </td>
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
                                <button type="button" class="close" data-dismiss="modal" data-coreui-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body flex justify-center items-center">
                                <div class="content w-full">
                                    {{-- dynamic content here --}}
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal" data-coreui-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @include('user.shared.data-table.script')
    <script src="{{ asset('js/advance/supervisor/department-timesheet-adjustments.js') }}"></script>
@endsection