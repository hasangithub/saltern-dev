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
         <!-- User Dropdown Menu in Navbar -->
         <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="fas fa-user-circle"></i>
                @if(auth('web')->check())
                {{ auth('web')->user()->name }}
                @elseif(auth('owner')->check())
                {{ auth('owner')->user()->full_name }}
                @elseif(auth('buyer')->check())
                {{ auth('buyer')->user()->name }}
                @else
                Guest
                @endif
                <i class="fas fa-caret-down ml-1"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <a href="{{route('staff-loans.index')}}" class="dropdown-item">
                    <i class="fas fa-file-invoice-dollar mr-2"></i> My Loans
                </a>
                <div class="dropdown-divider"></div>
            </div>
        </li>
    </ul>
</nav>
<!-- /.navbar -->