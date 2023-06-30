<!doctype html>
<html lang="en">

<head>
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="login-token" content="{{ Auth::user()->login_token }}">

    {{-- Tailwind, etc. --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">

	{{-- Icons --}}
	<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined">
	<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons+Round">

	<!-- CoreUI CSS -->
	<link rel="stylesheet" href="{{ asset('css/admin/app.css') }}">
	<link rel="stylesheet" href="https://unpkg.com/@coreui/icons@2.0.0-beta.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    
    @yield('styles')

	<script src="https://kit.fontawesome.com/c1df3fb280.js" crossorigin="anonymous"></script>

	<title>@yield('title', 'BUNDY')</title>
</head>

<body class="c-app">

	@include('admin.shared.sidebar')

	<div class="c-wrapper">

	    @include('admin.shared.header')

		<div class="c-body">
			<main class="c-main">
				@yield('content')
			</main>
		</div>
		<footer class="c-footer">
			<!-- Footer content here -->
			&copy; La Verdad Christian College
		</footer>
	</div>

    {{-- ************ CORE UI ************ --}}
    <script src="{{ asset('js/coreui.bundle.min.js') }}"></script>
    <script src="{{ asset('js/coreui-utils.js') }}"></script>
    <script src="{{ asset('js/Chart.min.js') }}"></script>
    <script src="{{ asset('js/coreui-chartjs.bundle.js') }}"></script>
	<script src="{{ asset('js/app.js') }}"></script>
    
	{{-- this causes modal is not a function error --}}
    {{-- <script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script> --}}

	<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>
	<script src="https://unpkg.com/chart.js-plugin-labels-dv/dist/chartjs-plugin-labels.min.js"></script>
    
    @yield('javascript')
</body>

</html>