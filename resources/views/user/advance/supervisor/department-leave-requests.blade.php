@extends('user.advance.components.base')


@section('styles')
    @include('user.shared.data-table.style')
@endsection

@section('content')
    <div class="card mb-3">
        <div class="card-header">{{ $department->department }} Leave Requests</div>
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
                            <th>Type</th>
                            <th>When</th>
                            <th>Total Days</th>
                            <th>Status/Reason</th>
                            <th class="no-export">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($department_leave_requests as $leave_request)
                        @php
                            $avatar = $leave_request->employee->user->getAvatar();
                            $employee_full_name = $leave_request->employee->getFullName();
                            $leave_type = $leave_request->leaveType->getName();
                            $start_date = $leave_request->getStartDate('M j, Y');
                            $end_date = $leave_request->getEndDate('M j, Y');
                            $total_days = $leave_request->getTotalDays();
                            $status = $leave_request->getStatusBadge(1, '18px');
                            $leave_id = $leave_request->getId()
                        @endphp
                        <tr>
                            <td><img src="{{ $avatar }}"></td>
                            <td>{{ $employee_full_name }}</td>
                            <td>{{ $leave_type }}</td>
                            <td>
                                <span class="px-2 py-1 rounded-pill bg-blue-200 bg-gradient shadow-sm">{{ $start_date }}</span> 
                                <span>to</span>
                                <span class="px-2 py-1 rounded-pill bg-blue-200 bg-gradient shadow-sm">{{ $end_date }}</span> 
                            </td>
                            <td>{{ $total_days }}</td>
                            <td>
                                <button 
                                    class="view-btn" 
                                    data-toggle="modal" 
                                    data-target="#modal" 
                                    data-coreui-toggle="modal" 
                                    data-coreui-target="#modal"
                                    data-model-id="{{ $leave_id }}">
                                    {!! $status !!}
                                </button>
                            </td>
                            <td class="w-44">
                                <div class="actions flex items-center justify-center w-32">
                                    @if ($leave_request->isPending())
                                        <div class="absolute">
                                            <button type="button" class="btn btn-info approve shadow-sm text-white" data-model-id="{{ $leave_id }}">
                                                <i class="far fa-thumbs-up"></i>
                                            </button>
                                            <button type="button" class="btn btn-danger reject shadow-sm text-white" data-model-id="{{ $leave_id }}">
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
                                <h5 class="modal-title" id="">Leave Request Reason</h5>
                                <button type="button" class="close" data-dismiss="modal" data-coreui-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
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
    <script src="{{ asset('js/advance/supervisor/department-leave.js') }}"></script>
@endsection