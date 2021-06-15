{{--Meta--}}
<meta charset="utf-8">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="viewport" content="width=device-width, initial-scale=1">
{{--Styles--}}
<link rel="stylesheet" href="{{ asset('/plugins/fontawesome-pro/css/all.min.css') }}">
<link rel="stylesheet" href="{{ asset('/plugins/toastr/toastr.min.css') }}">
<link rel="stylesheet" href="{{ asset('/Admin/scss/adminlte.css') }}">
<!-- Кастомные стили -->
<link rel="stylesheet" href="{{ asset('/Admin/scss/app.css') }}">
{{--Title--}}
<title>@yield('title') - Bazza</title>
