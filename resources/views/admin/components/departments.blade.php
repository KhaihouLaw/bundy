<form class="edit-dept">
    <table id="dept-datatable" class="display table table-striped dt-responsive nowrap text-center shadow">
        <thead class="data-table-sticky-header">
            <tr>
                <th class="no-export"></th>
                <th class="dtr-control-col no-export"></th>
                <th>Department</th>
                <th>Supervisor</th>
                <th>Approver</th>
                <th class="w-20 no-export"></th>
            </tr>
        </thead>
        <tbody>
            @php
                $row_index = 0;
            @endphp
            @foreach ($departments as $dept)
                @php
                    $is_hr = $dept->department === App\Models\Department::HR_DEPT;
                    $row_index++;
                @endphp
                <tr class="dept-container dept-{{ $dept->id }}">
                    <td>{{ $row_index }}</td>
                    <td>{{-- responsive row button --}}</td> 
                    <td>
                        <div class="flex justify-center items-center">
                            <div class="overflow-x-auto w-64 h-16 flex justify-center items-center">
                                <span class="{{ !$is_hr ? 'editable' : null }} dept-{{ $dept->id }} department">{{ $dept->department }}</span>
                                @if (!$is_hr)
                                <input class="editable dept-{{ $dept->id }} border-blue-500 rounded-2xl shadow-sm hidden" type="text" name="department" value="{{ $dept->department }}" />
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="flex justify-center items-center">
                            <div class="overflow-x-auto w-80 h-16 flex justify-center items-center">
                                <span class="editable dept-{{ $dept->id }} supervisor">{{ $dept->supervisor }}</span>
                                <select class="editable dept-{{ $dept->id }} border-blue-500 rounded-2xl shadow-sm hidden" name="supervisor" required>
                                    <option value="{{ $dept->supervisor }}" selected disabled hidden>{{ $dept->supervisor }}</option>
                                    @foreach ($employees as $supervisor)
                                        <option value="{{ $supervisor->email }}">{{ $supervisor->email }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="flex justify-center items-center">
                            <div class="overflow-x-auto w-80 h-16 flex justify-center items-center">
                                <span class="editable dept-{{ $dept->id }} approver">{{ $dept->approver }}</span>
                                <select class="editable dept-{{ $dept->id }} border-blue-500 rounded-2xl shadow-sm hidden" name="approver" required>
                                    <option value="{{ $dept->approver }}" selected disabled hidden>{{ $dept->approver }}</option>
                                    @foreach ($employees as $approver)
                                        <option value="{{ $approver->email }}">{{ $approver->email }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="dept-{{ $dept->id }} actions flex justify-end items-center">
                            <div class="absolute">
                                <div class="cont-a flex justify-end items-center">
                                    <div>
                                        <button class="cancel px-2 rounded-xl border-gray-500 bg-gray-200 hover:text-white shadow-sm hidden" data-dept-id="{{ $dept->id }}" type="button">x</button>
                                        <button class="save btn btn-success shadow-sm hidden" data-dept-id="{{ $dept->id }}" type="submit">
                                            <i class="far fa-save"></i>
                                        </button>
                                        <button class="edit btn btn-info shadow-sm" data-dept-id="{{ $dept->id }}" type="button">
                                            <i class="fas fa-user-edit"></i>
                                        </button>
                                    </div>
                                    <div class="flex justify-center items-center w-16 h-10">
                                        @if (!$is_hr)
                                            <button class="delete btn btn-danger shadow-sm absolute" data-dept-id="{{ $dept->id }}" type="button">
                                                <i class="far fa-trash-alt"></i>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</form>