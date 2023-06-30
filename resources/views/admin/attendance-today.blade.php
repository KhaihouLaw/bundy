@extends('admin.base')

@section('styles')
    @include('admin.shared.data-table.style')
@endsection

@section('content')

<div class="attendance-today container-fluid">
    <div class="fade-in">
        <div class="card">
            <div class="card-header font-black">Attendance Today</div>
            <div class="card-body shadow-sm">
                @include('admin.components.attendance-today')
                <!-- View Reason Modal -->
                <div class="leave-reason-modal modal fade" id="view-leave-reason-modal" tabindex="-1" role="dialog" aria-labelledby="view-leave-reason-modal-title" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="">Leave Request Reason</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <p class="request-reason bg-gray-200 py-10 px-4 rounded-lg text-justify" style="text-indent: 50px;">Loading...</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('javascript')
    @include('admin.shared.data-table.script')
    <script src="https://rawgit.com/notifyjs/notifyjs/master/dist/notify.js"></script>
    <script src="{{ asset('js/admin.js') }}"></script>
@endsection