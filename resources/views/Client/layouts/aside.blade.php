<aside class="sidebar-container">
  <div class="sidebar__logo">
    <a href="{{ route('Client.index') }}">BAZZA.KZ</a>
  </div>

  <div class="sidebar__links">
    <ul>
      @if (Cookie::get('_client') !== null)
        <li><a class="@if (Request::routeIs('Client.account')) active @endif" href="{{ route('Client.account') }}"><i class="fad fa-user"></i>Аккаунт</a></li>
      @endif
      <li><a href="{{ route('Client.nomenclature') }}"><i class="fad fa-list"></i>Каталог</a></li>
      <li><a href="#"><i class="fad fa-cart-plus"></i>Как заказать</a></li>
      <li><a href="#"><i class="fad fa-info-square"></i>Информация</a></li>
      <li><a href="#"><i class="fad fa-boxes"></i>Склады и магазины</a></li>
      <li>
        @if (Cookie::get('_client') !== null) 
          <a href="{{ route('Client.logout') }}" class="btn"><i class="fad fa-sign-out"></i>Выйти</a>
        @else
          <a href="{{ route('Client.login') }}" class="btn"><i class="fad fa-user"></i>Войти</a>
        @endif
      </li>
    </ul>
  </div>

  <div class="sidebar__phone">
    <a href="tel:+77773342200">
      <div class="sidebar__phone-icon">
        <i class="fad fa-phone-alt"></i>
      </div>

      +7 (777) 334-22-00
    </a>
  </div>
</aside>