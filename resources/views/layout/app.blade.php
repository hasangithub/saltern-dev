<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Saltern')</title>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- DataTables CSS with Bootstrap 4 integration -->

    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.css">



    @yield('styles')
    <!-- For additional styles -->
    <style>
    .form-control {
        height: calc(1.8125rem + 2px);
        /* Match Bootstrap form-control-sm height */
        padding: .25rem .5rem;
        font-size: .875rem;
    }

    .table {
        font-size: 14px;
        /* smaller font */
    }

    table .btn {
        padding: 0.25rem 0.4rem;
        font-size: 0.60rem;
        /* smaller font */
        line-height: 1;
        height: auto;
        min-width: unset;
    }

    .table th,
    .table td {
        padding-top: 0.25rem;
        padding-bottom: 0.25rem;
        /* optionally reduce font size too */
        font-size: 13px;
        /* reduce line height to compact rows */
        line-height: 1;
    }
    </style>
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">

        <!-- Navbar -->
        @if(auth()->guard('owner')->check())
        @include('partials.navbar-owners')
        @elseif(auth()->guard('web')->check())
        @include('partials.navbar')
        @endif


        <!-- Main Sidebar Container -->
        @if(auth()->guard('owner')->check())
        @include('partials.sidebar-owners')
        @elseif(auth()->guard('web')->check())
        @include('partials.sidebar')
        @endif

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">

            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h4 class="m-0">@yield('content_header_title', 'Saltern')</h4>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </section>

            <!-- Main content -->
            <section class="content">
                @yield('content_body')
                <!-- Main content -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
            <div class="p-3">
                <h5>Title</h5>
                <p>Sidebar content</p>
            </div>
        </aside>
        <!-- /.control-sidebar -->

        <!-- Footer -->
        @include('partials.footer')
    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="{{ asset('adminlte/plugins/jquery/jquery.min.js') }}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

    <!-- DataTables JavaScript with Bootstrap 4 integration -->
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.js" defer></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.js" defer></script>

    <script src="https://adminlte.io/themes/v3/plugins/chart.js/Chart.min.js" defer></script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    @yield('scripts')
    <!-- For additional scripts -->
    <script>
    /** add active class and stay opened when selected */
    var url = window.location;
    const allLinks = document.querySelectorAll('.nav-item a');
    const currentLink = [...allLinks].filter(e => {
        return e.href == url;
    });

    if (currentLink.length > 0) { //this filter because some links are not from menu
        currentLink[0].classList.add("active");
        currentLink[0].closest(".nav-treeview").style.display = "block";

        if (currentLink[0].closest(".has-treeview"))
            currentLink[0].closest(".has-treeview").classList.add("active");
    }
    </script>


    @stack('js')
</body>

</html>