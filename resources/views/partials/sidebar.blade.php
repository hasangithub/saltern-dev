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
                    <a href="{{ route('dashboard') }}" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-chart-bar"></i>
                        <p>
                            Accounts
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('accounts.index') }}" class="nav-link">
                                <i class="nav-icon fas fa-water"></i>
                                <p>Chart of Accounts</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-chart-bar"></i>
                        <p>
                            Waikal Registartion
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <!-- Yahai -->
                        <li class="nav-item">
                            <a href="{{ route('yahai.index') }}" class="nav-link">
                                <i class="nav-icon fas fa-table"></i>
                                <p>Yahai</p>
                            </a>
                        </li>
                        <!-- Saltern -->
                        <li class="nav-item">
                            <a href="{{ route('saltern.index') }}" class="nav-link">
                                <i class="nav-icon fas fa-water"></i>
                                <p>Waikal No</p>
                            </a>
                        </li>
                        <!-- Owners -->
                        <li class="nav-item">
                            <a href="{{ route('owners.index') }}" class="nav-link">
                                <i class="nav-icon fas fa-user"></i>
                                <p>Owners</p>
                            </a>
                        </li>
                        <!-- Memberships -->
                        <li class="nav-item">
                            <a href="{{ route('memberships.index') }}" class="nav-link">
                                <i class="nav-icon fas fa-file-signature"></i>
                                <p>Memberships</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <!-- Buyers -->
                <li class="nav-item">
                    <a href="{{ route('buyers.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Vendors</p>
                    </a>
                </li>

                <!-- Weighbridge -->
                <li class="nav-item">
                    <a href="{{ route('weighbridge_entries.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-weight"></i>
                        <p>Weighbridge</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('vouchers.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-water"></i>
                        <p>Vouchers</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('journal-entries.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-water"></i>
                        <p>Journal</p>
                    </a>
                </li>
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-chart-bar"></i>
                        <p>
                            Loans
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('owner-loans.index') }}" class="nav-link">
                                <i class="nav-icon fas fa-water"></i>
                                <p>Owner Loan Management</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-chart-bar"></i>
                        <p>
                            Incomes
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('other_incomes.index') }}" class="nav-link">
                                <i class="nav-icon fas fa-water"></i>
                                <p>Other Income</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <!-- <li class="nav-item">
                    <a href="{{ route('expenses.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-weight"></i>
                        <p>Expense</p>
                    </a>
                </li> -->
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
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
    </div>
</aside>