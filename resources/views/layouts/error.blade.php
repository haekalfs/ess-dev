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
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script src="{{ asset('js/timesheet.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/file_icon_download.css') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i">

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
        <ul class="navbar-nav @role('freelancer') bg-gradient-success @else bg-gradient-primary @endrole sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="#">
                <div class="sidebar-brand-icon">
                    <img src="{{ asset('img/PC-02.png') }}" style="height: auto; max-width: 65%;" />
                </div>
                <div class="sidebar-brand-text mx-3"></div>
            </a>

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
                        {{-- @usr_acc(203)
                        <h6 class="collapse-header text-danger">Restricted Access:</h6>
                        <a class="collapse-item" href="/approval">Manage Approval</a>
                        @else
                        @endusr_acc --}}
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
                        @usr_acc(303)
                        <h6 class="collapse-header text-danger">Restricted Access:</h6>
                        <a class="collapse-item" href="/development">Leave Report</a>
                        @else
                        @endusr_acc
                        @usr_acc(304)
                        <a class="collapse-item" href="/development">Manage</a>
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
                    <span>Reimburse</span>
                </a>
                <div id="collapseReimburse" class="collapse" aria-labelledby="headingReimburse" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        @usr_acc(502)
                        <h6 class="collapse-header">My Reimburse:</h6>
                        <a class="collapse-item" href="/development">History</a>
                        @else
                        @endusr_acc
                        @usr_acc(503)
                        <h6 class="collapse-header text-danger">Restricted Access:</h6>
                        <a class="collapse-item" href="/development">Manage Reimburse</a>
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
                    <i class="fas fa-fw fa-hand-holding-medical"></i>
                    <span>Medical Reimburse</span>
                </a>
                <div id="collapseMedReimburse" class="collapse" aria-labelledby="headingMedReimburse" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        @usr_acc(602)
                        <h6 class="collapse-header">My Medical Reimburse:</h6>
                        <a class="collapse-item" href="/medical/history">History <small style="color: red;"><i> &nbsp;&nbsp;Medical</i></small></a>
                        @else
                        @endusr_acc
                        @usr_acc(603)
                        <h6 class="collapse-header text-danger">Restricted Access:</h6>
                        <a class="collapse-item" href="/development">Manage Reimburse</a>
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
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseThree"
                    aria-expanded="true" aria-controls="collapseThree">
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

            <!-- Nav Item - Pages Collapse Menu -->
            @usr_acc(901)
            <li class="nav-item @yield('active-page-system_management')">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseSystem"
                    aria-expanded="true" aria-controls="collapseSystem">
                    <i class="fas fa-fw fa-user-cog"></i>
                    <span>System Management</span>
                </a>
                <div id="collapseSystem" class="collapse" aria-labelledby="headingSystem" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Administrator Access:</h6>
                        <a class="collapse-item" href="/manage/users">Manage Users</a>
                        <a class="collapse-item" style="font-size: 12px;" href="/management/security_&_roles/"><i>User Access Controller (UAC)</i></a>
                        <h6 class="collapse-header">Master Data:</h6>
                        <a class="collapse-item" href="/manage/list/employees">Employees Database</a>
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
                    <span>HR Tools</span>
                </a>
                <div id="collapseHRSystem" class="collapse" aria-labelledby="headingHRSystem" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">HR Access:</h6>
                        <a class="collapse-item" href="/hr/compliance/">A1. Compliance</a>
                        <a class="collapse-item" href="/development">A2. Exit Clearance</a>
                        <a class="collapse-item" href="/development">A3. Vendor List</a>
                        <a class="collapse-item" href="/development">A4. News Feed</a>
                        {{-- <a class="collapse-item" href="/hrtools/manage/roles">Manage Roles</a> --}}
                        {{-- <a class="collapse-item" href="/development">User Group</a>
                        <h6 class="collapse-header">Master Data:</h6>
                        <a class="collapse-item" href="/manage/users">List Employees</a>
                        <a class="collapse-item" href="/development">List Consultant</a> --}}
                    </div>
                </div>
            </li>
            @else
            @endusr_acc

            <!-- Divider -->
            <hr class="sidebar-divider">

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

                        @guest

                        @else
                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span
                                    class="mr-2 d-none d-lg-inline text-gray-600 small">{{ Auth::user()->name }}</span>
                                <img class="img-profile rounded-circle" src="{{ asset('img/PC-01A.png') }}">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Activity Log
                                </a>
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

</body>

<script>
function isconfirm(){
	if(!confirm('Are you sure want to do this ?')){
	    event.preventDefault();
	    return;
	}
    return true;
}
</script>

</html>
