@extends('admin.base')

@section('content')

<div class="container-fluid">
    <div class="fade-in">
        <div class="card">
            <div class="card-body">
                @include('admin.components.holidays')
            </div>
        </div>
    </div>
</div>

@endsection
