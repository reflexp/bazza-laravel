<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('Admin.home') }}" class="brand-link">
        <img src="{{ asset('/media/img/AdminLTELogo.png') }}" alt="BAZZA Logo" class="brand-image img-circle" style="opacity: .8">
        <span class="brand-text font-weight-light">BAZZA</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="info text-white">
                <span class="d-block">{{ $adminInfo['name'] ?? '--' }}</span>
                <span class="d-block">({{ $adminInfo['roleTitle'] ?? '---' }})</span>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
                     with font-awesome or any other icon font library -->

                <li class="nav-item">
                    <a href="{{ route('Admin.home') }}" class="nav-link @if (Request::routeIs('Admin.home')) active @endif">
                        <i class="nav-icon fad fa-home"></i>
                        <p>Главная</p>
                    </a>
                </li>

                @if($adminRights['clients'])
                    <li class="nav-item">
                        <a href="{{ route('Admin.clients') }}" class="nav-link
                        @if (Request::routeIs('Admin.clients'))
                            active
                        @endif">
                            <i class="nav-icon fad fa-users"></i>
                            <p>Клиенты</p>
                        </a>
                    </li>
                @endif
                @if($adminRights['users'])
                    <li class="nav-item">
                        <a href="{{ route('Admin.users') }}" class="nav-link
                        @if (Request::routeIs('Admin.users'))
                            active
                        @endif">
                            <i class="nav-icon fad fa-briefcase"></i>
                            <p>Сотрудники</p>
                        </a>
                    </li>
                @endif
                @if($adminRights['nomenclature'])
                    <li class="nav-item">
                        <a href="{{ route('Admin.nomenclature') }}" class="nav-link
                        @if (Request::routeIs('Admin.nomenclature'))
                            active
                        @endif">
                            <i class="nav-icon fad fa-file"></i>
                            <p>Номенклатура</p>
                        </a>
                    </li>
                @endif
                @if($adminRights['orders'])
                    <li class="nav-item">
                        <a href="{{ route('Admin.orders') }}" class="nav-link
                        @if (Request::routeIs('Admin.orders'))
                            active
                        @endif">
                            <i class="nav-icon fad fa-donate"></i>
                            <p>Заказы<span class="right badge badge-danger">Новое</span></p>
                        </a>
                    </li>
                @endif
                @if($adminRights['ordersbundles'])
                    <li class="nav-item">
                        <a href="{{ route('Admin.ordersbundles') }}" class="nav-link
                        @if (Request::routeIs('Admin.ordersbundles'))
                            active
                        @endif">
                            <i class="nav-icon fad fa-file-archive"></i>
                            <p>Пакеты заказов</p>
                        </a>
                    </li>
                @endif
                @if($adminRights['storages'])
                    <li class="nav-item">
                        <a href="{{ route('Admin.storages') }}" class="nav-link
                        @if (Request::routeIs('Admin.storages'))
                            active
                        @endif">
                            <i class="nav-icon fad fa-boxes"></i>
                            <p>Склады</p>
                        </a>
                    </li>
                @endif
                @if($adminRights['suppliers'])
                    <li class="nav-item">
                        <a href="{{ route('Admin.suppliers') }}" class="nav-link
                        @if (Request::routeIs('Admin.suppliers'))
                            active
                        @endif">
                            <i class="nav-icon fad fa-truck-loading"></i>
                            <p>Поставщики</p>
                        </a>
                    </li>
                @endif
                @if($adminRights['chat'])
                <li class="nav-item">
                    <a href="{{ route('Admin.chat') }}" class="nav-link
                    @if (Request::routeIs('Admin.chat'))
                        active
                    @endif">
                        <i class="fad fa-comments-alt"></i>
                        <p>Чат<span class="right badge badge-danger">Новое</span></p>
                    </a>
                </li>
                @endif
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
