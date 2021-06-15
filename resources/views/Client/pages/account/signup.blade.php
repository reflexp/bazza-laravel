@extends('Client.layouts.app')

@section('title', 'Регистрация')

@section('styles')
<style>
  .wrapper {
    display: flex;
    justify-content: center; 
  }

  .modal__confirm-btn {
    margin-left: 0 !important;
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
        <section class="signup-container">
          <div class="signup__header">
            <h1>Регистрация</h1>
          </div>
            
          <form id="signupForm" class="signup__form" method="POST">
            <input type="text" name="name" placeholder="Ваше имя">
            <input type="tel" name="login" placeholder="+7">
            <input type="email" name="email" placeholder="Электронная почта">
            <select name="buyerType">
              <option value="1">Розничный покупатель</option>
              <option value="2" selected>Оптовый покупатель</option>
            </select>
            <input type="password" name="password" placeholder="Введите пароль">
            <button class="btn" type="submit">Зарегистрироваться</button>
          </form>

          <div class="login__signup">
            <p>Если у вас уже есть аккаунт, вы можете <a href="{{ Route('Client.login') }}">авторизоваться</a> в системе.</p>
          </div>
        </section>

        <section class="modal-background-container">
          <section class="modal-container verification">
            <div class="modal__header">
              <h1 class="modal__title">Подтверждение кода</h1>
            </div>

            <div class="modal__content">
              <p>Пожалуйста, укажите какой смс код вам пришел на телефон.</p>
              
              <input type="hidden" name="hash">
              <input type="text" name="verificationCode" maxlength="4" placeholder="Например: 2468">
            </div>

            <div class="modal__buttons">
              <button class="btn modal__confirm-btn">Подтвердить</button>
            </div>
          </section>
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
    jQuery(document).on('submit', '#signupForm', function(e) {
      e.preventDefault();
      const formData = new FormData(this);

      const response = apiRequest('signup', formData);
      console.log(response);

      if (response.ok === true) {
        openModal('.modal-container.verification');
        jQuery('.modal-container.verification').find('input[name="hash"]').val(response.result.hash);
      }
    });

    jQuery(document).on('click', '.modal__confirm-btn', function(e) {
      const 
        formData = new FormData(),
        verificationCode = jQuery('.modal-container.verification').find('input[name="verificationCode"]').val(),
        hash = jQuery('.modal-container.verification').find('input[name="hash"]').val();

      formData.append('verificationCode', verificationCode);
      formData.append('hash', hash);

      const response = apiRequest('verify', formData);

      if (response.ok === true) {
        location.href = '/account';
      }
    });
  });
</script>
@endsection