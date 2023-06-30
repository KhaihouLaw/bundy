<header class="header header-sticky mb-4" style="box-shadow: 1px 1px 8px 0px rgba(0, 0, 0, 0.2);">
    <div class="container-fluid">
        <button class="header-toggler px-md-0 me-md-3" type="button"
            onclick="coreui.Sidebar.getInstance(document.querySelector('#sidebar')).toggle()">
            <svg class="icon icon-lg">
                <use xlink:href="/user-coreui/vendors/@coreui/icons/svg/free.svg#cil-menu"></use>
            </svg>
        </button>

        <a class="header-brand d-md-none" href="#">
            {{-- <svg width="118" height="46" alt="CoreUI Logo">
                <use xlink:href="/user-coreui/assets/brand/coreui.svg#full"></use>
            </svg> --}}
            LVCC Bundy
        </a>

        <ul class="header-nav d-none d-md-flex">
            <li class="nav-item"><a class="nav-link" href="{{ route('dashboard') }}">Home</a></li>
            {{-- <li class="nav-item"><a class="nav-link">Dashboard</a></li> --}}
            {{-- <li class="nav-item"><a class="nav-link" href="#">Users</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Settings</a></li> --}}
        </ul>

        <ul class="header-nav ms-auto">
            {{-- <li class="nav-item"><a class="nav-link" href="#">
            <svg class="icon icon-lg">
              <use xlink:href="/user-coreui/vendors/@coreui/icons/svg/free.svg#cil-bell"></use>
            </svg></a></li>
        <li class="nav-item"><a class="nav-link" href="#">
            <svg class="icon icon-lg">
              <use xlink:href="/user-coreui/vendors/@coreui/icons/svg/free.svg#cil-list-rich"></use>
            </svg></a></li>
        <li class="nav-item"><a class="nav-link" href="#">
            <svg class="icon icon-lg">
              <use xlink:href="/user-coreui/vendors/@coreui/icons/svg/free.svg#cil-envelope-open"></use>
            </svg></a></li> --}}
        </ul>


        {{-- <ul class="header-nav ms-3">
        <li class="nav-item dropdown"><a class="nav-link py-0" data-coreui-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
            <div class="avatar avatar-md"><img class="avatar-img" src="/user-coreui/assets/img/avatars/8.jpg" alt="user@email.com"></div>
          </a>
          <div class="dropdown-menu dropdown-menu-end pt-0">
            <div class="dropdown-header bg-light py-2">
              <div class="fw-semibold">Account</div>
            </div><a class="dropdown-item" href="#">
              <svg class="icon me-2">
                <use xlink:href="/user-coreui/vendors/@coreui/icons/svg/free.svg#cil-bell"></use>
              </svg> Updates<span class="badge badge-sm bg-info ms-2">42</span></a><a class="dropdown-item" href="#">
              <svg class="icon me-2">
                <use xlink:href="/user-coreui/vendors/@coreui/icons/svg/free.svg#cil-envelope-open"></use>
              </svg> Messages<span class="badge badge-sm bg-success ms-2">42</span></a><a class="dropdown-item" href="#">
              <svg class="icon me-2">
                <use xlink:href="/user-coreui/vendors/@coreui/icons/svg/free.svg#cil-task"></use>
              </svg> Tasks<span class="badge badge-sm bg-danger ms-2">42</span></a><a class="dropdown-item" href="#">
              <svg class="icon me-2">
                <use xlink:href="/user-coreui/vendors/@coreui/icons/svg/free.svg#cil-comment-square"></use>
              </svg> Comments<span class="badge badge-sm bg-warning ms-2">42</span></a>
            <div class="dropdown-header bg-light py-2">
              <div class="fw-semibold">Settings</div>
            </div><a class="dropdown-item" href="#">
              <svg class="icon me-2">
                <use xlink:href="/user-coreui/vendors/@coreui/icons/svg/free.svg#cil-user"></use>
              </svg> Profile</a><a class="dropdown-item" href="#">
              <svg class="icon me-2">
                <use xlink:href="/user-coreui/vendors/@coreui/icons/svg/free.svg#cil-settings"></use>
              </svg> Settings</a><a class="dropdown-item" href="#">
              <svg class="icon me-2">
                <use xlink:href="/user-coreui/vendors/@coreui/icons/svg/free.svg#cil-credit-card"></use>
              </svg> Payments<span class="badge badge-sm bg-secondary ms-2">42</span></a><a class="dropdown-item" href="#">
              <svg class="icon me-2">
                <use xlink:href="/user-coreui/vendors/@coreui/icons/svg/free.svg#cil-file"></use>
              </svg> Projects<span class="badge badge-sm bg-primary ms-2">42</span></a>
            <div class="dropdown-divider"></div><a class="dropdown-item" href="#">
              <svg class="icon me-2">
                <use xlink:href="/user-coreui/vendors/@coreui/icons/svg/free.svg#cil-lock-locked"></use>
              </svg> Lock Account</a><a class="dropdown-item" href="#">
              <svg class="icon me-2">
                <use xlink:href="/user-coreui/vendors/@coreui/icons/svg/free.svg#cil-account-logout"></use>
              </svg> Logout</a>
          </div>
        </li>
      </ul> --}}
    </div>
    <div class="header-divider"></div>
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb my-0 ms-2">
                {{-- <li class="breadcrumb-item">
            <!-- if breadcrumb is single--><span>Home</span>
          </li>
          <li class="breadcrumb-item active"><span>Dashboard</span></li> --}}

                <li class="breadcrumb-item active"><span>Advance</span></li>
            </ol>
        </nav>
    </div>
</header>