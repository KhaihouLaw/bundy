@extends('admin.base')

@section('styles')
    @include('admin.shared.data-table.style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.4/dist/sweetalert2.min.css">
@endsection

@section('content')

<div class="leave-request container-fluid">
    <div class="fade-in">
        <div class="card">
            <div class="card-header font-black">Leave Requests</div>
            <div class="card-body">
                <div class="m-2">
                    @include('admin.components.leave-requests')
                    <!-- View Reason Modal -->
                    <div class="modal fade" id="view-leave-reason-modal" tabindex="-1" role="dialog" aria-labelledby="view-leave-reason-modal-title" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="">Leave Request Reason</h5>
                                    <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
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
            </div>
        </div>
    </div>
</div>

@endsection


@section('javascript')
    @include('admin.shared.data-table.script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.4/dist/sweetalert2.all.min.js" integrity="sha256-dOvlmZEDY4iFbZBwD8WWLNMbYhevyx6lzTpfVdo0asA=" crossorigin="anonymous"></script>
    <script src="https://rawgit.com/notifyjs/notifyjs/master/dist/notify.js"></script>
    <script src="{{ asset('js/admin.js') }}"></script>
@endsection