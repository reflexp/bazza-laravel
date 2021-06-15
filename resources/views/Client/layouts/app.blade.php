<!DOCTYPE html>
<html>
    <head>
        {{-- Стили для всех страниц --}}
        @include('Client.layouts.head')
        {{-- Дополнительные стили для страницы --}}
        @yield('styles')
    </head>
    <body>
        {{-- Главный контент страницы --}}
        @yield('content')
        {{-- Скрипты для всех страниц --}}
        @include('Client.layouts.scripts')
        {{-- Дополнительные скрипты для страницы --}}
        @yield('scripts')
    </body>
</html>
