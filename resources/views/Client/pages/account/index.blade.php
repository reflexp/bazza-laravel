@extends('Client.layouts.app')

@section('title', 'Личный кабинет')

@section('styles')
  
@endsection

@section('content')
<main class="main-container">
  <div class="main__left-column">
    <!-- Navbar -->
    @include('Client.layouts.navbar')

    <!-- Main content -->
    <div class="wrapper">
      <section class="profile-container">
        <!-- Account menu -->
        @include('Client.layouts.account-menu')

        <div class="profile__settings">
          <div class="left-column">
            <div class="profile__header">
              <h1>Личные данные</h1>
            </div>

            <div class="profile__form">
              <form id="editForm" method="POST">
                <div class="profile__form-row">
                  <input type="text" name="name" placeholder="Ваше имя" value="{{ $clientInfo['name'] }}">
                  <input type="tel" disabled readonly placeholder="+7" value="{{ $clientInfo['login'] }}" autocomplete="off">
                  <input type="email" name="email" placeholder="Электронная почта" value="{{ $clientInfo['email'] }}">
                </div>

                <div class="profile__form-row">
{{--                  <input type="text" placeholder="Адрес доставки">--}}
{{--                  <input type="text" placeholder="VIN код кузова">--}}
                </div>

                <button class="btn" type="submit">Применить все изменения</button>
              </form>
            </div>
          </div>

          <div class="right-column">
            <div class="profile__header">
              <h1>Изменить пароля</h1>
            </div>

            <div class="profile__form">

              <form id="editPasswordForm" method="POST">
                <div class="profile__form-row">
                  <input type="password" name="oldPassword" placeholder="Старый пароль">
                  <input type="password" name="newPassword" placeholder="Новый пароль">
                  <input type="password" name="newPasswordRepeated" placeholder="Подтвердите новый пароль">
                </div>

                <button class="btn" type="submit">Изменить пароль</button>
              </form>
            </div>
          </div>
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
  jQuery(document).ready(() => {
    jQuery(document).on('submit', '#editForm', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const response = apiRequest('editAccount', formData);

        if (response.ok === true) {
        // this.reset();
        }
    });

    jQuery(document).on('submit', '#editPasswordForm', function(e) {
      e.preventDefault();
      const formData = new FormData(this);

      const response = apiRequest('editPassword', formData);

      if (response.ok === true) {
        this.reset();
      }
    });
  });
</script>
@endsection
