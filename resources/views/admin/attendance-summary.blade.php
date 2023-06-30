@extends('admin.base')

@section('styles')
    @include('admin.shared.data-table.style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.9.0/main.min.css">
@endsection

@section('content')
    <div class="container-fluid attendance-summary">
        <div class="fade-in">
            <div class="card">
                <div class="card-header font-black">Attendance History</div>
                <div class="card-body">
                    <div id="attendance-summary-calendar" class="calendar"></div>
                    <div class="modal fade" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" style="max-width: 870px;">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title font-black text-xl">Attendance Summary</h5>
                                    <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body flex justify-center" style="padding: 40px 0 40px 0;">
                                    <div class="summary flex flex-col items-center justify-center px-10 w-full">
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
    <script src="https://rawgit.com/notifyjs/notifyjs/master/dist/notify.js"></script>
    <script src="https://momentjs.com/downloads/moment.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.9.0/main.min.js"></script>
    <script src="{{ asset('js/admin.js') }}"></script>
@endsection
