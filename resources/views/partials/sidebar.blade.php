<!-- resources/views/partials/sidebar.blade.php -->
<aside class="main-sidebar sidebar-dark-maroon elevation-4">
    <a href="#" class="brand-link">
        <span class="brand-text font-weight-light">Saltern</span>
    </a>
    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column nav-compact nav-child-indent" data-widget="treeview"
                role="menu" data-accordion="false">
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
                            Registartion
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('accounts.index') }}" class="nav-link">
                                <i class="nav-icon fas fa-file-invoice"></i>
                                <p>Chart of Accounts</p>
                            </a>
                        </li>
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
                                <i class="nav-icon fas fa-id-badge"></i>
                                <p>Memberships</p>
                            </a>
                        </li>

                        <!-- Buyers -->
                        <li class="nav-item">
                            <a href="{{ route('buyers.index') }}" class="nav-link">
                                <i class="nav-icon fas fa-users"></i>
                                <p>Vendors</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('employees.index') }}" class="nav-link">
                                <i class="nav-icon fas fa-users-cog"></i>
                                <p>Employees</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Weighbridge -->
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-chart-bar"></i>
                        <p>
                            Weighbridge
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('weighbridge_entries.index') }}" class="nav-link">
                                <i class="nav-icon fas fa-weight"></i>
                                <p>Weighbridge</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('weighbridge.initial.create') }}" class="nav-link">
                                <i class="nav-icon fas fa-weight"></i>
                                <p>First Weight</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-chart-bar"></i>
                        <p>
                            Private Weighbridge
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('private-weighbridge-entries.index') }}" class="nav-link">
                                <i class="nav-icon fas fa-weight"></i>
                                <p>Private Weighbridge</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('private-weighbridge-entries.create') }}" class="nav-link">
                                <i class="nav-icon fas fa-weight"></i>
                                <p>First Weight</p>
                            </a>
                        </li>
                    </ul>
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
                                <i class="nav-icon fas fa-hand-holding-usd text-danger"></i>
                                <p>Owner Loan Management @if(!empty($pendingOwnerLoanCount))
                                    <span class="badge badge-danger right">&nbsp;{{ $pendingOwnerLoanCount }}</span>
                                    @endif
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.owner_loans.create') }}" class="nav-link">
                                <i class="nav-icon fas fa-hand-holding-usd text-danger"></i>
                                <p>Create Owner Loan</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('owner-loan-repayments.index') }}" class="nav-link">
                                <i class="nav-icon fas fa-hand-holding-usd text-danger"></i>
                                <p>Owner Loan Repayments</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.staff-loans.index') }}" class="nav-link">
                                <i class="nav-icon fas fa-hand-holding-usd text-danger"></i>
                                <p>Staff Loan Management</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.staff_loans.create') }}" class="nav-link">
                                <i class="nav-icon fas fa-hand-holding-usd text-danger"></i>
                                <p>Create Staff Loan</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('staff-loan-repayments.index') }}" class="nav-link">
                                <i class="nav-icon fas fa-hand-holding-usd text-danger"></i>
                                <p>Staff Loan Repayments</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="{{ route('vouchers.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-receipt"></i>
                        <p>Vouchers</p>
                    </a>
                </li>

                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-coins"></i>
                        <p>
                            Incomes
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('other_incomes.index') }}" class="nav-link">
                                <i class="nav-icon fas fa-water text-warning"></i>
                                <p>Other Income</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="{{ route('receipts.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-file-invoice-dollar"></i>
                        <p>Receipts</p>
                    </a>
                </li>

                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-money-check-alt"></i>
                        <p>
                            Payroll
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('attendance.index') }}" class="nav-link">
                                <i class="nav-icon fas fa-water"></i>
                                <p>Attendances</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('payroll.batches.index') }}" class="nav-link">
                                <i class="nav-icon fas fa-water"></i>
                                <p>Payroll</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="{{ route('journal-entries.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-book"></i>
                        <p>Journal</p>
                    </a>
                </li>

                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-chart-bar"></i>
                        <p>
                            Reports
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('production.report.index') }}" class="nav-link">
                                <i class="nav-icon fas fa-industry text-info"></i>
                                <p>Production Report</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('ledger.report.index') }}" class="nav-link">
                                <i class="nav-icon fas fa-file-invoice text-info"></i>
                                <p>Ledger Report</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('trial.report.index') }}" class="nav-link">
                                <i class="nav-icon fas fa-calculator text-info"></i>
                                <p>Trial Balance</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('balance.sheet') }}" class="nav-link">
                                <i class="nav-icon fas fa-calculator text-info"></i>
                                <p>Balance Sheet</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('reports.owner.loan.index') }}" class="nav-link">
                                <i class="nav-icon fas fa-dollar-sign text-info"></i>
                                <p>Owner Loan</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('reports.staff.loan.index') }}" class="nav-link">
                                <i class="nav-icon fas fa-dollar-sign text-info"></i>
                                <p>Staff Loan</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('reports.pending.payments.index') }}" class="nav-link">
                                <i class="nav-icon fas fa-money-bill-wave text-info"></i>
                                <p>Pending Payments</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('reports.receipts.index') }}" class="nav-link">
                                <i class="nav-icon fas fa-file-invoice-dollar text-info"></i>
                                <p>Receipts</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('reports.voucher.index') }}" class="nav-link">
                                <i class="nav-icon fas fa-file-contract text-info"></i>
                                <p>Voucher</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('reports.inventory.index') }}" class="nav-link">
                                <i class="nav-icon fas fa-file-contract text-info"></i>
                                <p>Inventory</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('reports.refund.index') }}" class="nav-link">
                                <i class="nav-icon fas fa-file-contract text-info"></i>
                                <p>ServiceCharge Refund</p>
                            </a>
                        </li>

                    </ul>
                </li>
                <li class="nav-item">
                    <a href="{{ route('staff.complaints.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-exclamation-circle"></i>
                        <p>Complaints
                            @if(!empty($pendingComplaintsCount))
                            <span class="badge badge-danger right">{{ $pendingComplaintsCount }}</span>
                            @endif
                        </p>
                    </a>
                </li>


                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-money-check-alt"></i>
                        <p>
                            Inventories
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('inventories.index') }}" class="nav-link">
                                <i class="nav-icon fas fa-boxes"></i>
                                <p>Inventories</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('places.index') }}" class="nav-link">
                                <i class="nav-icon fas fa-boxes"></i>
                                <p>Places</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('stock.index') }}" class="nav-link">
                                <i class="nav-icon fas fa-book"></i>
                                <p>Stocks</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-money-check-alt"></i>
                        <p>
                            Refunds
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('refund-batches.index') }}" class="nav-link">
                                <i class="nav-icon fas fa-weight"></i>
                                <p>New Refunds</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('refunds.index') }}" class="nav-link">
                                <i class="nav-icon fas fa-weight"></i>
                                <p>Refunds</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('vouchers.refund.create') }}" class="nav-link">
                                <i class="nav-icon fas fa-weight"></i>
                                <p>Refund Voucher</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
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