<nav class="navbar-container">
    <div class="wrapper">
      <div class="navbar__left-column">
        <div class="navbar__logo">
          <a href="{{ route('Client.index') }}"><span>BAZZA</span>.KZ</a>
        </div>
  
        <div class="navbar__links">
          <ul>
            <li><a class="@if (Request::routeIs('Client.nomenclature') || Request::routeIs('Client.nomenclatureSearch')) active @endif" href="{{ route('Client.nomenclature') }}">Каталог</a></li>
            <li><a href="#">Как заказать</a></li>
            <li><a href="#">Информация</a></li>
            <li><a href="#">Склады и магазины</a></li>
          </ul>
        </div>
      </div>
  
      <div class="navbar__login">
        <ul>
          <li>
            <a href="tel:+77773342200" class="break"><i class="fad fa-phone-alt"></i>+7 (777) 334-22-00</a>
          </li>
          <li>
            <a href="{{ route('Client.shopcart') }}" class="break shopcart-btn"><i class="fad fa-shopping-cart"></i>Корзина</a>
          </li>
          <li>
            @if (Cookie::get('_client') !== null)
              @if (Request::routeIs('Client.account') || Request::routeIs('Client.account.orders') || Request::routeIs('Client.account.chat'))
                <a href="{{ route('Client.logout') }}" class="btn"><i class="fad fa-sign-out"></i>Выйти</a>
              @else
                <a href="{{ route('Client.account') }}" class="btn"><i class="fad fa-user"></i>Аккаунт</a>
              @endif
            @else
              <a href="{{ route('Client.login') }}" class="btn"><i class="fad fa-sign-in"></i>Войти / Регистрация</a>
            @endif
          </li>
        </ul>
      </div>
  
      <div class="navbar__menu">
        <i class="fas fa-bars"></i>
      </div>
    </div>
  </nav>