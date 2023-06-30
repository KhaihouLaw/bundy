<!DOCTYPE html>
<!-- Breadcrumb-->
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <base href="./">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="description" content="La Verdad Christian College Online Bundy">
    <meta name="author" content="LVCC Bundy Team">
    <meta name="keyword" content="LVCC,Bundy,Timesheet,Clock,Time">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="login-token" content="{{ Auth::user()->login_token }}">

    <title>{{ config('app.name', 'LVCC Bundy') }}</title>
    
    <link rel="apple-touch-icon-precomposed" sizes="57x57" href="/assets/favicon/lvcc-logo/apple-touch-icon-57x57.png" />
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="/assets/favicon/lvcc-logo/apple-touch-icon-114x114.png" />
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="/assets/favicon/lvcc-logo/apple-touch-icon-72x72.png" />
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="/assets/favicon/lvcc-logo/apple-touch-icon-144x144.png" />
    <link rel="apple-touch-icon-precomposed" sizes="60x60" href="/assets/favicon/lvcc-logo/apple-touch-icon-60x60.png" />
    <link rel="apple-touch-icon-precomposed" sizes="120x120" href="/assets/favicon/lvcc-logo/apple-touch-icon-120x120.png" />
    <link rel="apple-touch-icon-precomposed" sizes="76x76" href="/assets/favicon/lvcc-logo/apple-touch-icon-76x76.png" />
    <link rel="apple-touch-icon-precomposed" sizes="152x152" href="/assets/favicon/lvcc-logo/apple-touch-icon-152x152.png" />
    <link rel="icon" type="image/png" href="/assets/favicon/lvcc-logo/favicon-196x196.png" sizes="196x196" />
    <link rel="icon" type="image/png" href="/assets/favicon/lvcc-logo/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/png" href="/assets/favicon/lvcc-logo/favicon-32x32.png" sizes="32x32" />
    <link rel="icon" type="image/png" href="/assets/favicon/lvcc-logo/favicon-16x16.png" sizes="16x16" />
    <link rel="icon" type="image/png" href="/assets/favicon/lvcc-logo/favicon-128.png" sizes="128x128" />
    <meta name="application-name" content="&nbsp;"/>
    <meta name="msapplication-TileColor" content="#FFFFFF" />
    <meta name="msapplication-TileImage" content="/assets/favicon/lvcc-logo/mstile-144x144.png" />
    <meta name="msapplication-square70x70logo" content="/assets/favicon/lvcc-logo/mstile-70x70.png" />
    <meta name="msapplication-square150x150logo" content="/assets/favicon/lvcc-logo/mstile-150x150.png" />
    <meta name="msapplication-wide310x150logo" content="/assets/favicon/lvcc-logo/mstile-310x150.png" />
    <meta name="msapplication-square310x310logo" content="/assets/favicon/lvcc-logo/mstile-310x310.png" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.4/dist/sweetalert2.min.css">

    {{-- Icons --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <!-- Vendors styles-->
    <link rel="stylesheet" href="/user-coreui/vendors/simplebar/css/simplebar.css">
    <link rel="stylesheet" href="/user-coreui/css/vendors/simplebar.css">

    <!-- Main styles for this application-->
    <link href="/user-coreui/css/style.css" rel="stylesheet">
    
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link href="{{ asset('css/advance/base.css') }}" rel="stylesheet">

    <link href="/user-coreui/vendors/@coreui/chartjs/css/coreui-chartjs.css" rel="stylesheet">

    @yield('styles')

	<script src="https://kit.fontawesome.com/c1df3fb280.js" crossorigin="anonymous"></script>

</head>

<body>

    @include('user.advance.components.sidebar')

    <div class="wrapper d-flex flex-column min-vh-100 bg-light">

        @include('user.advance.components.header')

        <div class="body flex-grow-1 px-3">
            @if (Session::has('success'))
                <div class="alert alert-success" role="alert">Operation Successful!</div>
            @elseif (Session::has('error'))
                <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
            @endif

            {{-- <div class="container-lg">
                <div class="row">
                    <div class="col-sm-6 col-lg-3">
                        <div class="card mb-4 text-white bg-primary">
                            <div class="card-body pb-0 d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="fs-4 fw-semibold">26K <span class="fs-6 fw-normal">(-12.4%
                                            <svg class="icon">
                                                <use
                                                    xlink:href="/user-coreui/vendors/@coreui/icons/svg/free.svg#cil-arrow-bottom">
                                                </use>
                                            </svg>)</span></div>
                                    <div>Users</div>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-transparent text-white p-0" type="button"
                                        data-coreui-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <svg class="icon">
                                            <use
                                                xlink:href="/user-coreui/vendors/@coreui/icons/svg/free.svg#cil-options">
                                            </use>
                                        </svg>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end"><a class="dropdown-item"
                                            href="#">Action</a><a class="dropdown-item" href="#">Another action</a><a
                                            class="dropdown-item" href="#">Something else here</a></div>
                                </div>
                            </div>
                            <div class="c-chart-wrapper mt-3 mx-3" style="height:70px;">
                                <canvas class="chart" id="card-chart1" height="70"></canvas>
                            </div>
                        </div>
                    </div>
                    <!-- /.col-->
                    <div class="col-sm-6 col-lg-3">
                        <div class="card mb-4 text-white bg-info">
                            <div class="card-body pb-0 d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="fs-4 fw-semibold">$6.200 <span class="fs-6 fw-normal">(40.9%
                                            <svg class="icon">
                                                <use
                                                    xlink:href="/user-coreui/vendors/@coreui/icons/svg/free.svg#cil-arrow-top">
                                                </use>
                                            </svg>)</span></div>
                                    <div>Income</div>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-transparent text-white p-0" type="button"
                                        data-coreui-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <svg class="icon">
                                            <use
                                                xlink:href="/user-coreui/vendors/@coreui/icons/svg/free.svg#cil-options">
                                            </use>
                                        </svg>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end"><a class="dropdown-item"
                                            href="#">Action</a><a class="dropdown-item" href="#">Another action</a><a
                                            class="dropdown-item" href="#">Something else here</a></div>
                                </div>
                            </div>
                            <div class="c-chart-wrapper mt-3 mx-3" style="height:70px;">
                                <canvas class="chart" id="card-chart2" height="70"></canvas>
                            </div>
                        </div>
                    </div>
                    <!-- /.col-->
                    <div class="col-sm-6 col-lg-3">
                        <div class="card mb-4 text-white bg-warning">
                            <div class="card-body pb-0 d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="fs-4 fw-semibold">2.49% <span class="fs-6 fw-normal">(84.7%
                                            <svg class="icon">
                                                <use
                                                    xlink:href="/user-coreui/vendors/@coreui/icons/svg/free.svg#cil-arrow-top">
                                                </use>
                                            </svg>)</span></div>
                                    <div>Conversion Rate</div>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-transparent text-white p-0" type="button"
                                        data-coreui-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <svg class="icon">
                                            <use
                                                xlink:href="/user-coreui/vendors/@coreui/icons/svg/free.svg#cil-options">
                                            </use>
                                        </svg>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end"><a class="dropdown-item"
                                            href="#">Action</a><a class="dropdown-item" href="#">Another action</a><a
                                            class="dropdown-item" href="#">Something else here</a></div>
                                </div>
                            </div>
                            <div class="c-chart-wrapper mt-3" style="height:70px;">
                                <canvas class="chart" id="card-chart3" height="70"></canvas>
                            </div>
                        </div>
                    </div>
                    <!-- /.col-->
                    <div class="col-sm-6 col-lg-3">
                        <div class="card mb-4 text-white bg-danger">
                            <div class="card-body pb-0 d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="fs-4 fw-semibold">44K <span class="fs-6 fw-normal">(-23.6%
                                            <svg class="icon">
                                                <use
                                                    xlink:href="/user-coreui/vendors/@coreui/icons/svg/free.svg#cil-arrow-bottom">
                                                </use>
                                            </svg>)</span></div>
                                    <div>Sessions</div>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-transparent text-white p-0" type="button"
                                        data-coreui-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <svg class="icon">
                                            <use
                                                xlink:href="/user-coreui/vendors/@coreui/icons/svg/free.svg#cil-options">
                                            </use>
                                        </svg>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end"><a class="dropdown-item"
                                            href="#">Action</a><a class="dropdown-item" href="#">Another action</a><a
                                            class="dropdown-item" href="#">Something else here</a></div>
                                </div>
                            </div>
                            <div class="c-chart-wrapper mt-3 mx-3" style="height:70px;">
                                <canvas class="chart" id="card-chart4" height="70"></canvas>
                            </div>
                        </div>
                    </div>
                    <!-- /.col-->
                </div>
                <!-- /.row-->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="card-title mb-0">Traffic</h4>
                                <div class="small text-medium-emphasis">January - July 2021</div>
                            </div>
                            <div class="btn-toolbar d-none d-md-block" role="toolbar" aria-label="Toolbar with buttons">
                                <div class="btn-group btn-group-toggle mx-3" data-coreui-toggle="buttons">
                                    <input class="btn-check" id="option1" type="radio" name="options"
                                        autocomplete="off">
                                    <label class="btn btn-outline-secondary"> Day</label>
                                    <input class="btn-check" id="option2" type="radio" name="options"
                                        autocomplete="off" checked="">
                                    <label class="btn btn-outline-secondary active"> Month</label>
                                    <input class="btn-check" id="option3" type="radio" name="options"
                                        autocomplete="off">
                                    <label class="btn btn-outline-secondary"> Year</label>
                                </div>
                                <button class="btn btn-primary" type="button">
                                    <svg class="icon">
                                        <use
                                            xlink:href="/user-coreui/vendors/@coreui/icons/svg/free.svg#cil-cloud-download">
                                        </use>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="c-chart-wrapper" style="height:300px;margin-top:40px;">
                            <canvas class="chart" id="main-chart" height="300"></canvas>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row row-cols-1 row-cols-md-5 text-center">
                            <div class="col mb-sm-2 mb-0">
                                <div class="text-medium-emphasis">Visits</div>
                                <div class="fw-semibold">29.703 Users (40%)</div>
                                <div class="progress progress-thin mt-2">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 40%"
                                        aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                            <div class="col mb-sm-2 mb-0">
                                <div class="text-medium-emphasis">Unique</div>
                                <div class="fw-semibold">24.093 Users (20%)</div>
                                <div class="progress progress-thin mt-2">
                                    <div class="progress-bar bg-info" role="progressbar" style="width: 20%"
                                        aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                            <div class="col mb-sm-2 mb-0">
                                <div class="text-medium-emphasis">Pageviews</div>
                                <div class="fw-semibold">78.706 Views (60%)</div>
                                <div class="progress progress-thin mt-2">
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: 60%"
                                        aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                            <div class="col mb-sm-2 mb-0">
                                <div class="text-medium-emphasis">New Users</div>
                                <div class="fw-semibold">22.123 Users (80%)</div>
                                <div class="progress progress-thin mt-2">
                                    <div class="progress-bar bg-danger" role="progressbar" style="width: 80%"
                                        aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                            <div class="col mb-sm-2 mb-0">
                                <div class="text-medium-emphasis">Bounce Rate</div>
                                <div class="fw-semibold">40.15%</div>
                                <div class="progress progress-thin mt-2">
                                    <div class="progress-bar" role="progressbar" style="width: 40%"
                                        aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.card.mb-4-->
                <div class="row">
                    <div class="col-sm-6 col-lg-4">
                        <div class="card mb-4" style="--cui-card-cap-bg: #3b5998">
                            <div
                                class="card-header position-relative d-flex justify-content-center align-items-center">
                                <svg class="icon icon-3xl text-white my-4">
                                    <use xlink:href="/user-coreui/vendors/@coreui/icons/svg/brand.svg#cib-facebook-f">
                                    </use>
                                </svg>
                                <div class="chart-wrapper position-absolute top-0 start-0 w-100 h-100">
                                    <canvas id="social-box-chart-1" height="90"></canvas>
                                </div>
                            </div>
                            <div class="card-body row text-center">
                                <div class="col">
                                    <div class="fs-5 fw-semibold">89k</div>
                                    <div class="text-uppercase text-medium-emphasis small">friends</div>
                                </div>
                                <div class="vr"></div>
                                <div class="col">
                                    <div class="fs-5 fw-semibold">459</div>
                                    <div class="text-uppercase text-medium-emphasis small">feeds</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.col-->
                    <div class="col-sm-6 col-lg-4">
                        <div class="card mb-4" style="--cui-card-cap-bg: #00aced">
                            <div
                                class="card-header position-relative d-flex justify-content-center align-items-center">
                                <svg class="icon icon-3xl text-white my-4">
                                    <use xlink:href="/user-coreui/vendors/@coreui/icons/svg/brand.svg#cib-twitter">
                                    </use>
                                </svg>
                                <div class="chart-wrapper position-absolute top-0 start-0 w-100 h-100">
                                    <canvas id="social-box-chart-2" height="90"></canvas>
                                </div>
                            </div>
                            <div class="card-body row text-center">
                                <div class="col">
                                    <div class="fs-5 fw-semibold">973k</div>
                                    <div class="text-uppercase text-medium-emphasis small">followers</div>
                                </div>
                                <div class="vr"></div>
                                <div class="col">
                                    <div class="fs-5 fw-semibold">1.792</div>
                                    <div class="text-uppercase text-medium-emphasis small">tweets</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.col-->
                    <div class="col-sm-6 col-lg-4">
                        <div class="card mb-4" style="--cui-card-cap-bg: #4875b4">
                            <div
                                class="card-header position-relative d-flex justify-content-center align-items-center">
                                <svg class="icon icon-3xl text-white my-4">
                                    <use xlink:href="/user-coreui/vendors/@coreui/icons/svg/brand.svg#cib-linkedin">
                                    </use>
                                </svg>
                                <div class="chart-wrapper position-absolute top-0 start-0 w-100 h-100">
                                    <canvas id="social-box-chart-3" height="90"></canvas>
                                </div>
                            </div>
                            <div class="card-body row text-center">
                                <div class="col">
                                    <div class="fs-5 fw-semibold">500+</div>
                                    <div class="text-uppercase text-medium-emphasis small">contacts</div>
                                </div>
                                <div class="vr"></div>
                                <div class="col">
                                    <div class="fs-5 fw-semibold">292</div>
                                    <div class="text-uppercase text-medium-emphasis small">feeds</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.col-->
                </div>
                <!-- /.row-->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card mb-4">
                            <div class="card-header">Traffic &amp; Sales</div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="border-start border-start-4 border-start-info px-3 mb-3">
                                                    <small class="text-medium-emphasis">New Clients</small>
                                                    <div class="fs-5 fw-semibold">9,123</div>
                                                </div>
                                            </div>
                                            <!-- /.col-->
                                            <div class="col-6">
                                                <div class="border-start border-start-4 border-start-danger px-3 mb-3">
                                                    <small class="text-medium-emphasis">Recuring Clients</small>
                                                    <div class="fs-5 fw-semibold">22,643</div>
                                                </div>
                                            </div>
                                            <!-- /.col-->
                                        </div>
                                        <!-- /.row-->
                                        <hr class="mt-0">
                                        <div class="progress-group mb-4">
                                            <div class="progress-group-prepend"><span
                                                    class="text-medium-emphasis small">Monday</span></div>
                                            <div class="progress-group-bars">
                                                <div class="progress progress-thin">
                                                    <div class="progress-bar bg-info" role="progressbar"
                                                        style="width: 34%" aria-valuenow="34" aria-valuemin="0"
                                                        aria-valuemax="100"></div>
                                                </div>
                                                <div class="progress progress-thin">
                                                    <div class="progress-bar bg-danger" role="progressbar"
                                                        style="width: 78%" aria-valuenow="78" aria-valuemin="0"
                                                        aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="progress-group mb-4">
                                            <div class="progress-group-prepend"><span
                                                    class="text-medium-emphasis small">Tuesday</span></div>
                                            <div class="progress-group-bars">
                                                <div class="progress progress-thin">
                                                    <div class="progress-bar bg-info" role="progressbar"
                                                        style="width: 56%" aria-valuenow="56" aria-valuemin="0"
                                                        aria-valuemax="100"></div>
                                                </div>
                                                <div class="progress progress-thin">
                                                    <div class="progress-bar bg-danger" role="progressbar"
                                                        style="width: 94%" aria-valuenow="94" aria-valuemin="0"
                                                        aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="progress-group mb-4">
                                            <div class="progress-group-prepend"><span
                                                    class="text-medium-emphasis small">Wednesday</span></div>
                                            <div class="progress-group-bars">
                                                <div class="progress progress-thin">
                                                    <div class="progress-bar bg-info" role="progressbar"
                                                        style="width: 12%" aria-valuenow="12" aria-valuemin="0"
                                                        aria-valuemax="100"></div>
                                                </div>
                                                <div class="progress progress-thin">
                                                    <div class="progress-bar bg-danger" role="progressbar"
                                                        style="width: 67%" aria-valuenow="67" aria-valuemin="0"
                                                        aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="progress-group mb-4">
                                            <div class="progress-group-prepend"><span
                                                    class="text-medium-emphasis small">Thursday</span></div>
                                            <div class="progress-group-bars">
                                                <div class="progress progress-thin">
                                                    <div class="progress-bar bg-info" role="progressbar"
                                                        style="width: 43%" aria-valuenow="43" aria-valuemin="0"
                                                        aria-valuemax="100"></div>
                                                </div>
                                                <div class="progress progress-thin">
                                                    <div class="progress-bar bg-danger" role="progressbar"
                                                        style="width: 91%" aria-valuenow="91" aria-valuemin="0"
                                                        aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="progress-group mb-4">
                                            <div class="progress-group-prepend"><span
                                                    class="text-medium-emphasis small">Friday</span></div>
                                            <div class="progress-group-bars">
                                                <div class="progress progress-thin">
                                                    <div class="progress-bar bg-info" role="progressbar"
                                                        style="width: 22%" aria-valuenow="22" aria-valuemin="0"
                                                        aria-valuemax="100"></div>
                                                </div>
                                                <div class="progress progress-thin">
                                                    <div class="progress-bar bg-danger" role="progressbar"
                                                        style="width: 73%" aria-valuenow="73" aria-valuemin="0"
                                                        aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="progress-group mb-4">
                                            <div class="progress-group-prepend"><span
                                                    class="text-medium-emphasis small">Saturday</span></div>
                                            <div class="progress-group-bars">
                                                <div class="progress progress-thin">
                                                    <div class="progress-bar bg-info" role="progressbar"
                                                        style="width: 53%" aria-valuenow="53" aria-valuemin="0"
                                                        aria-valuemax="100"></div>
                                                </div>
                                                <div class="progress progress-thin">
                                                    <div class="progress-bar bg-danger" role="progressbar"
                                                        style="width: 82%" aria-valuenow="82" aria-valuemin="0"
                                                        aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="progress-group mb-4">
                                            <div class="progress-group-prepend"><span
                                                    class="text-medium-emphasis small">Sunday</span></div>
                                            <div class="progress-group-bars">
                                                <div class="progress progress-thin">
                                                    <div class="progress-bar bg-info" role="progressbar"
                                                        style="width: 9%" aria-valuenow="9" aria-valuemin="0"
                                                        aria-valuemax="100"></div>
                                                </div>
                                                <div class="progress progress-thin">
                                                    <div class="progress-bar bg-danger" role="progressbar"
                                                        style="width: 69%" aria-valuenow="69" aria-valuemin="0"
                                                        aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.col-->
                                    <div class="col-sm-6">
                                        <div class="row">
                                            <div class="col-6">
                                                <div
                                                    class="border-start border-start-4 border-start-warning px-3 mb-3">
                                                    <small class="text-medium-emphasis">Pageviews</small>
                                                    <div class="fs-5 fw-semibold">78,623</div>
                                                </div>
                                            </div>
                                            <!-- /.col-->
                                            <div class="col-6">
                                                <div
                                                    class="border-start border-start-4 border-start-success px-3 mb-3">
                                                    <small class="text-medium-emphasis">Organic</small>
                                                    <div class="fs-5 fw-semibold">49,123</div>
                                                </div>
                                            </div>
                                            <!-- /.col-->
                                        </div>
                                        <!-- /.row-->
                                        <hr class="mt-0">
                                        <div class="progress-group">
                                            <div class="progress-group-header">
                                                <svg class="icon icon-lg me-2">
                                                    <use
                                                        xlink:href="/user-coreui/vendors/@coreui/icons/svg/free.svg#cil-user">
                                                    </use>
                                                </svg>
                                                <div>Male</div>
                                                <div class="ms-auto fw-semibold">43%</div>
                                            </div>
                                            <div class="progress-group-bars">
                                                <div class="progress progress-thin">
                                                    <div class="progress-bar bg-warning" role="progressbar"
                                                        style="width: 43%" aria-valuenow="43" aria-valuemin="0"
                                                        aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="progress-group mb-5">
                                            <div class="progress-group-header">
                                                <svg class="icon icon-lg me-2">
                                                    <use
                                                        xlink:href="/user-coreui/vendors/@coreui/icons/svg/free.svg#cil-user-female">
                                                    </use>
                                                </svg>
                                                <div>Female</div>
                                                <div class="ms-auto fw-semibold">37%</div>
                                            </div>
                                            <div class="progress-group-bars">
                                                <div class="progress progress-thin">
                                                    <div class="progress-bar bg-warning" role="progressbar"
                                                        style="width: 43%" aria-valuenow="43" aria-valuemin="0"
                                                        aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="progress-group">
                                            <div class="progress-group-header">
                                                <svg class="icon icon-lg me-2">
                                                    <use
                                                        xlink:href="/user-coreui/vendors/@coreui/icons/svg/brand.svg#cib-google">
                                                    </use>
                                                </svg>
                                                <div>Organic Search</div>
                                                <div class="ms-auto fw-semibold me-2">191.235</div>
                                                <div class="text-medium-emphasis small">(56%)</div>
                                            </div>
                                            <div class="progress-group-bars">
                                                <div class="progress progress-thin">
                                                    <div class="progress-bar bg-success" role="progressbar"
                                                        style="width: 56%" aria-valuenow="56" aria-valuemin="0"
                                                        aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="progress-group">
                                            <div class="progress-group-header">
                                                <svg class="icon icon-lg me-2">
                                                    <use
                                                        xlink:href="/user-coreui/vendors/@coreui/icons/svg/brand.svg#cib-facebook-f">
                                                    </use>
                                                </svg>
                                                <div>Facebook</div>
                                                <div class="ms-auto fw-semibold me-2">51.223</div>
                                                <div class="text-medium-emphasis small">(15%)</div>
                                            </div>
                                            <div class="progress-group-bars">
                                                <div class="progress progress-thin">
                                                    <div class="progress-bar bg-success" role="progressbar"
                                                        style="width: 15%" aria-valuenow="15" aria-valuemin="0"
                                                        aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="progress-group">
                                            <div class="progress-group-header">
                                                <svg class="icon icon-lg me-2">
                                                    <use
                                                        xlink:href="/user-coreui/vendors/@coreui/icons/svg/brand.svg#cib-twitter">
                                                    </use>
                                                </svg>
                                                <div>Twitter</div>
                                                <div class="ms-auto fw-semibold me-2">37.564</div>
                                                <div class="text-medium-emphasis small">(11%)</div>
                                            </div>
                                            <div class="progress-group-bars">
                                                <div class="progress progress-thin">
                                                    <div class="progress-bar bg-success" role="progressbar"
                                                        style="width: 11%" aria-valuenow="11" aria-valuemin="0"
                                                        aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="progress-group">
                                            <div class="progress-group-header">
                                                <svg class="icon icon-lg me-2">
                                                    <use
                                                        xlink:href="/user-coreui/vendors/@coreui/icons/svg/brand.svg#cib-linkedin">
                                                    </use>
                                                </svg>
                                                <div>LinkedIn</div>
                                                <div class="ms-auto fw-semibold me-2">27.319</div>
                                                <div class="text-medium-emphasis small">(8%)</div>
                                            </div>
                                            <div class="progress-group-bars">
                                                <div class="progress progress-thin">
                                                    <div class="progress-bar bg-success" role="progressbar"
                                                        style="width: 8%" aria-valuenow="8" aria-valuemin="0"
                                                        aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.col-->
                                </div>
                                <!-- /.row--><br>
                                <div class="table-responsive">
                                    <table class="table border mb-0">
                                        <thead class="table-light fw-semibold">
                                            <tr class="align-middle">
                                                <th class="text-center">
                                                    <svg class="icon">
                                                        <use
                                                            xlink:href="/user-coreui/vendors/@coreui/icons/svg/free.svg#cil-people">
                                                        </use>
                                                    </svg>
                                                </th>
                                                <th>User</th>
                                                <th class="text-center">Country</th>
                                                <th>Usage</th>
                                                <th class="text-center">Payment Method</th>
                                                <th>Activity</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="align-middle">
                                                <td class="text-center">
                                                    <div class="avatar avatar-md"><img class="avatar-img"
                                                            src="/user-coreui/assets/img/avatars/1.jpg"
                                                            alt="user@email.com"><span
                                                            class="avatar-status bg-success"></span></div>
                                                </td>
                                                <td>
                                                    <div>Yiorgos Avraamu</div>
                                                    <div class="small text-medium-emphasis"><span>New</span> |
                                                        Registered: Jan 1, 2020</div>
                                                </td>
                                                <td class="text-center">
                                                    <svg class="icon icon-xl">
                                                        <use
                                                            xlink:href="/user-coreui/vendors/@coreui/icons/svg/flag.svg#cif-us">
                                                        </use>
                                                    </svg>
                                                </td>
                                                <td>
                                                    <div class="clearfix">
                                                        <div class="float-start">
                                                            <div class="fw-semibold">50%</div>
                                                        </div>
                                                        <div class="float-end"><small
                                                                class="text-medium-emphasis">Jun 11, 2020 - Jul 10,
                                                                2020</small></div>
                                                    </div>
                                                    <div class="progress progress-thin">
                                                        <div class="progress-bar bg-success" role="progressbar"
                                                            style="width: 50%" aria-valuenow="50" aria-valuemin="0"
                                                            aria-valuemax="100"></div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <svg class="icon icon-xl">
                                                        <use
                                                            xlink:href="/user-coreui/vendors/@coreui/icons/svg/brand.svg#cib-cc-mastercard">
                                                        </use>
                                                    </svg>
                                                </td>
                                                <td>
                                                    <div class="small text-medium-emphasis">Last login</div>
                                                    <div class="fw-semibold">10 sec ago</div>
                                                </td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button class="btn btn-transparent p-0" type="button"
                                                            data-coreui-toggle="dropdown" aria-haspopup="true"
                                                            aria-expanded="false">
                                                            <svg class="icon">
                                                                <use
                                                                    xlink:href="/user-coreui/vendors/@coreui/icons/svg/free.svg#cil-options">
                                                                </use>
                                                            </svg>
                                                        </button>
                                                        <div class="dropdown-menu dropdown-menu-end"><a
                                                                class="dropdown-item" href="#">Info</a><a
                                                                class="dropdown-item" href="#">Edit</a><a
                                                                class="dropdown-item text-danger" href="#">Delete</a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr class="align-middle">
                                                <td class="text-center">
                                                    <div class="avatar avatar-md"><img class="avatar-img"
                                                            src="/user-coreui/assets/img/avatars/2.jpg"
                                                            alt="user@email.com"><span
                                                            class="avatar-status bg-danger"></span></div>
                                                </td>
                                                <td>
                                                    <div>Avram Tarasios</div>
                                                    <div class="small text-medium-emphasis"><span>Recurring</span> |
                                                        Registered: Jan 1, 2020</div>
                                                </td>
                                                <td class="text-center">
                                                    <svg class="icon icon-xl">
                                                        <use
                                                            xlink:href="/user-coreui/vendors/@coreui/icons/svg/flag.svg#cif-br">
                                                        </use>
                                                    </svg>
                                                </td>
                                                <td>
                                                    <div class="clearfix">
                                                        <div class="float-start">
                                                            <div class="fw-semibold">10%</div>
                                                        </div>
                                                        <div class="float-end"><small
                                                                class="text-medium-emphasis">Jun 11, 2020 - Jul 10,
                                                                2020</small></div>
                                                    </div>
                                                    <div class="progress progress-thin">
                                                        <div class="progress-bar bg-info" role="progressbar"
                                                            style="width: 10%" aria-valuenow="10" aria-valuemin="0"
                                                            aria-valuemax="100"></div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <svg class="icon icon-xl">
                                                        <use
                                                            xlink:href="/user-coreui/vendors/@coreui/icons/svg/brand.svg#cib-cc-visa">
                                                        </use>
                                                    </svg>
                                                </td>
                                                <td>
                                                    <div class="small text-medium-emphasis">Last login</div>
                                                    <div class="fw-semibold">5 minutes ago</div>
                                                </td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button class="btn btn-transparent p-0" type="button"
                                                            data-coreui-toggle="dropdown" aria-haspopup="true"
                                                            aria-expanded="false">
                                                            <svg class="icon">
                                                                <use
                                                                    xlink:href="/user-coreui/vendors/@coreui/icons/svg/free.svg#cil-options">
                                                                </use>
                                                            </svg>
                                                        </button>
                                                        <div class="dropdown-menu dropdown-menu-end"><a
                                                                class="dropdown-item" href="#">Info</a><a
                                                                class="dropdown-item" href="#">Edit</a><a
                                                                class="dropdown-item text-danger" href="#">Delete</a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr class="align-middle">
                                                <td class="text-center">
                                                    <div class="avatar avatar-md"><img class="avatar-img"
                                                            src="/user-coreui/assets/img/avatars/3.jpg"
                                                            alt="user@email.com"><span
                                                            class="avatar-status bg-warning"></span></div>
                                                </td>
                                                <td>
                                                    <div>Quintin Ed</div>
                                                    <div class="small text-medium-emphasis"><span>New</span> |
                                                        Registered: Jan 1, 2020</div>
                                                </td>
                                                <td class="text-center">
                                                    <svg class="icon icon-xl">
                                                        <use
                                                            xlink:href="/user-coreui/vendors/@coreui/icons/svg/flag.svg#cif-in">
                                                        </use>
                                                    </svg>
                                                </td>
                                                <td>
                                                    <div class="clearfix">
                                                        <div class="float-start">
                                                            <div class="fw-semibold">74%</div>
                                                        </div>
                                                        <div class="float-end"><small
                                                                class="text-medium-emphasis">Jun 11, 2020 - Jul 10,
                                                                2020</small></div>
                                                    </div>
                                                    <div class="progress progress-thin">
                                                        <div class="progress-bar bg-warning" role="progressbar"
                                                            style="width: 74%" aria-valuenow="74" aria-valuemin="0"
                                                            aria-valuemax="100"></div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <svg class="icon icon-xl">
                                                        <use
                                                            xlink:href="/user-coreui/vendors/@coreui/icons/svg/brand.svg#cib-cc-stripe">
                                                        </use>
                                                    </svg>
                                                </td>
                                                <td>
                                                    <div class="small text-medium-emphasis">Last login</div>
                                                    <div class="fw-semibold">1 hour ago</div>
                                                </td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button class="btn btn-transparent p-0" type="button"
                                                            data-coreui-toggle="dropdown" aria-haspopup="true"
                                                            aria-expanded="false">
                                                            <svg class="icon">
                                                                <use
                                                                    xlink:href="/user-coreui/vendors/@coreui/icons/svg/free.svg#cil-options">
                                                                </use>
                                                            </svg>
                                                        </button>
                                                        <div class="dropdown-menu dropdown-menu-end"><a
                                                                class="dropdown-item" href="#">Info</a><a
                                                                class="dropdown-item" href="#">Edit</a><a
                                                                class="dropdown-item text-danger" href="#">Delete</a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr class="align-middle">
                                                <td class="text-center">
                                                    <div class="avatar avatar-md"><img class="avatar-img"
                                                            src="/user-coreui/assets/img/avatars/4.jpg"
                                                            alt="user@email.com"><span
                                                            class="avatar-status bg-secondary"></span></div>
                                                </td>
                                                <td>
                                                    <div>Enéas Kwadwo</div>
                                                    <div class="small text-medium-emphasis"><span>New</span> |
                                                        Registered: Jan 1, 2020</div>
                                                </td>
                                                <td class="text-center">
                                                    <svg class="icon icon-xl">
                                                        <use
                                                            xlink:href="/user-coreui/vendors/@coreui/icons/svg/flag.svg#cif-fr">
                                                        </use>
                                                    </svg>
                                                </td>
                                                <td>
                                                    <div class="clearfix">
                                                        <div class="float-start">
                                                            <div class="fw-semibold">98%</div>
                                                        </div>
                                                        <div class="float-end"><small
                                                                class="text-medium-emphasis">Jun 11, 2020 - Jul 10,
                                                                2020</small></div>
                                                    </div>
                                                    <div class="progress progress-thin">
                                                        <div class="progress-bar bg-danger" role="progressbar"
                                                            style="width: 98%" aria-valuenow="98" aria-valuemin="0"
                                                            aria-valuemax="100"></div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <svg class="icon icon-xl">
                                                        <use
                                                            xlink:href="/user-coreui/vendors/@coreui/icons/svg/brand.svg#cib-cc-paypal">
                                                        </use>
                                                    </svg>
                                                </td>
                                                <td>
                                                    <div class="small text-medium-emphasis">Last login</div>
                                                    <div class="fw-semibold">Last month</div>
                                                </td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button class="btn btn-transparent p-0" type="button"
                                                            data-coreui-toggle="dropdown" aria-haspopup="true"
                                                            aria-expanded="false">
                                                            <svg class="icon">
                                                                <use
                                                                    xlink:href="/user-coreui/vendors/@coreui/icons/svg/free.svg#cil-options">
                                                                </use>
                                                            </svg>
                                                        </button>
                                                        <div class="dropdown-menu dropdown-menu-end"><a
                                                                class="dropdown-item" href="#">Info</a><a
                                                                class="dropdown-item" href="#">Edit</a><a
                                                                class="dropdown-item text-danger" href="#">Delete</a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr class="align-middle">
                                                <td class="text-center">
                                                    <div class="avatar avatar-md"><img class="avatar-img"
                                                            src="/user-coreui/assets/img/avatars/5.jpg"
                                                            alt="user@email.com"><span
                                                            class="avatar-status bg-success"></span></div>
                                                </td>
                                                <td>
                                                    <div>Agapetus Tadeáš</div>
                                                    <div class="small text-medium-emphasis"><span>New</span> |
                                                        Registered: Jan 1, 2020</div>
                                                </td>
                                                <td class="text-center">
                                                    <svg class="icon icon-xl">
                                                        <use
                                                            xlink:href="/user-coreui/vendors/@coreui/icons/svg/flag.svg#cif-es">
                                                        </use>
                                                    </svg>
                                                </td>
                                                <td>
                                                    <div class="clearfix">
                                                        <div class="float-start">
                                                            <div class="fw-semibold">22%</div>
                                                        </div>
                                                        <div class="float-end"><small
                                                                class="text-medium-emphasis">Jun 11, 2020 - Jul 10,
                                                                2020</small></div>
                                                    </div>
                                                    <div class="progress progress-thin">
                                                        <div class="progress-bar bg-info" role="progressbar"
                                                            style="width: 22%" aria-valuenow="22" aria-valuemin="0"
                                                            aria-valuemax="100"></div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <svg class="icon icon-xl">
                                                        <use
                                                            xlink:href="/user-coreui/vendors/@coreui/icons/svg/brand.svg#cib-cc-apple-pay">
                                                        </use>
                                                    </svg>
                                                </td>
                                                <td>
                                                    <div class="small text-medium-emphasis">Last login</div>
                                                    <div class="fw-semibold">Last week</div>
                                                </td>
                                                <td>
                                                    <div class="dropdown dropup">
                                                        <button class="btn btn-transparent p-0" type="button"
                                                            data-coreui-toggle="dropdown" aria-haspopup="true"
                                                            aria-expanded="false">
                                                            <svg class="icon">
                                                                <use
                                                                    xlink:href="/user-coreui/vendors/@coreui/icons/svg/free.svg#cil-options">
                                                                </use>
                                                            </svg>
                                                        </button>
                                                        <div class="dropdown-menu dropdown-menu-end"><a
                                                                class="dropdown-item" href="#">Info</a><a
                                                                class="dropdown-item" href="#">Edit</a><a
                                                                class="dropdown-item text-danger" href="#">Delete</a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr class="align-middle">
                                                <td class="text-center">
                                                    <div class="avatar avatar-md"><img class="avatar-img"
                                                            src="/user-coreui/assets/img/avatars/6.jpg"
                                                            alt="user@email.com"><span
                                                            class="avatar-status bg-danger"></span></div>
                                                </td>
                                                <td>
                                                    <div>Friderik Dávid</div>
                                                    <div class="small text-medium-emphasis"><span>New</span> |
                                                        Registered: Jan 1, 2020</div>
                                                </td>
                                                <td class="text-center">
                                                    <svg class="icon icon-xl">
                                                        <use
                                                            xlink:href="/user-coreui/vendors/@coreui/icons/svg/flag.svg#cif-pl">
                                                        </use>
                                                    </svg>
                                                </td>
                                                <td>
                                                    <div class="clearfix">
                                                        <div class="float-start">
                                                            <div class="fw-semibold">43%</div>
                                                        </div>
                                                        <div class="float-end"><small
                                                                class="text-medium-emphasis">Jun 11, 2020 - Jul 10,
                                                                2020</small></div>
                                                    </div>
                                                    <div class="progress progress-thin">
                                                        <div class="progress-bar bg-success" role="progressbar"
                                                            style="width: 43%" aria-valuenow="43" aria-valuemin="0"
                                                            aria-valuemax="100"></div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <svg class="icon icon-xl">
                                                        <use
                                                            xlink:href="/user-coreui/vendors/@coreui/icons/svg/brand.svg#cib-cc-amex">
                                                        </use>
                                                    </svg>
                                                </td>
                                                <td>
                                                    <div class="small text-medium-emphasis">Last login</div>
                                                    <div class="fw-semibold">Yesterday</div>
                                                </td>
                                                <td>
                                                    <div class="dropdown dropup">
                                                        <button class="btn btn-transparent p-0" type="button"
                                                            data-coreui-toggle="dropdown" aria-haspopup="true"
                                                            aria-expanded="false">
                                                            <svg class="icon">
                                                                <use
                                                                    xlink:href="/user-coreui/vendors/@coreui/icons/svg/free.svg#cil-options">
                                                                </use>
                                                            </svg>
                                                        </button>
                                                        <div class="dropdown-menu dropdown-menu-end"><a
                                                                class="dropdown-item" href="#">Info</a><a
                                                                class="dropdown-item" href="#">Edit</a><a
                                                                class="dropdown-item text-danger" href="#">Delete</a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.col-->
                </div>
                <!-- /.row-->
            </div> --}}

            @yield('content')

        </div>

        <footer class="footer">
            {{-- <div><a href="https://coreui.io">CoreUI </a><a href="https://coreui.io">Bootstrap Admin Template</a> © 2021
                creativeLabs.</div>
            <div class="ms-auto">Powered by&nbsp;<a href="https://coreui.io/bootstrap/ui-components/">CoreUI UI
                    Components</a></div> --}}
        </footer>
    </div>



    <!-- CoreUI and necessary plugins-->
    <script src="/user-coreui/vendors/@coreui/coreui/js/coreui.bundle.min.js"></script>
    <script src="/user-coreui/vendors/simplebar/js/simplebar.min.js"></script>

    <!-- Plugins and scripts required by this view-->
    <script src="/user-coreui/vendors/chart.js/js/chart.min.js"></script>
    <script src="/user-coreui/vendors/@coreui/chartjs/js/coreui-chartjs.js"></script>
    <script src="/user-coreui/vendors/@coreui/utils/js/coreui-utils.js"></script>
    {{-- commented out because of chart errors --}}
    {{-- <script src="/user-coreui/js/main.js"></script> --}} 

    {{-- <script class="test" src="{{ asset('js/app.js') }}"></script> --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.4/dist/sweetalert2.all.min.js" integrity="sha256-dOvlmZEDY4iFbZBwD8WWLNMbYhevyx6lzTpfVdo0asA=" crossorigin="anonymous"></script>
    <script src="https://rawgit.com/notifyjs/notifyjs/master/dist/notify.js"></script>

    <script class="test" src="{{ asset('js/advance/base.js') }}"></script>

    @yield('scripts')
</body>

</html>
