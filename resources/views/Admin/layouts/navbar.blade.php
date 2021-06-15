<nav class="navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
{{--        <li class="nav-item d-none d-sm-inline-block">--}}
{{--            <a href="#" class="nav-link">Home</a>--}}
{{--        </li>--}}
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <!-- Messages Dropdown Menu -->
        <li class="nav-item">
            <a class="nav-link" href="{{ route('Client.index') }}" target="_blank" title="Перейти на сайт">
                <i class="fas fa-external-link"></i>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="{{ route('Admin.logout') }}" title="Выйти">
                <i class="fas fa-power-off"></i>
            </a>
        </li>
    </ul>
</nav>
