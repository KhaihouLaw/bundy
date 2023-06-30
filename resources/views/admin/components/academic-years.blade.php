<form class="edit-ay">
    <table id="ay-datatable" class="display table table-striped dt-responsive nowrap text-center shadow">
        <thead class="data-table-sticky-header">
            <tr>
                <th class="no-export"></th>
                <th class="dtr-control-col no-export"></th>
                <th>Description</th>
                <th>Semester</th>
                <th>Start Year</th>
                <th>End Year</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th class="w-20 no-export"></th>
            </tr>
        </thead>
        <tbody>
            @php
                $row_index = 0;
            @endphp
            @foreach ($academic_years as $ay)
                @php
                    $row_index++;
                @endphp
                <tr class="ay-container ay-{{ $ay->id }}">
                    <td>{{ $row_index }}</td>
                    <td>{{-- responsive row button --}}</td>
                    <td>
                        <span class="editable ay-{{ $ay->id }} description">{{ $ay->description }}</span>
                        <input class="editable ay-{{ $ay->id }} border-blue-500 rounded-2xl shadow-xl hidden" type="text" name="description" value="{{ $ay->description }}" />
                    </td>
                    <td>
                        <span class="editable ay-{{ $ay->id }} semester">{{ $ay->semester }}</span>
                        <input class="editable ay-{{ $ay->id }} border-blue-500 rounded-2xl shadow-xl hidden" type="number" name="semester" value="{{ $ay->semester }}" />
                    </td>
                    <td>
                        <span class="editable ay-{{ $ay->id }} start-year">{{ $ay->start_year }}</span>
                        <input class="editable ay-{{ $ay->id }} year border-blue-500 rounded-2xl shadow-xl hidden" type="number" name="start_year" value="{{ $ay->start_year }}" />
                    </td>
                    <td>
                        <span class="editable ay-{{ $ay->id }} end-year">{{ $ay->end_year }}</span>
                        <input class="editable ay-{{ $ay->id }} year border-blue-500 rounded-2xl shadow-xl hidden" type="number" name="end_year" value="{{ $ay->end_year }}" />
                    </td>
                    <td>
                        <span class="editable ay-{{ $ay->id }} start-date">{{ $ay->start_date }}</span>
                        <input class="editable ay-{{ $ay->id }} border-blue-500 rounded-2xl shadow-xl hidden" type="date" name="start_date" value="{{ $ay->start_date }}" />
                    </td>
                    <td>
                        <span class="editable ay-{{ $ay->id }} end-date">{{ $ay->end_date }}</span>
                        <input class="editable ay-{{ $ay->id }} border-blue-500 rounded-2xl shadow-xl hidden" type="date" name="end_date" value="{{ $ay->end_date }}" />
                    </td>
                    <td>
                        <div class="ay-{{ $ay->id }} actions flex justify-end items-center">
                            <div class="absolute">
                                <div class="cont-a flex justify-end items-center">
                                    <div>
                                        <button class="cancel px-2 rounded-xl border-gray-500 bg-gray-200 hover:text-white shadow-sm hidden" data-ay-id="{{ $ay->id }}" type="button">x</button>
                                        <button class="save btn btn-success shadow-sm hidden" data-ay-id="{{ $ay->id }}" type="submit">
                                            <i class="far fa-save"></i>
                                        </button>
                                        <button class="edit btn btn-info shadow-sm" data-ay-id="{{ $ay->id }}" type="button">
                                            <i class="fas fa-user-edit"></i>
                                        </button>
                                    </div>
                                    <div class="flex justify-center items-center w-16 h-10">
                                        <button class="delete btn btn-danger shadow-sm absolute" data-ay-id="{{ $ay->id }}" type="button">
                                            <i class="far fa-trash-alt"></i>
                                        </button>
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