@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/vue/vue.css') }}">
@endsection

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Dashboard') }}
    </h2>

@endsection

@php
    $employee = Auth::user()->employee;
    $is_regular = $employee->isRegular();
@endphp

@section('content')
    <div class="leave-request-list py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg py-3 ">
                <br>
                <h3 class="max-w-5xl mx-auto sm:px-6 lg:px-8">
                    Leave Request Listing 
                    @if ($is_regular)
                    <a href="{{ route('add_leave_request') }}" class="btn btn-sm btn-primary text-white">Add a Leave Request</a>
                    @endif
                </h3>
                <br>
                <div class="flex justify-center max-w-4xl mx-auto sm:px-6 lg:px-8">
                    <nav class="nav nav-pills nav-fill nav-justified border border-primary rounded py-1 px-1 w-100">
                        <a href="{{ route('all_leave_requests') }}" class="w-25 nav-item "><button type="button" class="btn py-1  w-100"><span>All</span></button></a>
                        <a href="{{ route('approved_leave_requests') }}" class="w-25 nav-item "><button type="button" class="btn py-1 w-100 "><span>Approved</span></button></a>
                        <a href="{{ route('pending_leave_requests') }}" class="w-25 nav-item"><button type="button" class="btn btn-primary  py-1 w-100 font-weight-bold"><span>Pending</span></button></a>
                        <a href="{{ route('cancelled_leave_requests') }}" class="w-25 nav-item"><button type="button" class="btn py-1 w-100"><span>Cancelled</span></button></a>
                        <a href="{{ route('rejected_leave_requests') }}" class="w-25 nav-item"><button type="button" class="btn py-1 w-100 "><span>Rejected</span></button></a>
                    </nav>
                </div>
               
                
                <div class="p-4 bg-white border-b border-gray-200 flex justify-center max-w-4xl mx-auto sm:px-6 lg:px-8 list-group">
                    @foreach ($leaveRequestLists as $leaveRequestList)
                        <div>{{Carbon\Carbon::parse($leaveRequestList->created_at)->format('F Y')}}</div>
                        <div class="border border-primary rounded w-100 px-2 py-1 flex flex-col sm:flex-row items-center justify-between">
                            <div class="flex flex-col items-center sm:flex-row">
                                <span class="text-capitalize badge w-24 bg-yellow-500">{{$leaveRequestList->status}}</span> 
                                <span class="text-capitalize mx-4">{{$leaveRequestList->leave}}</span> 
                                <span class="text-capitalize mx-2">{{Carbon\Carbon::parse($leaveRequestList->created_at)->format('l, F d')}}</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <button class="bg-red-500 float-right p-1 text-white rounded-md cancel-btn" data-leave-id="{{ $leaveRequestList->id }}">Cancel</button>
                                <button class="btn btn-primary float-right p-1 text-white view-btn" data-toggle="modal" data-target="#exampleModalCenter" data-leave-id="{{ $leaveRequestList->id }}">View</button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Modal -->
                <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
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
@endsection

@section('javascript')
    <script src="https://rawgit.com/notifyjs/notifyjs/master/dist/notify.js"></script>
    <script src="{{ asset('js/leave-request.js') }}"></script>
@endsection