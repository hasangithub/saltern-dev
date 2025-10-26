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

    /* Smaller font and padding for main menu links */
    .sidebar .nav-sidebar>.nav-item>.nav-link {
        font-size: 0.85rem !important;
        /* smaller font */
        padding: 0.35rem 0.75rem !important;
        /* less vertical and horizontal padding */
        height: auto !important;
        /* auto height */
        line-height: 1.1 !important;
        /* tighter line height */
    }

    /* Smaller font and padding for submenu links */
    .sidebar .nav-treeview>.nav-item>.nav-link {
        font-size: 0.8rem !important;
        padding-left: 1rem !important;
        padding-top: 0.25rem !important;
        padding-bottom: 0.25rem !important;
        height: auto !important;
        line-height: 1.1 !important;
    }

    /* Optional: smaller icons in menu and submenu */
    .sidebar .nav-icon {
        font-size: 0.9rem !important;
    }

    /* Optional: reduce spacing between menu items */
    .sidebar .nav-sidebar>.nav-item,
    .sidebar .nav-treeview>.nav-item {
        margin-bottom: 0.15rem !important;
    }

    .table-hover tbody tr:hover {
        background-color: #0056b3 !important;
        color: white !important;
    }
    </style>
</head>

<body class="hold-transition sidebar-mini sidebar-collapse">
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
            <div class="card shadow-sm mb-4">
                <div class="card-body py-2">
                    <div class="d-flex justify-content-between">
                        <h4 class="card-title mb-0">@yield('content_header_title', 'Saltern')</h4>
                        <div class="d-flex gap-2">
                            @yield('page-buttons')
                        </div>
                    </div>
                </div>
            </div>

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
    document.addEventListener("DOMContentLoaded", function() {
        const url = window.location.href;
        const allLinks = document.querySelectorAll('.nav-item a');

        // Find the link that matches current URL or starts with current URL
        const currentLink = [...allLinks].find(link => {
            const linkHref = link.href.replace(/\/$/, '');
            const currentUrl = url.replace(/\/$/, '');

            return currentUrl === linkHref || currentUrl.startsWith(linkHref + '/');
        });

        if (currentLink) {
            // Add active class to the submenu link
            currentLink.classList.add("active");

            // Find the closest nav-treeview (submenu container)
            const navTreeview = currentLink.closest(".nav-treeview");
            if (navTreeview) {
                // Find the parent .has-treeview <li>
                const parentLi = navTreeview.closest(".has-treeview");
                if (parentLi) {
                    // Add menu-open to parent <li> to expand submenu
                    parentLi.classList.add("menu-open");

                    // Add active to the main menu <a> inside parent <li>
                    const parentLink = parentLi.querySelector('a.nav-link');
                    if (parentLink) {
                        parentLink.classList.add("active");
                    }
                }
            }
        }
    });
    </script>


    @stack('js')
</body>

</html>