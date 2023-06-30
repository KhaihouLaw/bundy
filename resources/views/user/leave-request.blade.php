@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/leave-request.css') }}">
@endsection

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Dashboard') }}
    </h2>
@endsection
    
@section('content')
    <div id="app" class="leave-request-root py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 bg-white border-b border-gray-200">
                    <div class="leave-cont">
                        <form>
                            <div class="options flex flex-col items-center">
                                <div class="mb-2" style="width: 85%;">
                                    <span class="font-black text-lg">Type of Leave</span>
                                </div>
                                <div class="checkboxes hidden sm:flex flex-wrap justify-center">
                                    @foreach ($leaveTypes as $type)
                                        <label class="border-2 border-blue-500 flex items-center pl-4 m-1 rounded-lg w-60 h-16">
                                            <input class="checkbox-round" type="checkbox" value="{{ $type['id'] }}" required>
                                            <span class="ml-2">{{ $type['leave'] }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                <div class="select sm:hidden">
                                    <select class="leave border-2 border-blue-500 rounded-lg w-64" required>
                                        <option value="" selected disabled hidden>Select an Option</option>
                                        @foreach ($leaveTypes as $type)
                                            <option value="{{ $type['id'] }}">{{ $type['leave'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="flex flex-wrap justify-evenly mt-4">
                                <label class="flex flex-col m-2">
                                    <span class="font-black text-lg mb-2">Start Date</span>
                                    <input class="start-date border-2 border-blue-500 rounded-lg" type="date" required>
                                </label>
                                <label class="flex flex-col m-2">
                                    <span class="font-black text-lg mb-2">End Date</span>
                                    <input class="end-date border-2 border-blue-500 rounded-lg" type="date" required>
                                </label>
                                <label class="flex flex-col m-2">
                                    <span class="font-black text-lg mb-2">Approver</span>
                                    <select class="border-2 border-blue-500 rounded-lg w-64" name="approver" required>
                                        @if (empty($employee->getApprover()))
                                            @php $approver = App\Models\User::findByEmail('luzvimindacruz@laverdad.edu.ph'); @endphp
                                            <option value="{{ $approver->employee->getId() }}">{{ $approver->employee->getFullName() }}</option>
                                        @else
                                            <option value="{{ $employee->getApprover()->getId() }}">{{ $employee->getApprover()->getFullName() }}</option>
                                        @endif
                                    </select>
                                </label>
                            </div>
                            <div>
                                <label class="flex flex-col mt-4" style="padding: 0 8% 0 8%;">
                                    <span class="font-black text-lg mb-2">Notes</span>
                                    <textarea class="reason border-2 rounded-lg border-gray-200 h-40" name="reason" required></textarea>
                                </label>
                            </div>
                            <div class="buttons-cont flex justify-end mt-5" style="padding-right: 8%;">
                                <button 
                                    class="cancel bg-gray-500 rounded-lg font-black text-white w-36 h-14 mr-4"
                                    type="button">Cancel</button>
                                <button 
                                    class="bg-blue-500 rounded-lg font-black text-white w-52 h-14"
                                    type="submit">Submit Leave Request</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script src="https://rawgit.com/notifyjs/notifyjs/master/dist/notify.js"></script>
    <script src="{{ asset('js/leave-request.js') }}" defer></script>
@endsection
