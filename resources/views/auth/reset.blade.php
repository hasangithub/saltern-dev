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
</head>

<body class="hold-transition login-page">
    <div class="login-box">
        <div class="login-logo">
            <a href="../../index2.html"><b>Admin</b>Login</a>
        </div>
        <!-- /.login-logo -->
        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg"></p>
                @if($errors->any())
                <div style="color: red">{{ $errors->first() }}</div>
                @endif
                @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
                @endif
                <h2>Reset Password ({{ ucfirst($type) }})</h2>

                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="type" value="{{ $type }}">
                    <input type="email" name="email" value="{{ old('email', $email) }}" required>
                    <input type="password" name="password" placeholder="New Password" required>
                    <input type="password" name="password_confirmation" placeholder="Confirm Password" required>
                    <button type="submit">Reset Password</button>
                </form>
            </div>
            <!-- /.login-card-body -->
        </div>
    </div>
    <!-- /.login-box -->

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