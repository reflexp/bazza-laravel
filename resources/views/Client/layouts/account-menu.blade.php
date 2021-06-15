<menu class="profile__menu">
  <ul>
    <li><a class="@if (Request::routeIs('Client.account')) active @endif" href="{{ Route('Client.account') }}"><i class="fad fa-sliders-v-square"></i>Настройка профиля</a></li>
    <li><a class="@if (Request::routeIs('Client.account.orders')) active @endif" href="{{ Route('Client.account.orders') }}"><i class="fad fa-clipboard-list-check"></i>Список заказов</a></li>
    <li><a class="@if (Request::routeIs('Client.account.chat')) active @endif" href="{{ Route('Client.account.chat') }}"><i class="fad fa-comments-alt"></i>Диалог с экспертом</a></li>
  </ul>
</menu> 