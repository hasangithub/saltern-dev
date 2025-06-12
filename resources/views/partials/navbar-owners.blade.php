<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <!-- User Info -->
        <li class="nav-item">
            <a class="nav-link" href="#">
                @if(auth('web')->check())
                {{ auth('web')->user()->name }}
                @elseif(auth('owner')->check())
                {{ auth('owner')->user()->full_name }}
                @elseif(auth('buyer')->check())
                {{ auth('buyer')->user()->name }}
                @else
                Guest
                @endif
            </a>
        </li>
    </ul>
</nav>
<!-- /.navbar -->