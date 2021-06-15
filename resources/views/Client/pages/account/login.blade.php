@extends('Client.layouts.app')

@section('title', 'Авторизация')

@section('styles')
<style>
  .wrapper {
    display: flex;
    justify-content: center; 
  }
</style>
@endsection

@section('content')
<main class="main-container">
  <div class="main__left-column">
    <!-- Navbar -->
    @include('Client.layouts.navbar')

    <!-- Main content -->
    <div class="wrapper">
      <section class="login-container">
        <div class="login__header">
          <h1>Вход в систему</h1>
        </div>
        
        <form id="loginForm" class="login__form" method="POST">
          <input type="tel" name="login" placeholder="Введите номер телефона">
          <input type="password" name="password" placeholder="Введите пароль">
          <label class="login__checkbox" for="rememberMe">
            <input type="checkbox" name="rememberMe" id="rememberMe"> Запомнить меня
            <span class="checkmark"></span>
          </label>
          <button class="btn" type="submit">Войти</button>
        </form>

        <div class="login__signup">
          <p>Вы можете <a href="{{ Route('Client.reset') }}">сбросить пароль</a> если забыли его</p>
          <p>Если у вас до сих пор нет аккаунта, вы можете <a href="{{ Route('Client.signup') }}">зарегистрироваться</a> сейчас.</p>
        </div>
      </section>
    </div>

    <!-- Footer -->
    @include('Client.layouts.footer')
  </div>

  <div class="main__right-column">
      @include('Client.layouts.aside')
  </div>
</main>
@endsection

@section('scripts')
<script>
  $(document).ready(function() {
    jQuery(document).on('submit', '#loginForm', function(e) {
      e.preventDefault();
      const formData = new FormData(this);
      const addData = {'test': 'test'};

      Object.keys(addData).map((current, index) => formData.append(current, addData[current]));

      const response = apiRequest('login', formData);

      if (response.ok === true) {
        this.reset();
        window.location.href = '/account';
      }
    });
  });
</script>
@endsection