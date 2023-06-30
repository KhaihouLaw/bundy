@php
    $employee = $leave_request_model->employee;
    $employee_full_name = $employee->getFullName();
    $start_date = $leave_request_model->getStartDate('M j, Y');
    $end_date = $leave_request_model->getEndDate('M j, Y');
    $status = $leave_request_model->getStatusBadge(1, '15px');
    $is_pending = $leave_request_model->isPending();
    $reason = $leave_request_model->getReason(true);
@endphp
<div class="w-full">
    <div class="card-title flex justify-between items-center mb-4">
        <div class="font-black ">{{ $employee_full_name }}</div>
        <div class="text-lg">
            <span class="badge rounded-pill bg-light bg-gradient text-black">{{ $start_date }}</span> 
            <span>to</span>
            <span class="badge rounded-pill bg-light bg-gradient text-black">{{ $end_date }}</span> 
        </div>
        <div>{!! $status !!}</div>
    </div>

    <div class="bg-gray-200 py-10 px-4 rounded-lg text-justify mb-3" style="text-indent: 50px; min-height: 300px;">
        {{ $reason }}
    </div>
    
    @if ($is_pending)
        <div class="h-12">
            <div class="absolute left-0 right-0 ml-auto mr-auto" style="width: 89px;">
                <button type="button" class="btn btn-info approve shadow-sm text-white" data-model-id="{{ $leave_request_model->id }}">
                    <i class="far fa-thumbs-up"></i>
                </button>
                <button type="button" class="btn btn-danger reject shadow-sm text-white" data-model-id="{{ $leave_request_model->id }}">
                    <i class="far fa-thumbs-down"></i>
                </button>
            </div>
        </div>
    @endif
</div>