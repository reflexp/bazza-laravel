@extends('Client.layouts.app')

@section('title', 'Мои заказы')

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

        <div class="profile__orders">
          <div class="profile__header">
            <h1>Список ваших заказов</h1>
          </div>

          @if (count($orders) > 0)
            @foreach ($orders as $order)
              <details class="profile__order-container">
                <summary class="profile__order-title">Заказ: #{{ $order->id }}</summary>

                <div class="order__content">
                  <div class="order__list-item">
                    <span class="order__list-title">Статус:</span>
                    <span class="badge {{ $order['status']['color'] }}">{{ $order['status']['text'] }}</span>
                  </div>

                  <div class="order__list-item">
                    <span class="order__list-title">Дата заказа:</span>
                    <span class="order__list-value">{{ Carbon\Carbon::parse($order['created_at'])->isoFormat('DD-MM-YYYY H:m') }}</span>
                  </div>

                  <div class="order__list-item">
                    <span class="order__list-title">Сумма заказа:</span>
                    <span class="order__list-value">{{ $order['totalSum'] }} тенге</span>
                  </div>

                  <div class="order__list-item">
                    <span class="order__list-title">Нужна предоплата:</span>
                    <span class="order__list-value">
                      @if ($order['needPrePayment'] == 1)
                        Да
                      @else
                        Нет
                      @endif
                    </span>
                  </div>

                  <div class="order__list-item">
                    <span class="order__list-title">Полностью оплачено:</span>
                    <span class="order__list-value">
                      @if ($order->paymentConfirmed == 1)
                        Да
                      @else
                        Нет
                      @endif
                    </span>
                  </div>

                  <div class="order__list-item">
                    <span class="order__list-title">Выдача:</span>
                    <span class="order__list-value">
                      @if ($order->deliveryType == 1)
                        Самовывоз со склада
                      @else
                        Доставка
                      @endif
                    </span>
                  </div>

                  <div class="order__list-item">
                    <span class="order__list-title">Склад:</span>
                    <span class="order__list-value">{{ $order['storageTitle'] }}</span>
                  </div>

                  @if (!empty($order->deliveryAddressInCity))
                    <div class="order__list-item">
                      <span class="order__list-title">Адрес доставки:</span>
                      <span class="order__list-value">{{ $order['deliveryAddressInCity'] }}</span>
                    </div>
                  @endif

                  <div class="order__list-item order__comment">
                    <p><span class="order__list-title">Комментарий к заказу:</span> {{ $order['comment'] ?? '---' }}</p>
                  </div>

                  <details class="order__products-container">
                    <summary class="order__products-title">Список товаров</summary>

                    <div class="products__content">
                      @foreach ($order['products'] as $product)
                          <div class="order__product-container">
                            <div class="order__product-title">{{ $product['title'] }}</div>

                            <div class="order__product-row">
                              <div class="order__product-article">Артикул: {{ $product['article'] }}</div>
                              <div class="order__product-amount">Количество: {{ $product['amount'] }}</div>
                            </div>

                            <div class="order__product-price">Цена: {{ $product['totalPrice'] }} тенге</div>
                          </div>
                      @endforeach
                    </div>
                  </details>
                </div>
              </details>
            @endforeach
          @else
            <div class="orders__empty">
              <h1>Список ваших заказов пуст</h1>
              <p>
                  Вы можете перейти в каталог и выбрать необходимые вам товары.
              </p>
                <br>
                <br>
              <a class="btn" style="color: #000" href="{{ Route('Client.nomenclature') }}">Перейти в каталог</a>



            </div>
          @endif
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

@endsection




