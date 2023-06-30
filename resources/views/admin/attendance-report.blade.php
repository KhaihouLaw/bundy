@extends('admin.base')

@section('styles')
    @include('admin.shared.data-table.style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.4/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endsection

@section('content')

    <div class="root-report attendance-report container-fluid">
        <div class="fade-in">
            <div class="card">
                <div class="card-header font-black">Generate Attendance Report</div>
                <div class="card-body">
                    <div>
                        <div>
                            <form class="compute-total-hours flex flex-row flex-wrap justify-evenly items-center">
                                @csrf
                                <div class="flex flex-row space-x-4">
                                    <div>
                                        <label for="start-date">Start Date:</label>
                                        <input 
                                            class="date shadow-xl"
                                            id="start-date" 
                                            name="start-date" 
                                            type="date" 
                                            required/>
                                    </div>
                                    <div>
                                        <label for="end-date">End Date:</label>
                                        <input 
                                            class="date shadow-xl"
                                            id="end-date" 
                                            name="end-date" 
                                            type="date" 
                                            required/>
                                    </div>
                                </div>
                                <button name="compute" type="submit" class="w-28 h-16 my-6 bg-gradient-to-t from-blue-500 via-blue-300 to-blue-300 rounded-xl text-white font-black shadow-sm">
                                    Compute
                                </button>
                            </form>
                        </div>
                        <div class="report-container flex flex-col mt-14">
                            <div class="buttons mb-2 hidden">
                                {{-- <button 
                                    class="create-report-pdf rounded-lg w-16 h-10 bg-red-500 text-white font-black" 
                                    style="letter-spacing: 2px;">
                                    PDF
                                </button> --}}
                                <button 
                                    class="create-report-csv rounded-lg w-16 h-10 bg-green-500 text-white font-black" 
                                    style="letter-spacing: 2px;">
                                    CSV
                                </button>
                            </div>
                            <div class="table text-center bg-gray-100 rounded-lg shadow-inner p-4 overflow-y-scroll" style="height: 450px;">
                                <div class="flex justify-center items-center h-full">
                                    <span class="font-black text-white text-lg filter drop-shadow-lg">No report preview</span>
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
    <script src="{{ asset('js/main.js') }}" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.4/dist/sweetalert2.all.min.js" integrity="sha256-dOvlmZEDY4iFbZBwD8WWLNMbYhevyx6lzTpfVdo0asA=" crossorigin="anonymous"></script>
    <script src="https://rawgit.com/notifyjs/notifyjs/master/dist/notify.js"></script>
    <script src="https://momentjs.com/downloads/moment.js"></script>
    <script src="{{ asset('js/tableToCsv.js') }}"></script>
    <script src="{{ asset('js/admin.js') }}"></script>
@endsection
