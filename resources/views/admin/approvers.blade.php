@extends('admin.base')

@section('styles')
<link rel="stylesheet" href="{{ asset('/css/jquery.orgchart.min.css') }}">
@endsection

@section('content')

<div id="chart-container">

<div class="container-fluid">
    <div class="fade-in">
        <div class="card">
            <div class="card-body">
                @foreach ($departments as $department)
                <ul id="ul-data">
                    <li><strong>{{ $department->getDepartment() }} Department</strong>
                        <br>{{ $department->getApprover()->employee->getFullName() }}
                        <ul>
                        @foreach ($department->employees as $employee)
                        @if ($employee->user->getEmail() != $department->getApproverEmail())
                        <li>{{ $employee->getFullName() }}</li>
                        @endif
                        @endforeach
                        </ul>
                    <li>
                </ul>
                @endforeach
            </div>
        </div>
    </div>
</div>

@endsection

@section('javascript')
<script src="{{ asset('/js/jquery.orgchart.min.js') }}"></script>
<script>
let orgchart = new OrgChart({
  'chartContainer': '#chart-container',
  'data' : '#ul-data'
});
</script>
@endsection
