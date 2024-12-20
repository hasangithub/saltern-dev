<!-- resources/views/partials/sidebar.blade.php -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="#" class="brand-link">
        <span class="brand-text font-weight-light">AdminLTE</span>
    </a>
    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <!-- Buyers -->
                <li class="nav-item">
                    <a href="{{ route('buyers.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Buyers</p>
                    </a>
                </li>
                <!-- Owners -->
                <li class="nav-item">
                    <a href="{{ route('owners.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-user"></i>
                        <p>Owners</p>
                    </a>
                </li>
                <!-- Weighbridge -->
                <li class="nav-item">
                    <a href="{{ route('weighbridge_entries.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-weight"></i>
                        <p>Weighbridge</p>
                    </a>
                </li>
                <!-- Memberships -->
                <li class="nav-item">
                    <a href="{{ route('memberships.create') }}" class="nav-link">
                        <i class="nav-icon fas fa-file-signature"></i>
                        <p>Memberships</p>
                    </a>
                </li>
                <!-- Yahai -->
                <li class="nav-item">
                    <a href="{{ route('yahai.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-water"></i>
                        <p>Yahai</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>
