<!DOCTYPE html>
<html>
    <head>
        {{-- Стили для всех страниц --}}
        @include('Admin.layouts._clear.head')
        {{-- Дополнительные стили для страницы --}}
        @yield('styles')
    </head>
    <body class="hold-transition sidebar-mini layout-fixed">
            {{-- Главный контент страницы --}}
            @yield('content')
        {{-- Скрипты для всех страниц --}}
        @include('Admin.layouts._clear.scripts')
        {{-- Дополнительные скрипты для страницы --}}
        @yield('scripts')
    </body>
</html>
