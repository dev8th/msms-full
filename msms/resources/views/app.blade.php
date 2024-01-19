<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/x-icon" href="{{url('assets/dist/pic/favicon.ico')}}">
    <title>MisMass Apps</title>
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Bootstrap -->
    <link rel="stylesheet" href="{{ url('assets/plugins/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ url('assets/plugins/select2/css/select2.min.css') }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ url('assets/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ url('assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ url('assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ url('assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <!-- daterange picker -->
    <link rel="stylesheet" href="{{ url('assets/plugins/daterangepicker/daterangepicker.css') }}">
    <!-- sweet alert -->
    <link rel="stylesheet" href="{{ url('assets/plugins/sweetalert2/sweetalert2.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ url('assets/dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ url('assets/customs/css/styleku.css?v='.date('YmdHis')) }}">
    <link rel="stylesheet" href="{{ url('assets/customs/css/custom.css?v='.date('YmdHis')) }}">
    <link rel="stylesheet" href="{{ url('assets/customs/css/newCustom.css?v='.date('YmdHis')) }}">
    <link rel="stylesheet" href="{{ url('assets/customs/css/newLoader.css?v='.date('YmdHis')) }}">
    <link rel="stylesheet" href="{{ url('assets/customs/css/scrollTop.css?v='.date('YmdHis')) }}">
</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed">

    <div class="preloaderz">
            <div class="preloaderz-wrapper">
                <img style="animation:shake 1s infinite" src="{{url('assets/dist/pic/logo-adm-notext.png')}}" height="90" width="90" style='opacity:1'>
                <div style='font-size:20px;color:white;font-weight:bold;margin-top:10px'>Loading...</div>
                <!-- <span class="loaderz"></span> -->
            </div>
        </div>

    <div class="wrapper">

        <!-- Preloader -->
        <!-- <div class="preloader flex-column justify-content-center align-items-center" style="font-size:50px">
            <img class="animation__shake" src="{{url('assets/dist/pic/logo-adm-notext.png')}}" height="60" width="60"> -->
            <!-- <i class="animation__shake fas fa-store-alt brand-image img-circle"></i> -->
        <!-- </div> -->

        <!-- Tempat Topbar -->
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">

            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
            </ul>

            <!-- <ul class="navbar-nav">
                <li class="nav-item">
                    <div class="expired-label">Exp : </div>
                </li>
            </ul> -->

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="#">
                        Halo, {{$username}}<i class="fas fa-user ml-2"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <div class="dropdown-item">
                            <div class="media">
                                <img src="{{url('/assets/dist/img/default.jpg')}}" alt="User Avatar" class="mr-3 img-circle" style="width:80px">
                                <div class="media-body">
                                    <div class="media-idname" style="display:flex">
                                        <h3 class="dropdown-item-title">
                                            {{$username}}
                                        </h3>
                                    </div>
                                    <p style="margin-top:1rem"><a href="#" id="profile" link="{{url('/profile')}}" class="text-sm">View Profile</a></p>
                                    <p><a href="#" onclick="logout()" class="text-sm logout-btn"><i class="fas fa-sign-out"></i> Logout</a></p>
                                </div>
                            </div>
                        </div>
                    </div>

                </li>
            </ul>
        </nav>
        <!-- /.navbar -->

        <!-- Tempat Sidebar -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="/" class="brand-link">
                <img src="{{url('/assets/dist/pic/logo-adm-2.png')}}" alt="AdminLTE Logo" class="brand-image" style="">
                <!--<i class="fas fa-store-alt brand-image img-circle elevation-3"></i>-->
                <!--<span class="brand-text font-weight-light">Londrian</span>-->
            </a>

            <!-- Sidebar -->
            <div class="sidebar">

                <!-- <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image" style="font-size:20px;color:#c2c7d0;margin-left:7px">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div class="info">
                        <a href="#" class="d-block" id="sidebar-balance"></a>
                    </div>
                </div> -->

                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        @if(Auth::user()->dashboard_page)
                        <li class="nav-item">
                            <a href="#" id="dashboard" link="{{url('/dashboard')}}" class="nav-link">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>
                                    Dashboard
                                </p>
                            </a>
                        </li>
                        @endif
                        <!-- <li class="nav-header">Main Menu</li> -->
                        @if(Auth::user()->neworder_page)
                        <li class="nav-item">
                            <a href="#" id="newship" link="{{url('/newship')}}" class="nav-link">
                                <i class="nav-icon fas fa-keyboard"></i>
                                <p>
                                    Create Shipment
                                </p>
                            </a>
                        </li>
                        @endif
                        @if(Auth::user()->shiplist_page)
                        <li class="nav-item">
                            <a href="#" id="shiplist" link="{{url('/shiplist')}}" class="nav-link">
                                <i class="nav-icon fas fa-table"></i>
                                <p>
                                    Shipment List
                                </p>
                            </a>
                        </li>
                        @endif
                        @if(Auth::user()->custlist_page)
                        <li class="nav-item">
                            <a href="#" id="custlist" link="{{url('/custlist')}}" class="nav-link">
                                <i class="nav-icon fas fa-user-tie"></i>
                                <p>
                                    Customer List
                                </p>
                            </a>
                        </li>
                        @endif
                        @if(Auth::user()->warelist_page)
                        <li class="nav-item">
                            <a href="#" id="ware" link="{{url('/warehouse')}}" class="nav-link">
                                <i class="nav-icon fas fa-warehouse"></i>
                                <p>
                                    Warehouse
                                </p>
                            </a>
                        </li>
                        @endif
                        @if(Auth::user()->servlist_page)
                        <li class="nav-item">
                            <a href="#" id="servlist" link="{{url('/servlist')}}" class="nav-link">
                                <i class="nav-icon fas fa-concierge-bell"></i>
                                <p>
                                    Service List
                                </p>
                            </a>
                        </li>
                        @endif
                        @if(Auth::user()->export_page)
                        <li class="nav-item">
                            <a href="#" id="backup" link="{{url('/backup')}}" class="nav-link">
                                <i class="nav-icon fas fa-database"></i>
                                <p>
                                    Export Data
                                </p>
                            </a>
                        </li>
                        @endif
                        @if(Auth::user()->history_page)
                        <li class="nav-item">
                            <a href="#" id="history" link="{{url('/history')}}" class="nav-link">
                                <i class="nav-icon fas fa-history"></i>
                                <p>
                                    History
                                </p>
                            </a>
                        </li>
                        @endif
                    </ul>
                </nav>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">

            <!-- Tempat Konten -->
            <div id="page-content"></div>

        </div>
        <!-- /.content-wrapper -->
        <footer class="main-footer">
            <div class="float-right d-none d-sm-block">
                <b>Version</b> {{env('APP_VERSION')}}
            </div>
            <strong>Copyright &copy; {{ date('Y') }} <a href="#">@MisMass</a>.</strong> All rights reserved.
        </footer>

    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="{{ url('assets/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ url('assets/plugins/jquery-mask/jquery.mask.min.js') }}"></script>
    <script src="{{ url('assets/plugins/jquery-validation/jquery.validate.js') }}"></script>
    <script src="{{ url('assets/plugins/jquery-validation/additional-methods.min.js') }}"></script>
    <!-- date-range-picker -->
    <script src="{{ url('assets/plugins/moment/moment.min.js') }}"></script>
    <script src="{{ url('assets/plugins/daterangepicker/daterangepicker.js') }}"></script>
    <!-- sweetalert2 -->
    <script src="{{ url('assets/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ url('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- DataTables  & Plugins -->
    <script src="{{ url('assets/plugins/datatables/jquery.dataTables.min.js') }}" defer></script>
    <script src="{{ url('assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}" defer></script>
    <script src="{{ url('assets/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}" defer></script>
    <script src="{{ url('assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}" defer></script>
    <script src="{{ url('assets/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}" defer></script>
    <script src="{{ url('assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}" defer></script>
    <!-- Chart -->
    <script src="{{ url('assets/plugins/chart.js/Chart.min.js') }}"></script>
    <script src="{{ url('assets/plugins/select2/js/select2.full.min.js') }}"></script>

    <!-- AdminLTE App -->
    <script src="{{ url('assets/dist/js/adminlte.min.js') }}"></script>
    <!-- Scriptku -->
    <script src="{{ url('assets/customs/js/script.js?v='.env('APP_VERSION')) }}"></script>

    <script>
        $(function() {
            var firstNav = $(".sidebar nav ul li a").first().attr("id"),
                url = location.origin+"/"+firstNav,
                token = "{{ csrf_token() }}";
            
            $(".sidebar nav ul li a").first().addClass("active");
            pageReload(url, token);

            $("nav ul li a[data-widget='pushmenu']").on('click', function() {
                if ($('body').hasClass('sidebar-collapse')) {
                    $(".main-sidebar .brand-link img").attr("src", "{{ url('assets/dist/pic/logo-adm-2.png') }}");
                } else {
                    $(".main-sidebar .brand-link img").attr("src", "{{ url('assets/dist/pic/logo-adm-notext.png') }}");
                }
            });

            $("aside").hover(function() {
                if ($('body').hasClass('sidebar-collapse')) {
                    $(".main-sidebar .brand-link img").attr("src", "{{ url('assets/dist/pic/logo-adm-2.png') }}");
                };
            }, function() {
                if ($('body').hasClass('sidebar-collapse')) {
                    $(".main-sidebar .brand-link img").attr("src", "{{ url('assets/dist/pic/logo-adm-notext.png') }}");
                }
            });

            $('.sidebar .nav-link, #profile').on('click', function() {

                var id = $(this).attr('id');
                var url = $(this).attr('link');
                $('.nav-link').removeClass('active');
                $('#' + id).addClass('active');

                if ($('body').hasClass('sidebar-open')) {
                    $('body').removeClass('sidebar-open');
                    $('body').addClass('sidebar-closed sidebar-collapse');
                }

                token = "{{ csrf_token() }}";
                pageReload(url, token);
                checkSes();

            });

            setInterval(() => {
                checkSes();
            }, 1000 * 60 * 15);

            // $("#example1").DataTable({
            //     "responsive": true,
            //     "lengthChange": false,
            //     "autoWidth": false,
            //     "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
            // }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

        });
    </script>
</body>

</html>