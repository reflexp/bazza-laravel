@extends('Client.layouts.app')

@section('title', 'Сброс пароля')

@section('styles')
<style>
  .wrapper {
    display: flex;
    justify-content: center; 
  }

  .modal-container.editPassword .modal__confirm-btn {
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
      <section class="reset-container">
        <div class="reset__header">
          <h1>Сброс пароля</h1>
        </div>
        
        <form id="resetForm" method="POST">
          <input type="tel" name="login" placeholder="Введите номер телефона">
          <button class="btn" type="submit">Сбросить пароль</button>
        </form>
      </section>

      <section class="modal-background-container">
        <section class="modal-container verification">
          <div class="modal__close"><i class="fas fa-times"></i></div>

          <div class="modal__header">
            <h1 class="modal__title">Подтверждение кода</h1>
          </div>

          <div class="modal__content">
            <p>Пожалуйста, укажите какой смс код вам пришел на телефон.</p>
            
            <input type="hidden" name="hash">
            <input type="text" name="verificationCode" maxlength="4" placeholder="Например: 2468">
          </div>

          <div class="modal__buttons">
            <button class="btn modal__close-btn">Закрыть</button>
            <button class="btn modal__confirm-btn">Подтвердить</button>
          </div>
        </section>

        <section class="modal-container editPassword">
          <div class="modal__close"><i class="fas fa-times"></i></div>

          <div class="modal__header">
            <h1 class="modal__title">Изменение пароля</h1>
          </div>

          <div class="modal__content">
            <p>Для смены пароля необходимо ввести поля ниже</p>
            
            <input type="hidden" name="hash">
            <input type="password" name="newPassword" placeholder="Введите новый пароль">
            <input type="password" name="newPasswordRepeated" placeholder="Повторите новый пароль">
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
    jQuery(document).on('submit', '#resetForm', function(e) {
      e.preventDefault();
      const formData = new FormData(this);

      const response = apiRequest('reset', formData);

      if (response.ok === true) {
        openModal('.modal-container.verification');
        jQuery('.modal-container.verification').find('input[name="hash"]').val(response.result.hash);
      }
    });

    jQuery(document).on('click', '.modal-container.verification .modal__confirm-btn', function(e) {
      const 
        formData = new FormData(),
        verificationCode = jQuery('.modal-container.verification').find('input[name="verificationCode"]').val(),
        hash = jQuery('.modal-container.verification').find('input[name="hash"]').val();

      formData.append('verificationCode', verificationCode);
      formData.append('hash', hash);

      const response = apiRequest('verifyReset', formData);

      if (response.ok === true) {
        closeModal();
        openModal('.modal-container.editPassword');
        jQuery('.modal-container.editPassword').find('input[name="hash"]').val(response.result.hash);
      }
    });

    jQuery(document).on('click', '.modal-container.editPassword .modal__confirm-btn', function(e) {
      const 
        formData = new FormData(),
        hash = jQuery('.modal-container.editPassword').find('input[name="hash"]').val(),
        newPassword = jQuery('.modal-container.editPassword').find('input[name="newPassword"]').val(),
        newPasswordRepeated = jQuery('.modal-container.editPassword').find('input[name="newPasswordRepeated"]').val();

      formData.append('hash', hash);
      formData.append('newPassword', newPassword);
      formData.append('newPasswordRepeated', newPasswordRepeated);

      const response = apiRequest('editPasswordReset', formData);

      if (response.ok === true) {
        window.location.href = '/login';
      }
    });
  });
</script>
@endsection