@extends('Admin.layouts._clear.app')

@section('title', 'Авторизация')

@section('styles')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">

    <style>
        .form-signin {
            width: 100%;
            max-width: 330px;
            padding: 15px;
            margin: 1rem auto;
        }
        a {
            color: #262626;
            text-decoration: none;
        }
        .navbar__logo span {
            color: #fcd707;
        }
        .navbar__logo {
            font-size: 42px;
            font-weight: 500;
            margin: 1rem;
            text-align: center;
        }
    </style>
@endsection

@section('content')

    <form class="form-signin" id="loginForm">
        <div class="navbar__logo">
            <a href="{{ route('Client.index') }}"><span>BAZZA</span>.KZ</a>
        </div>
        <h1 class="h3 mb-3 font-weight-normal">Вход</h1>
        <input type="tel" name="login" class="form-control" placeholder="Логин"  autofocus="" autocomplete="login">
        <input type="password" name="password" class="form-control" placeholder="Пароль"  autocomplete="password">
        <div class="checkbox mb-3">
            <label>
                <input type="checkbox" name="rememberMe" value="remember-me"> Запомнить меня на месяц
            </label>
        </div>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Войти</button>
        <p class="mt-5 mb-3 text-muted">© Bazza.kz {{ date('Y') }}</p>
    </form>
@endsection

@section('scripts')
    <script>
        window.addEventListener('load', () => {
            let loginForm = document.querySelector('#loginForm');

            loginForm.addEventListener('submit', (e) => {
                e.preventDefault();
                // let object = {'test': 123};
                let formData = new FormData(loginForm);

                // Object.keys(object).forEach(key => formData.append(key, object[key]));
                let response = apiRequest('login', formData);
                if(response.ok === true)
                {
                    window.location.href = '/control';
                }
            });
        });
    </script>
@endsection
