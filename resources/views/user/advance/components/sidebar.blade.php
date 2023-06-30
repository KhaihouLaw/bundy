@php
    $is_supervisor = Auth::user()->isSupervisor();
    $is_approver = Auth::user()->isApprover();
    $approver_departments = null;
    $supervisor_departments = null;
    if ($is_supervisor) {
        $supervisor_departments = Auth::user()->getSupervisedDepartments();
    } 
    if ($is_approver) {
        $approver_departments = Auth::user()->getApproverAssignedDepartments();
    }
@endphp
<div class="sidebar bg-white sidebar-fixed" id="sidebar" style="box-shadow: 1px 1px 8px 0px rgba(0, 0, 0, 0.2);">
    <div class="sidebar-brand d-none d-md-flex">
        {{-- <svg class="sidebar-brand-full" width="118" height="46" alt="CoreUI Logo">
		<use xlink:href="/user-coreui/assets/brand/coreui.svg#full"></use>
	  </svg> --}}
	  
        <img src="/images/homepage_images/lvcc.png" alt="LVCC Logo" class="w-28 fill-current" style="width: 50px">

        {{-- <svg class="sidebar-brand-narrow" width="46" height="46" alt="CoreUI Logo">
		<use xlink:href="/user-coreui/assets/brand/coreui.svg#signet"></use>
	  </svg> --}}
    </div>
    <ul class="sidebar-nav" data-coreui="navigation" data-simplebar="">
        {{-- <li class="nav-item"><a class="nav-link" href="index.html">
                <svg class="nav-icon">
                    <use xlink:href="/user-coreui/vendors/@coreui/icons/svg/free.svg#cil-speedometer"></use>
                </svg> Dashboard<span class="badge badge-sm bg-info ms-auto">NEW</span></a></li>

        <li class="nav-title">Theme</li>
        <li class="nav-item"><a class="nav-link" href="colors.html">
                <svg class="nav-icon">
                    <use xlink:href="/user-coreui/vendors/@coreui/icons/svg/free.svg#cil-drop"></use>
                </svg> Colors</a></li>
        <li class="nav-item"><a class="nav-link" href="typography.html">
                <svg class="nav-icon">
                    <use xlink:href="/user-coreui/vendors/@coreui/icons/svg/free.svg#cil-pencil"></use>
                </svg> Typography</a></li> --}}

                
        {{-- <li class="nav-title">Components</li> --}}
        {{-- <li class="nav-group">
            <a class="nav-link nav-group-toggle" href="#">
                <svg class="nav-icon">
                    <use xlink:href="/user-coreui/vendors/@coreui/icons/svg/free.svg#cil-puzzle"></use>
                </svg>
                Leave
            </a>
            <ul class="nav-group-items">
                <li class="nav-item">
                    <a class="nav-link" href="base/accordion.html">
                        <span class="nav-icon"></span> Leave Requests
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="base/breadcrumb.html">
                        <span class="nav-icon"></span> Breadcrumb
                    </a>
                </li>

                <li class="nav-item"><a class="nav-link" href="base/cards.html"><span
                            class="nav-icon"></span> Cards</a></li>
                <li class="nav-item"><a class="nav-link" href="base/carousel.html"><span
                            class="nav-icon"></span> Carousel</a></li>
                <li class="nav-item"><a class="nav-link" href="base/collapse.html"><span
                            class="nav-icon"></span> Collapse</a></li>
                <li class="nav-item"><a class="nav-link" href="base/list-group.html"><span
                            class="nav-icon"></span> List group</a></li>
                <li class="nav-item"><a class="nav-link" href="base/navs.html"><span
                            class="nav-icon"></span> Navs</a></li>
                <li class="nav-item"><a class="nav-link" href="base/pagination.html"><span
                            class="nav-icon"></span> Pagination</a></li>
                <li class="nav-item"><a class="nav-link" href="base/popovers.html"><span
                            class="nav-icon"></span> Popovers</a></li>
                <li class="nav-item"><a class="nav-link" href="base/progress.html"><span
                            class="nav-icon"></span> Progress</a></li>
                <li class="nav-item"><a class="nav-link" href="base/scrollspy.html"><span
                            class="nav-icon"></span> Scrollspy</a></li>
                <li class="nav-item"><a class="nav-link" href="base/spinners.html"><span
                            class="nav-icon"></span> Spinners</a></li>
                <li class="nav-item"><a class="nav-link" href="base/tables.html"><span
                            class="nav-icon"></span> Tables</a></li>
                <li class="nav-item"><a class="nav-link" href="base/tabs.html"><span
                            class="nav-icon"></span> Tabs</a></li>
                <li class="nav-item"><a class="nav-link" href="base/tooltips.html"><span
                            class="nav-icon"></span> Tooltips</a></li>
            </ul>
        </li> --}}


        @if ($is_supervisor && !empty($supervisor_departments->toArray()))
            <li class="nav-title">Supervised Departments</li>
            @foreach ($supervisor_departments as $department)
            @php
                $pending_adjustments_count = App\Models\TimesheetModificationRequest::deptPendingCount($department->id);
                $pending_adjustments_count = $pending_adjustments_count ? $pending_adjustments_count : null;
                $pending_leave_count = App\Models\LeaveRequest::deptPendingCount($department->id);
                $pending_leave_count = $pending_leave_count ? $pending_leave_count : null;
            @endphp
            <li class="nav-group">
                <a class="nav-link nav-group-toggle pointer-none" href="#" data-coreui-toggle="tooltip" data-coreui-original-title="{{ __($department->getDepartment()) }}">
                    <i class="nav-icon fa fa-building"></i>
                    <p class="truncate">{{ __($department->getDepartment()) }}</p>
                    @if (!empty($pending_adjustments_count) || !empty($pending_leave_count))
                        <span class="position-absolute top-2 right-0 translate-middle bg-warning rounded-circle bg-gradient shadow-sm" style="padding: 5px !important;">
                            <span class="visually-hidden">New alerts</span>
                        </span>
                    @endif
                </a>
                <ul class="nav-group-items">
                    {{-- <li class="nav-item">
                        <a class="nav-link">
                            <span class="nav-icon"></span> Attendance History
                        </a>
                    </li> --}}

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('advance.supervisor.department_timesheet_adjustments', $department->id) }}">
                            <span class="nav-icon"></span>
                            <span class="truncate">Timesheet Adjustments</span>
                            <span class="badge bg-warning bg-gradient ms-auto shadow-sm">{{ $pending_adjustments_count }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('advance.supervisor.department_leave_requests', $department->id) }}">
                            <span class="nav-icon"></span>
                            <span>Leave Requests</span>
                            <span class="badge bg-warning bg-gradient ms-auto shadow-sm">{{ $pending_leave_count }}</span>
                        </a>
                    </li>
                </ul>
            </li>
            @endforeach
        @endif
        @if ($is_approver && !empty($approver_departments->toArray()))
            <li class="nav-title">Assigned Departments</li>
            @foreach ($approver_departments as $department)
            @php
                $pending_adjustments_count = App\Models\TimesheetModificationRequest::deptPendingCount($department->id);
                $pending_adjustments_count = $pending_adjustments_count ? $pending_adjustments_count : null;
                $pending_leave_count = App\Models\LeaveRequest::deptPendingCount($department->id);
                $pending_leave_count = $pending_leave_count ? $pending_leave_count : null;
            @endphp
            <li class="nav-group">
                <a class="nav-link nav-group-toggle" href="#" data-coreui-toggle="tooltip" data-coreui-original-title="{{ __($department->getDepartment()) }}">
                    <i class="nav-icon fa fa-building"></i>
                    <p class="truncate">{{ __($department->getDepartment()) }}</p>
                    @if (!empty($pending_adjustments_count) || !empty($pending_leave_count))
                        <span class="position-absolute top-2 right-0 translate-middle bg-warning rounded-circle bg-gradient shadow-sm" style="padding: 5px !important;">
                            <span class="visually-hidden">New alerts</span>
                        </span>
                    @endif
                </a>
                <ul class="nav-group-items">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('advance.supervisor.department_timesheet_adjustments', $department->id) }}">
                            <span class="nav-icon"></span>
                            <span class="truncate">Timesheet Adjustments</span>
                            <span class="badge bg-warning bg-gradient ms-auto shadow-sm">{{ $pending_adjustments_count }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('advance.supervisor.department_leave_requests', $department->id) }}">
                            <span class="nav-icon"></span>
                            <span>Leave Requests</span>
                            <span class="badge bg-warning bg-gradient ms-auto shadow-sm">{{ $pending_leave_count }}</span>
                        </a>
                    </li>
                </ul>
            </li>
            @endforeach
        @endif

        {{-- <li class="nav-item"><a class="nav-link" href="charts.html">
                <svg class="nav-icon">
                    <use xlink:href="/user-coreui/vendors/@coreui/icons/svg/free.svg#cil-chart-pie"></use>
                </svg> Charts</a></li>
        <li class="nav-group"><a class="nav-link nav-group-toggle" href="#">
                <svg class="nav-icon">
                    <use xlink:href="/user-coreui/vendors/@coreui/icons/svg/free.svg#cil-notes"></use>
                </svg> Forms</a>
            <ul class="nav-group-items">
                <li class="nav-item"><a class="nav-link" href="forms/form-control.html"> Form Control</a>
                </li>
                <li class="nav-item"><a class="nav-link" href="forms/select.html"> Select</a></li>
                <li class="nav-item"><a class="nav-link" href="forms/checks-radios.html"> Checks and
                        radios</a></li>
                <li class="nav-item"><a class="nav-link" href="forms/range.html"> Range</a></li>
                <li class="nav-item"><a class="nav-link" href="forms/input-group.html"> Input group</a></li>
                <li class="nav-item"><a class="nav-link" href="forms/floating-labels.html"> Floating
                        labels</a></li>
                <li class="nav-item"><a class="nav-link" href="forms/layout.html"> Layout</a></li>
                <li class="nav-item"><a class="nav-link" href="forms/validation.html"> Validation</a></li>
            </ul>
        </li>
        <li class="nav-group"><a class="nav-link nav-group-toggle" href="#">
                <svg class="nav-icon">
                    <use xlink:href="/user-coreui/vendors/@coreui/icons/svg/free.svg#cil-star"></use>
                </svg> Icons</a>
            <ul class="nav-group-items">
                <li class="nav-item"><a class="nav-link" href="icons/coreui-icons-free.html"> CoreUI
                        Icons<span class="badge badge-sm bg-success ms-auto">Free</span></a></li>
                <li class="nav-item"><a class="nav-link" href="icons/coreui-icons-brand.html"> CoreUI
                        Icons - Brand</a></li>
                <li class="nav-item"><a class="nav-link" href="icons/coreui-icons-flag.html"> CoreUI Icons
                        - Flag</a></li>
            </ul>
        </li>
        <li class="nav-group"><a class="nav-link nav-group-toggle" href="#">
                <svg class="nav-icon">
                    <use xlink:href="/user-coreui/vendors/@coreui/icons/svg/free.svg#cil-bell"></use>
                </svg> Notifications</a>
            <ul class="nav-group-items">
                <li class="nav-item"><a class="nav-link" href="notifications/alerts.html"><span
                            class="nav-icon"></span> Alerts</a></li>
                <li class="nav-item"><a class="nav-link" href="notifications/badge.html"><span
                            class="nav-icon"></span> Badge</a></li>
                <li class="nav-item"><a class="nav-link" href="notifications/modals.html"><span
                            class="nav-icon"></span> Modals</a></li>
                <li class="nav-item"><a class="nav-link" href="notifications/toasts.html"><span
                            class="nav-icon"></span> Toasts</a></li>
            </ul>
        </li>
        <li class="nav-item"><a class="nav-link" href="widgets.html">
                <svg class="nav-icon">
                    <use xlink:href="/user-coreui/vendors/@coreui/icons/svg/free.svg#cil-calculator"></use>
                </svg> Widgets<span class="badge badge-sm bg-info ms-auto">NEW</span></a></li>
        <li class="nav-divider"></li> --}}

        {{-- <li class="nav-title">PUNCH CLOCK</li> --}}

        {{-- <li class="nav-group">
            <a class="nav-link nav-group-toggle" href="#">
                <svg class="nav-icon">
                    <use xlink:href="/user-coreui/vendors/@coreui/icons/svg/free.svg#cil-star"></use>
                </svg> 
                Pages
            </a>
            <ul class="nav-group-items">
                <li class="nav-item">
                    <a class="nav-link" href="login.html" target="_top">
                        <svg class="nav-icon">
                            <use xlink:href="/user-coreui/vendors/@coreui/icons/svg/free.svg#cil-account-logout"></use>
                        </svg> 
                        Login
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="register.html" target="_top">
                        <svg class="nav-icon">
                            <use xlink:href="/user-coreui/vendors/@coreui/icons/svg/free.svg#cil-account-logout"></use>
                        </svg> 
                        Register
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="404.html" target="_top">
                        <svg class="nav-icon">
                            <use xlink:href="/user-coreui/vendors/@coreui/icons/svg/free.svg#cil-bug"></use>
                        </svg> 
                        Error 404
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="500.html" target="_top">
                        <svg class="nav-icon">
                            <use xlink:href="/user-coreui/vendors/@coreui/icons/svg/free.svg#cil-bug"></use>
                        </svg> 
                        Error 500
                    </a>
                </li>
            </ul>
        </li> --}}

        {{-- <li class="nav-item">
            <a class="nav-link" href="{{ route('face-recognition.register') }}">
                <svg class="nav-icon">
                    <use xlink:href="/user-coreui/vendors/@coreui/icons/svg/free.svg#cil-description"></use>
                </svg> 
                Register Face
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link nav-link-danger" href="{{ route('punch_clock.authorize') }}" target="_top">
                <svg class="nav-icon">
                    <use xlink:href="/user-coreui/vendors/@coreui/icons/svg/free.svg#cil-layers"></use>
                </svg> 
                Instance
            </a>
        </li> --}}

    </ul>
    <button class="sidebar-toggler" type="button" data-coreui-toggle="unfoldable"></button>
</div>
