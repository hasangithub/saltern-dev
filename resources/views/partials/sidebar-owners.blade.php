<!-- resources/views/partials/sidebar.blade.php -->
<aside class="main-sidebar sidebar-dark-maroon elevation-4">
    <a href="#" class="brand-link">
        <span class="brand-text font-weight-light">Saltern</span>
    </a>
    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="{{ route('owner.dashboard') }}" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-chart-bar"></i>
                        <p>
                            Owner Profile
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('owner-loans.create') }}" class="nav-link">
                                <i class="nav-icon fas fa-water"></i>
                                <p>Request Loan</p>
                            </a>
                            <a href="{{ route('owner.my-loans.index') }}" class="nav-link">
                                <i class="nav-icon fas fa-water"></i>
                                <p>My Loans</p>
                            </a>
                            <a href="{{ route('productions.index') }}" class="nav-link">
                                <i class="nav-icon fas fa-water"></i>
                                <p>Productions</p>
                            </a>
                            <a href="{{ route('owner.complaints.index') }}" class="nav-link">
                                <i class="nav-icon fas fa-water"></i>
                                <p>Complaints</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <form action="{{ route('owner.logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="nav-link bg-danger text-white">
                            <i class="nav-icon fas fa-sign-out-alt"></i>
                            <p>Logout</p>
                        </button>
                    </form>
                </li>
            </ul>
        </nav>
    </div>
</aside>