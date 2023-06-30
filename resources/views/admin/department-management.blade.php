@extends('admin.base')

@section('styles')
    @include('admin.shared.data-table.style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.4/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endsection

@section('content')
<div class="dept-management container-fluid">
    <div class="fade-in">
        <div class="card">
            <div class="card-header font-black">Departments</div>
            <div class="card-body">
                <div role="alert" aria-live="polite" aria-atomic="true" class="ts-alert alert alert-warning h-14 flex flex-row" style="padding: 0 !important;">
                    <div class="h-full w-14 bg-yellow-200 rounded flex justify-center items-center">
                        <i class="fas fa-exclamation-triangle text-4xl text-yellow-500"></i>
                    </div>
                    <span class="flex items-center w-full font-black pl-10">
                        Deleting a department will also delete all its employees
                    </span>
                </div>
                <div class="flex justify-end mx-4">
                    <button class="add-dept px-4 py-1 flex justify-center items-center space-x-2 bg-gradient-to-t from-blue-500 via-blue-300 to-blue-300 rounded-xl text-white font-black shadow-sm">
                        <span class="text-2xl">+</span>
                        <span>Add Department</span>
                    </button>
                </div>
                <div class="mt-2">@include('admin.components.departments')</div>
                {{-- add department --}}
                <div class="add-dept-modal modal fade" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" style="max-width: 870px;">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title font-black text-xl">Add Department</h5>
                                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body flex justify-center" style="padding: 40px 0 40px 0;">
                                <div class="form flex flex-col items-center justify-center px-10 w-full">
                                    <form class="add-dept flex flex-col space-y-2 justify-center items-center">
                                        <div class="p-2 flex flex-wrap justify-center items-center space-x-4">
                                            <label class="font-black text-lg mt-2" for="add-department">Department:</label>
                                            <input id="add-department" class="m-2 border-2 border-blue-300 rounded-xl" type="text" name="department" required />
                                            <label class="font-black text-lg mt-2" for="add-dept-supervisor">
                                                <span>supervisor:</span>
                                                <select id="add-dept-supervisor" class="m-2 border-2 border-blue-300 rounded-xl" name="supervisor" required>
                                                    <option value="" selected disabled hidden>Select a Supervisor</option>
                                                    @foreach ($employees as $supervisor)
                                                        <option value="{{ $supervisor->email }}">{{ $supervisor->email }}</option>
                                                    @endforeach
                                                </select>
                                            </label>
                                        </div>
                                        <button class="add-dept w-28 h-14 mt-4 bg-blue-400 rounded-xl font-black text-white" type="submit">Save</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- strict confirmation --}}
                <div class="confirm-delete-modal modal fade" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" style="max-width: 870px;">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title font-black text-xl">Are you sure about this deletion?</h5>
                                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body flex flex-col justify-center" style="padding: 0px 0 40px 0;">
                                <div role="alert" aria-live="polite" aria-atomic="true" class="ts-alert alert alert-warning h-14 flex flex-row" style="padding: 0 !important;">
                                    <div class="h-full w-14 bg-yellow-200 rounded flex justify-center items-center">
                                        <i class="fas fa-exclamation-triangle text-4xl text-yellow-500"></i>
                                    </div>
                                    <span class="flex items-center w-full text-base font-black pl-4">
                                        This action cannot be undone
                                    </span>
                                </div>
                                <div class="form flex flex-col items-center justify-center px-10 w-full">
                                    <form class="delete-dept flex flex-col space-y-2 justify-center items-center">
                                        <div class="p-2 flex flex-col justify-center items-center space-x-4">
                                            <label class="font-black text-lg mt-2" for="del-phrase">Please enter <i class="bg-gray-100 rounded-lg given-delete-phrase" style="color: black;">I'm sure about this deletion</i>:</label>
                                            <input id="del-phrase" class="m-2 border-2 border-blue-300 rounded-xl" type="text" name="del-phrase" required />
                                            <label class="font-black text-lg mt-2" for="password">Password:</label>
                                            <input id="password" class="m-2 border-2 border-blue-300 rounded-xl" type="password" name="password" required />
                                        </div>
                                        <button class="delete-dept w-28 h-14 mt-4 bg-blue-400 rounded-xl font-black text-white" type="submit">Delete</button>
                                    </form>
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
    <script src="https://momentjs.com/downloads/moment.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.color-animation/1/mainfile"></script>
    <script src="{{ asset('js/admin.js') }}" class="dept-management" data-employees="{{ $employees }}"></script>
@endsection