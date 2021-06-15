<!DOCTYPE html>
<html>
    <head>
        {{-- Стили для всех страниц --}}
        @include('Admin.layouts.head')
        {{-- Дополнительные стили для страницы --}}
        @yield('styles')
    </head>
    <body class="hold-transition sidebar-mini layout-fixed">
        <div class="content-wrapper">
            {{-- Navbar --}}
            @include('Admin.layouts.navbar')
            {{-- Sidebar --}}
            @include('Admin.layouts.aside')
            {{-- Главный контент страницы --}}
            @yield('content')
        </div>
        {{-- Скрипты для всех страниц --}}
        @include('Admin.layouts.scripts')
        {{-- Дополнительные скрипты для страницы --}}
        @yield('scripts')
    </body>
</html>
