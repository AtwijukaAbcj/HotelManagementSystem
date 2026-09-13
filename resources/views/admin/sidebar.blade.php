<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <li class="{{ request()->is('redirect', 'dashboard') ? 'active' : '' }}"><a href="{{ url('redirect') }}"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a></li>
                <li class="list-divider"></li>

                <li class="sidebar-section-title">Front office</li>

                <li class="submenu">
                    <a href="#"><i class="fas fa-suitcase"></i> <span>Bookings</span> <span class="menu-arrow"></span></a>
                    <ul class="submenu_class" style="display: none;">
                        <li><a href="{{ url('form/allbooking') }}">All bookings</a></li>
                        <li><a href="{{ url('form/addbooking') }}">Add booking</a></li>
                        <li><a href="{{ url('/calander') }}">Reservation calendar</a></li>
                    </ul>
                </li>

                <li class="submenu">
                    <a href="#"><i class="fas fa-bed"></i> <span>Rooms</span> <span class="menu-arrow"></span></a>
                    <ul class="submenu_class" style="display: none;">
                        <li><a href="{{ url('/room-status-board') }}">Status board</a></li>
                        <li><a href="{{ url('/all_rooms') }}">All rooms</a></li>
                        <li><a href="{{ url('/add_rooms') }}">Add rooms</a></li>
                        <li><a href="{{ route('room-types') }}">Room types & pricing</a></li>
                    </ul>
                </li>

                <li class="{{ request()->routeIs('guests.*') ? 'active' : '' }}"><a href="{{ route('guests.index') }}"><i class="fas fa-user-friends"></i> <span>Guests</span></a></li>
                <li class="sidebar-section-title">Hotel operations</li>
                <li class="submenu">
                    <a href="#"><i class="fas fa-building"></i> <span>Property Management</span> <span class="menu-arrow"></span></a>
                    <ul class="submenu_class" style="display: none;">
                        <li><a href="{{ route('properties.index') }}">Properties</a></li>
                        <li><a href="{{ route('properties.dashboard') }}">Property dashboard</a></li>
                    </ul>
                </li>
                <li><a href="{{ route('notifications.index') }}"><i class="fas fa-bell"></i> <span>Notifications</span></a></li>
                @if(auth()->check() && in_array(strtolower((string) auth()->user()->role), ['admin', 'manager', 'security_manager', 'reception', 'hr', 'auditor'], true))
                <li class="{{ request()->routeIs('access-control.*') ? 'active' : '' }}"><a href="{{ route('access-control.index') }}"><i class="fas fa-id-card"></i> <span>Control room</span></a></li>
                @endif
                <li><a href="{{ route('pos.index') }}"><i class="fas fa-cash-register"></i> <span>Restaurant / Bar POS</span></a></li>
                <li class="{{ request()->routeIs('inventory.*') ? 'active' : '' }}"><a href="{{ route('inventory.index') }}"><i class="fas fa-boxes"></i> <span>Inventory</span></a></li>
                <li class="{{ request()->routeIs('suppliers.*') ? 'active' : '' }}"><a href="{{ route('suppliers.index') }}"><i class="fas fa-address-book"></i> <span>Suppliers</span></a></li>
                <li class="{{ request()->routeIs('stock-movements.*') ? 'active' : '' }}"><a href="{{ route('stock-movements.index') }}"><i class="fas fa-exchange-alt"></i> <span>Stock movements</span></a></li>
                <li><a href="{{ route('purchases.index') }}"><i class="fas fa-truck-loading"></i> <span>Purchases</span></a></li>
                <li><a href="{{ route('housekeeping.index') }}"><i class="fas fa-broom"></i> <span>Housekeeping</span></a></li>
                <li><a href="{{ route('maintenance.index') }}"><i class="fas fa-tools"></i> <span>Maintenance</span></a></li>
                <li><a href="{{ url('/check') }}"><i class="fas fa-clipboard-list"></i> <span>Attendance</span></a></li>

                <li class="submenu {{ request()->is('employee/*', 'form/addemployee') ? 'active' : '' }}">
                    <a href="#"><i class="fas fa-user"></i> <span>Employees</span> <span class="menu-arrow"></span></a>
                    <ul class="submenu_class" style="display: none;">
                        <li><a href="{{ url('employee/list') }}">Employees list</a></li>
                        <li><a href="{{ url('form/addemployee') }}">Add employee</a></li>
                    </ul>
                </li>

                <li class="sidebar-section-title">Finance & reporting</li>
                <li class="{{ request()->routeIs('finance.*') ? 'active' : '' }}"><a href="{{ route('finance.index') }}"><i class="fas fa-chart-line"></i> <span>Finance overview</span></a></li>
                <li class="{{ request()->routeIs('payments.*') ? 'active' : '' }}"><a href="{{ route('payments.index') }}"><i class="fas fa-money-bill-wave"></i> <span>Payments</span></a></li>
                <li class="{{ request()->routeIs('invoices.*') ? 'active' : '' }}"><a href="{{ route('invoices.index') }}"><i class="fas fa-file-invoice-dollar"></i> <span>Invoices</span></a></li>
                <li class="{{ request()->routeIs('expenses.*') ? 'active' : '' }}"><a href="{{ route('expenses.index') }}"><i class="fas fa-wallet"></i> <span>Expenses</span></a></li>
                <li><a href="{{ url('/billing') }}"><i class="fas fa-receipt"></i> <span>Billing</span></a></li>
                <li><a href="{{ url('/billing_report') }}"><i class="fas fa-file-contract"></i> <span>Invoice reports</span></a></li>
                <li><a href="{{ url('/sheet-report') }}"><i class="fas fa-file-excel"></i> <span>Sheet report</span></a></li>
            </ul>
        </div>
    </div>
</div>