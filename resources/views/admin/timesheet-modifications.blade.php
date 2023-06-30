@extends('admin.base')

@section('styles')
    @include('admin.shared.data-table.style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.4/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endsection

@section('content')
<div class="timesheet-adjustments container-fluid">
    <div class="fade-in">
        <div class="card">
            <div class="card-header font-black">Timesheet Adjustment Requests</div>
            <div class="card-body" style="min-height: 70vh;">
                <div class="w-full overflow-x-auto flex justify-center items-center mb-4">
                    <canvas id="timesheet-chart" width="500" height="500"></canvas>
                    <div class="flex flex-col justify-center items-center border-2 border-gray-100 rounded-xl p-10">
                        <form method="GET" action="{{ route('admin_timesheet_modifications') }}">
                            <div class="flex flex-col justify-center items-center">
                                <div class="mb-2 text-sm text-gray-400 italic">Filter data to be shown in the table with a maximum of 100 entries.</div>
                                <div class="flex justify-center space-x-2">
                                    <label>
                                        <span>Keywords:</span>
                                        <input class="filter-keywords border-2 border-blue-300 rounded-xl" 
                                            type="text" 
                                            name="keywords" 
                                            placeholder="Enter: date, letters, numbers, etc." 
                                            value="{{ !empty($filter_keywords) ? $filter_keywords : null }}" />
                                    </label>
                                </div>
                                <button class="add-timesheet w-28 h-14 bg-blue-400 rounded-xl font-black text-white" type="submit">Filter</button>
                            </div>
                        </form>
                        <hr class="w-full bg-transparent border-2 border-gray-100 ">
                        <form method="GET" action="{{ route('admin_timesheet_modifications') }}">
                            <div class="flex flex-col justify-center items-center">
                                <div class="mb-2 text-sm text-gray-400 italic">
                                    Filter result batch has total of&nbsp;
                                    <span class="bg-gray-100 p-1 rounded" style="color: rgba(0, 0, 0, 0.637);">{{ $filtered_timesheet_adjustments->count() }}</span>&nbsp;
                                    entries.
                                </div>
                                <div class="mb-2 text-sm text-gray-400 italic">Get prev|next batch to be shown in the table.</div>
                                <input class="hidden" type="text" name="keywords" value="{{ !empty($filter_keywords) ? $filter_keywords : null }}" />
                                <input class="hidden" type="text" name="last_timesheet_date" value="{{ $timesheet_adjustments->last()->timesheet_date }}" />
                                <div class="flex space-x-2">
                                    <button class="w-28 h-14 bg-blue-400 rounded-xl font-black text-white" type="submit" name="batch" value="previous">Previous</button>
                                    <button class="w-28 h-14 bg-blue-400 rounded-xl font-black text-white" type="submit" name="batch" value="next">Next</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                @include('admin.components.timesheet-modifications')
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
    <script class="chart-data" 
        data-total-requests="{{ $all_timesheet_adjustments->count() }}" 
        data-status-counts='{{ json_encode($status_counts) }}'
        src="{{ asset('js/admin/timesheet_adjustments/index.js') }}">
    </script>
@endsection