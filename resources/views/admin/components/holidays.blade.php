<div class="m-2">
    <div class="grid justify-items-center mb-4">
        <span class="text-xl font-black">Holidays</span>
    </div>
    <table id="employee-datatable" class="display table table-striped table-bordered dt-responsive nowrap" style="width:100%;">
        <thead class="data-table-sticky-header">
            <tr>
                <th class="w-30">Holiday</th>
                <th>Date</th>
                <th>Type</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($holidays as $holiday)
            <tr>
                <td>{{ $holiday->getName() }}</td>
                <td>{{ $holiday->getHolidayDate() }}</td>
                <td>{{ $holiday->getType() }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>