@php
	$pending_adjustments_count = App\Models\TimesheetModificationRequest::pendingCount();
	$pending_adjustments_count = $pending_adjustments_count ? $pending_adjustments_count : null;
	$pending_leave_count = App\Models\LeaveRequest::pendingCount();
	$pending_leave_count = $pending_leave_count ? $pending_leave_count : null;
@endphp
<div class="c-sidebar c-sidebar-dark c-sidebar-fixed c-sidebar-lg-show" id="sidebar">
	<div class="c-sidebar-brand d-lg-down-none">
		<span class="c-sidebar-brand-full font-black text-xl text-center align-middle">LVCC Bundy</span>
		<span class="c-sidebar-brand-minimized font-black text-sm text-center align-middle">LVCC Bundy</span>
	</div>
	<ul class="c-sidebar-nav">
		<li class="c-sidebar-nav-item">
			<a class="c-sidebar-nav-link" href="{{ route('bundy') }}">
				<i class="c-sidebar-nav-icon far fa-clock"></i> 
				Bundy
			</a>
		</li>
		<li class="c-sidebar-nav-item">
			<a class="c-sidebar-nav-link" href="{{ route('admin_dashboard') }}">
				<i class="c-sidebar-nav-icon fas fa-chart-line"></i> 
				Dashboard
			</a>
		</li>

		<li class="c-sidebar-nav-title">
			Attendance
		</li>

		<li class="c-sidebar-nav-item">
			<a class="c-sidebar-nav-link" href="{{ route('admin_attendance_today') }}">
				<i class="c-sidebar-nav-icon fas fa-calendar-day"></i> 
				Attendance Today
			</a>
		</li>

        <li class="c-sidebar-nav-item">
			<a class="c-sidebar-nav-link" href="{{ route('admin_attendance_summary') }}">
				<i class="c-sidebar-nav-icon fas fa-clipboard-list"></i> 
				Attendance History
			</a>
		</li>

		<li class="c-sidebar-nav-item">
			<a class="c-sidebar-nav-link" href="{{ route('admin_attendance_reports') }}">
				<i class="c-sidebar-nav-icon far fa-folder-open"></i>
				Attendance Report
			</a>
		</li>

		<li class="c-sidebar-nav-item">
			<a class="c-sidebar-nav-link" href="{{ route('admin_timesheet_modifications') }}">
				<i class="c-sidebar-nav-icon fas fa-sliders-h"></i> 
				<span>Adjustments</span>
				<span class="badge badge-warning timesheet-modifications-count text-white">{{ $pending_adjustments_count }}</span>
			</a>
		</li>

		<li class="c-sidebar-nav-title">Leave Requests</li>

		<li class="c-sidebar-nav-item">
			<a class="c-sidebar-nav-link" href="{{ route('admin_leave_requests') }}">
				<i class="c-sidebar-nav-icon fas fa-envelope-open-text"></i> 
				<span>Leave Requests</span>
				<span class="badge badge-warning leave-request-count text-white">{{ $pending_leave_count }}</span>
			</a>
		</li>

		<li class="c-sidebar-nav-item">
			<a class="c-sidebar-nav-link" href="{{ route('admin_leave_report') }}">
				<i class="c-sidebar-nav-icon far fa-folder-open"></i>
				Leave Report
			</a>
		</li>
	
		<li class="c-sidebar-nav-divider"></li>

		<li class="c-sidebar-nav-title">Settings</li>

		<li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ route('admin_employee_management') }}">
			<i class="c-sidebar-nav-icon fas fa-user-tie"></i> Employees</a>
		</li>
		{{-- <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ route('admin_academic_year_management') }}">
			<i class="c-sidebar-nav-icon fas fa-graduation-cap"></i> Academic Years</a>
		</li> --}}
		<li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ route('admin_department_management') }}">
			<i class="c-sidebar-nav-icon far fa-building"></i> Departments</a>
		</li>
		<li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ route('admin_approvers') }}">
			<i class="c-sidebar-nav-icon cil-puzzle"></i> Approvers</a>
		</li>
		<li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ route('admin_holidays') }}">
			<i class="c-sidebar-nav-icon cil-puzzle"></i> Holidays / Events</a>
		</li>
		{{-- <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ route('admin_advance_punch_clock_management') }}">
			<i class="c-sidebar-nav-icon cil-puzzle"></i> Face / QR</a>
		</li> --}}
	</ul>

	<button class="c-sidebar-minimizer c-class-toggler" type="button" data-target="_parent"
		data-class="c-sidebar-minimized">
	</button>

</div>