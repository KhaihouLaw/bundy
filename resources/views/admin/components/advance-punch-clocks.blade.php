<form class="edit-apc-instance">
    <table id="apc-datatable" class="display table table-striped dt-responsive nowrap text-center shadow">
        <thead class="data-table-sticky-header">
            <tr>
                <th class="no-export"></th>
                <th class="dtr-control-col no-export"></th>
                <th>Type</th>
                <th>Description</th>
                <th>Access Code</th>
                <th class="w-20 no-export"></th>
            </tr>
        </thead>
        <tbody>
            @php
                $row_index = 0;
            @endphp
            @foreach ($apc_instances as $instance)
                @php
                    $row_index++;
                @endphp
                <tr class="apc-container apc-{{ $instance->id }}">
                    <td>{{ $row_index }}</td>
                    <td>{{-- responsive row button --}}</td> 
                    <td>
                        <span class="editable apc-{{ $instance->id }} type">{{ $instance->type }}</span>
                    </td>
                    <td>
                        <span class="editable apc-{{ $instance->id }} description">{{ $instance->description }}</span>
                    </td>
                    <td>
                        <span class="editable apc-{{ $instance->id }} access-code">{{ $instance->access_code }}</span>
                    </td>
                    <td>
                        <div class="apc-{{ $instance->id }} actions flex justify-end items-center">
                            <div class="absolute">
                                <div class="cont-a flex justify-end items-center">
                                    <div>
                                        <button class="view btn btn-info shadow-sm" data-apc-id="{{ $instance->id }}"  data-schedule="{{ json_encode($instance->schedules) }}" type="button">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="flex justify-center items-center w-16 h-10">
                                        <button class="delete btn btn-danger shadow-sm absolute" data-apc-id="{{ $instance->id }}" type="button">
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