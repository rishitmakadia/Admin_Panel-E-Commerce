<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="{{ route('admin.dashboard') }}" class="brand-link">
        <span class="brand-text font-weight-light d-block text-center">Admin Panel</span>
    </a>
    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->is('admin/dashboard*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ url('/admin/users') }}" class="nav-link {{ request()->is('admin/users*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Users</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ url('/admin/address')}}" class="nav-link">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Users Address</p>
                    </a>
                </li>

                <li class="nav-item {{ request()->is('admin/electronics*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->is('admin/electronics*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tv"></i>
                        <p>
                            Electronics
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ url('/admin/electronics') }}" class="nav-link {{ request()->is('admin/electronics') && !request()->is('admin/electronics/*') ? 'active' : '' }}">
                                <i class="fas fa-list-ul nav-icon"></i>
                                <p>All Electronics</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('/admin/electronics/categoryForm') }}" class="nav-link {{ request()->is('admin/electronics/categoryForm*') ? 'active' : '' }}">
                                <i class="fas fa-sitemap nav-icon"></i>
                                <p>Categories</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('/admin/electronics/subCategoryForm') }}" class="nav-link {{ request()->is('admin/electronics/subCategoryForm*') ? 'active' : '' }}">
                                <i class="fas fa-code-branch nav-icon"></i>
                                <p>Sub-Categories</p>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
    </div>
</aside>
