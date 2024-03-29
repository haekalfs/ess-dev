<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Favicons -->
    <link href="{{ asset('img/PC-01A.png') }}" rel="icon">
    <link href="{{ asset('img/PC-01A.png') }}" rel="apple-touch-icon">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'ESS Perdana')</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <!-- Place the first <script> tag in your HTML's <head> -->
    <script src="https://cdn.tiny.cloud/1/7151mpnl7wa2vt6edvg8mqikfi69pmviecvx90uiq1uy20bc/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/file_icon_download.css') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i">

    @yield('css-js-if-exist')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Custom fonts for this template-->
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <!-- Custom styles for this template-->
    <link href="{{ asset('css/sb-admin-2.css') }}" rel="stylesheet">
    <!-- Custom styles for this page -->
    <link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">

    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>
</head>

<body id="page-top">
    <div id="wrapper">
        <!-- Sidebar -->
        <ul class="navbar-nav @role('non-internal') bg-gradient-success  @else bg-gradient-primary @endrole sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="#">
                <div class="sidebar-brand-icon">
                    <img src="{{ asset('img/PC-02.png') }}" style="height: auto; max-width: 65%;" />
                </div>
                <div class="sidebar-brand-text mx-3"></div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">


            <!-- Nav Item - Dashboard -->
            <li class="nav-item @yield('active-page-db')">
                <a class="nav-link" href="/home">
                    <i class="fas fa-fw fa-home"></i>
                    <span>Home</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">
            <!-- Heading -->
            <div class="sidebar-heading">
                Main Menu
            </div>

            <li class="nav-item @yield('active-page-myprofile')">
                <a class="nav-link" href="/myprofile">
                    <i class="fas fa-fw fa-user-circle"></i>
                    <span>My Profile</span>
                </a>
            </li>
            @usr_acc(101)
            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item @yield('active-page-timesheet')">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo"
                    aria-expanded="true" aria-controls="collapseTwo">
                    <i class="fas fa-fw fa-calendar"></i>
                    <span>Time Report</span>
                </a>
                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        @usr_acc(102)
                        <h6 class="collapse-header">My Timereport:</h6>
                        <a class="collapse-item" href="/timesheet">Timesheet</a>
                        @else
                        @endusr_acc
                        @usr_acc(103)
                        <a class="collapse-item" href="/timesheet/summary/all">Summary</a>
                        @else
                        @endusr_acc
                        @usr_acc(104)
                        <h6 class="collapse-header text-danger">Restricted Access:</h6>
                        <a class="collapse-item" href="/timesheet/review/fm">Review<small style="color: red;"><i> &nbsp;&nbsp;Finance Manager</i></small></a>
                        @else
                        @endusr_acc
                    </div>
                </div>
            </li>
            @else
            @endusr_acc
            @usr_acc(201)
            <!-- Nav Item - Utilities Collapse Menu -->
            <li class="nav-item @yield('active-page-approval')">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities"
                    aria-expanded="true" aria-controls="collapseUtilities">
                    <i class="fas fa-fw fa-check-circle"></i>
                    <span>Approval</span>
                </a>
                <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        @usr_acc(202)
                        <h6 class="collapse-header">Prior Approval:</h6>
                        <a class="collapse-item" href="/approval">Approval</a>
                        @else
                        @endusr_acc
                        @usr_acc(203)
                        <h6 class="collapse-header text-danger">Restricted Access:</h6>
                        <a class="collapse-item" href="/approval-history/">Approval History <span class="text-danger"><i><small>New</small></i></span></a>
                        @else
                        @endusr_acc
                    </div>
                </div>
            </li>
            @else
            @endusr_acc

            @usr_acc(301)
            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item @yield('active-page-leave')">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseLeave"
                    aria-expanded="true" aria-controls="collapseLeave">
                    <i class="fas fa-fw fa-plane-departure"></i>
                    <span>Leave</span>
                </a>
                <div id="collapseLeave" class="collapse" aria-labelledby="headingLeave" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        @usr_acc(302)
                        <h6 class="collapse-header">My Leaves:</h6>
                        <a class="collapse-item" href="/leave/history">History</a>
                        @else
                        @endusr_acc
                        @usr_acc(304)
                        <a class="collapse-item" href="/leave/manage/all">Manage Quota</a>
                        @else
                        @endusr_acc
                        @usr_acc(303)
                        <h6 class="collapse-header text-danger">Restricted Access:</h6>
                        <a class="collapse-item" href="/leave/request/manage/all">Emp. Leave Requests</a>
                        @else
                        @endusr_acc
                        @usr_acc(305)
                        <a class="collapse-item" href="/development">Timesheet &nbsp;<i class="fas fa-fw fa-exchange-alt"></i>&nbsp; Leave</a>
                        @else
                        @endusr_acc
                    </div>
                </div>
            </li>
            @else
            @endusr_acc

            @usr_acc(501)
            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item @yield('active-page-reimburse')">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseReimburse"
                    aria-expanded="true" aria-controls="collapseReimburse">
                    <i class="fas fa-fw fa-money-check-alt"></i>
                    <span>Reimbursement</span>
                </a>
                <div id="collapseReimburse" class="collapse" aria-labelledby="headingReimburse" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        @usr_acc(502)
                        <h6 class="collapse-header">My Reimburse:</h6>
                        <a class="collapse-item" href="/reimbursement/history">History</a>
                        @else
                        @endusr_acc
                        @usr_acc(503)
                        <h6 class="collapse-header text-danger">Restricted Access:</h6>
                        <a class="collapse-item" href="/reimbursement/manage/">Manage</a>
                        @else
                        @endusr_acc
                    </div>
                </div>
            </li>
            @else
            @endusr_acc

            @usr_acc(601)
            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item @yield('active-page-med-reimburse')">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseMedReimburse"
                    aria-expanded="true" aria-controls="collapseMedReimburse">
                    <i class="fas fa-briefcase-medical"></i>
                    <span>Medical Reimburse</span>
                </a>
                <div id="collapseMedReimburse" class="collapse" aria-labelledby="headingMedReimburse" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        @usr_acc(602)
                        <h6 class="collapse-header">My Medical Reimburse:</h6>
                        <a class="collapse-item" href="/medical/history">History</a>
                        @else
                        @endusr_acc
                        @usr_acc(603)
                        <h6 class="collapse-header text-danger">Restricted Access:</h6>
                        <a class="collapse-item" href="/medical/review">Review <small style="color: red;"><i> &nbsp;&nbsp;Finance Manager</i></small></a>
                        <a class="collapse-item" href="/medical/manage">Manage Medical <small style="color: red;"><i> &nbsp;&nbsp;FM</i></small></a>
                        @else
                        @endusr_acc
                    </div>
                </div>
            </li>
            @else
            @endusr_acc

            @usr_acc(401)
            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item @yield('active-page-project')">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseThree" aria-expanded="true" aria-controls="collapseThree">
                    <i class="fas fa-fw fa-code-branch"></i>
                    <span>Project Assignment</span>
                </a>
                <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Project Assignment:</h6>
                        <a class="collapse-item" href="/myprojects">MyProjects</a>
                        @usr_acc(403)
                        <h6 class="collapse-header text-danger">Restricted Access:</h6>
                        <a class="collapse-item" href="/assignment">Project Assignment</a>
                        <a class="collapse-item" href="/project_list">Project Organization</a>
                        <a class="collapse-item" href="/assignment/requested/by/user">Requested Assignment</a>
                        @else
                        @endusr_acc
                    </div>
                </div>
            </li>
            @else
            @endusr_acc
            <!-- Divider -->
            <hr class="sidebar-divider">

            @usr_acc(901)
            <!-- Heading -->
            <div class="sidebar-heading">
                System & Data
            </div>
            @endusr_acc

            <!-- Nav Item - Pages Collapse Menu -->
            @usr_acc(901)
            <li class="nav-item @yield('active-page-system_management')">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseSystem"
                    aria-expanded="true" aria-controls="collapseSystem">
                    <i class="fas fa-fw fa-user-cog"></i>
                    <span>Administration</span>
                </a>
                <div id="collapseSystem" class="collapse" aria-labelledby="headingSystem" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Account Administration:</h6>
                        <a class="collapse-item" href="/manage/users">A1. Manage Users</a>
                        <a class="collapse-item" style="font-size: 12px;" href="/management/security_&_roles/"><i>A2. User Access C. (UAC)</i></a>
                        <h6 class="collapse-header" style="font-size: 9px;">Employement Administration:</h6>
                        <a class="collapse-item" href="/manage/list/employees">A3. Emp. Database & CV</a>
                        <a class="collapse-item" href="/hr/exit_clearance/">A4. Exit Clearance</a>
                    </div>
                </div>
            </li>
            @else
            @endusr_acc
            <!-- Nav Item - Pages Collapse Menu -->
            @usr_acc(999)
            <li class="nav-item @yield('active-page-HR')">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseHRSystem"
                    aria-expanded="true" aria-controls="collapseHRSystem">
                    <i class="fas fa-fw fa-tools"></i>
                    <span>System Management</span>
                </a>
                <div id="collapseHRSystem" class="collapse" aria-labelledby="headingHRSystem" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">HR Access:</h6>
                        <a class="collapse-item" href="/hr/compliance/">B1. Compliance</a>
                        <a class="collapse-item" href="/vendor-list">B2. Vendor List</a>
                        <a class="collapse-item" href="/news-feed/manage">B3. News Feed</a>
                        <a class="collapse-item" href="/company-regulation/commands">B4. Commands</a>
                        <a class="collapse-item" href="{{ route('holiday.date') }}">B5. Add Holidays</a>
                        {{-- <a class="collapse-item" href="/development">User Group</a>
                        <h6 class="collapse-header">Master Data:</h6>
                        <a class="collapse-item" href="/manage/users">List Employees</a>
                        <a class="collapse-item" href="/development">List Consultant</a> --}}
                    </div>
                </div>
            </li>
            @else
            @endusr_acc

            @usr_acc(999)
            {{-- <li class="nav-item @yield('active-page-cv-creator')">
                <a class="nav-link" href="{{ route('mycv') }}">
                    <i class="fas fa-passport"></i>
                    <span>CV Creator</span>
                </a>
            </li> --}}
            <li class="nav-item @yield('active-page-perform.metrics')">
                <a class="nav-link" href="{{ route('kpi') }}">
                    <i class="fas fa-chart-line"></i>
                    <span>Emp. Performance Metrics</span>
                </a>
            </li>
            @endusr_acc

            <!-- Divider -->
            @usr_acc(901)
            <hr class="sidebar-divider">
            @endusr_acc

            <!-- Heading -->
            <div class="sidebar-heading">
                Having Troubles?
            </div>

            <!-- Nav Item - Tables -->
            <li class="nav-item">
                <a class="nav-link" href="mailto:haekal@perdana.co.id">
                    <i class="fas fa-fw fa-paper-plane"></i>
                    <span>Contact Administrator</span></a>
            </li>

            <li class="nav-item @yield('active-page-apps')">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseApps"
                    aria-expanded="true" aria-controls="collapseApps">
                    <i class="fas fa-fw fa-user-cog"></i>
                    <span>Browse Apps</span>
                </a>
                <div id="collapseApps" class="collapse" aria-labelledby="headingApps" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item" target="_blank" href="https://e-form.perdana.co.id/">eForm - Forms Controller</a>
                        <a class="collapse-item" target="_blank" href="https://myspace.perdana.co.id/">MySpace - Cloud Storage</a>
                        <a class="collapse-item" href="#">AMS - Assets Controller</a>
                    </div>
                </div>
            </li>


            <li class="nav-item">
                <a class="nav-link" href="{{ route('logout') }}"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>{{ __('Logout') }}</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

            <br>
            <style>
                .text {
                    font-size: small;
                    color: white;
                }

            </style>
            @yield('sidebar-info-ssl')
        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                                aria-labelledby="searchDropdown">
                                <form class="form-inline mr-auto w-100 navbar-search">
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light border-0 small"
                                            placeholder="Search for..." aria-label="Search"
                                            aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button">
                                                <i class="fas fa-search fa-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </li>

                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-bell fa-fw"></i>
                                <!-- Counter - Alerts -->
                                @if ($notifications->isEmpty() || $notificationsCount == 0)
                                @else
                                <span class="badge badge-danger badge-counter">
                                    {{ $notificationsCount }}
                                </span>
                                @endif
                            </a>
                            <!-- Dropdown - Alerts -->
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="alertsDropdown">
                                <h6 class="dropdown-header">
                                    Alerts Center
                                </h6>
                                @if ($notifications->isEmpty())
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <span class="text-gray-500">No Data Available</span>
                                </a>
                                @else
                                    @foreach ($notifications as $notification)
                                        <a class="dropdown-item notification-item d-flex align-items-center" data-notification-id="{{ $notification->id }}">
                                            @if($notification->importance == 1)
                                            <div class="mr-3">
                                                <div class="icon-circle bg-success">
                                                    <i class="fas fa-check-square text-white"></i>
                                                </div>
                                            </div>
                                            @else
                                            <div class="mr-3">
                                                <div class="icon-circle bg-warning">
                                                    <i class="fas fa-exclamation-triangle text-white"></i>
                                                </div>
                                            </div>
                                            @endif
                                            <div>
                                                <div class="small text-gray-500">{{ $notification->created_at }}</div>
                                                @if($notification->read_stat == 1)
                                                <span>{{ $notification->message}}</span>
                                                @else
                                                <span class="font-weight-bold">{{ $notification->message}}</span>
                                                @endif
                                            </div>
                                        </a>
                                    @endforeach
                                @endif
                                <a class="dropdown-item text-center small text-gray-500" href="/notification-center/{{ Crypt::encrypt(Auth::id()) }}">Show All Notifications</a>
                            </div>
                        </li>

                        @guest

                        @else
                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span
                                    class="mr-2 d-none d-lg-inline text-gray-600 small">{{ Auth::user()->name }}</span>
                                @if(Auth::user()->users_detail->profile_pic)
                                <img class="img-profile rounded-circle"  src="{{ url('/images_storage/'.Auth::user()->users_detail->profile_pic) }}">
                                @else
                                <img class="img-profile rounded-circle" src="{{ asset('img/PC-01A.png') }}">
                                @endif
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="{{ route('myprofile') }}">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Activity Log
                                </a>
                                {{-- <a class="dropdown-item" href="#">
                                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Dark Mode
                                </a> --}}
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    {{ __('Logout') }}
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>
                        @endguest

                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid" style="zoom: 90%;">


                    @yield('landing-page')
                    @yield('content')




                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Perdana Consulting <?php echo date("Y"); ?></span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Bootstrap core JavaScript-->
    @yield('javascript')
    {{-- <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script> --}}
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Core plugin JavaScript-->
    <script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>

    <!-- Custom scripts for all pages-->
    <script src="{{ asset('js/sb-admin-2.min.js') }}"></script>

    <!-- Page level plugins -->
    <script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>

    <!-- Page level custom scripts -->
    <script src="{{ asset('js/demo/datatables-demo.js') }}"></script>

    <script src="{{ asset('js/home.js') }}"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

</body>

</html>
