<table id="leave-request-datatable" class="display table table-striped dt-responsive nowrap text-center shadow" style="width:100%;">
    <thead class="data-table-sticky-header">
        <tr>
            <th class="w-16"></th>
            <th>Employee</th>
            <th>Type</th>
            <th>When</th>
            <th>Status/Reason</th>
            <th>Request At</th>
            <th class="no-export">Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($leave_requests as $request)
        <tr>
            <td>
                <img src="{{ $request->employee->user->getAvatar() }}">
            </td>
            <td>{{ $request->employee->getFullName() }}</td>
            <td>{{ $request->leaveType->getName() }}</td>
            <td>
                <span class="px-2 py-1 rounded-pill bg-blue-200 bg-gradient shadow-sm">{{ $request->getStartDate() }}</span> 
                <span>to</span>
                <span class="px-2 py-1 rounded-pill bg-blue-200 bg-gradient shadow-sm">{{ $request->getEndDate() }}</span> 
            </td>
            <td>
                <button 
                    class="view-btn" 
                    data-toggle="modal" 
                    data-target="#view-model-modal" 
                    data-model-id="{{ $request->getId() }}">
                    {!! $request->getStatusBadge(2, '17px') !!}
                </button>
            </td>
            <td>{{ date('F j, Y, h:i:s A', strtotime($request->created_at)) }}</td>
            <td class="w-44">
                <div class="actions flex items-center justify-center">
                    @if ($request->isPending())
                        <div class="absolute">
                            <button type="button" class="btn btn-info approve mr-2 shadow-sm" data-model-id="{{ $request->getId() }}">
                                <i class="far fa-thumbs-up"></i>
                            </button>
                            <button type="button" class="btn btn-danger reject shadow-sm" data-model-id="{{ $request->getId() }}">
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