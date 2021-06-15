@extends('Client.layouts.app')

@section('title', 'Оформление заказа')

@section('styles')

@endsection

@section('content')
    <main class="main-container">
        <div class="main__left-column">
            <!-- Navbar -->
        @include('Client.layouts.navbar')

        <!-- Main content -->
            <div class="wrapper">
                <section class="neworder-container">
                    <div class="neworder__header">
                        <h1>Создание нового заказа</h1>
                        {{--          <small>--}}
                        {{--            <p>Для заказа товаров требуется регистрация.</p>--}}
                        {{--            <p>Если вы уже зарегистрированы, войдите в свою учетную запись</p>--}}
                        {{--          </small>--}}
                    </div>

                    <div>
                        <table id="datatable" class="datatable-custom" style="overflow-x: auto">
                            <thead style="text-align: left">
                                <tr>
                                    <th>Артикул</th>
                                    <th>Наименование</th>
                                    <th>Кол-во</th>
                                    <th>Стоимость</th>
                                </tr>
                            </thead>
                            <tbody>

                            @php
                                $totalPrice = 0;
                            @endphp

                            @foreach ($products as $product)
                                <tr data-cart-id="{{ $product->cartID }}" data-product-id="{{ $product->productID }}">
                                    @php
                                        $totalPrice += $product->price * $product->amount;
                                    @endphp

                                    <td>{{ $product->article }}</td>
                                    <td>{{ $product->title }}</td>
                                    <td>
                                        {{ $product->amount }} шт
                                    </td>
                                    <td class="product-price">{{ $product->price * $product->amount }} KZT</td>
                                </tr>
                            @endforeach
                            </tbody>

                        </table>

                    </div>
                    <p style="font-size: 18px; font-weight:700; margin: 30px 10px;">Сумма заказа: {{ $totalPrice }} KZT </p>

                    <form id="orderForm" class="neworder__form" method="POST">
                        <div class="left-column">
                            <div class="top-row">
                                <div class="neworder__form-header">
                                    <h1>Контактные данные</h1>
                                </div>

                                <div class="neworder__form-row">
                                    <input type="text" placeholder="Ваше имя" value="{{ $clientInfo->name }}" disabled>
                                    <input type="tel" placeholder="+7" value="{{ $clientInfo->login }}" disabled>
                                    <input type="email" placeholder="+7" value="{{ $clientInfo->email }}" disabled>

                                </div>
                            </div>



                        </div>

                        <div class="right-column">
                            <div class="neworder__form-header">
                                <h1>Адрес доставки</h1>
                            </div>

                            <div class="neworder__form-row">
                                <select name="storage">
                                    @foreach ($storages as $storage)
                                        <option value="{{ $storage->id }}">{{ $storage->title }}</option>
                                    @endforeach
                                </select>
                                <select name="deliveryType">
                                    <option value="1" selected>Самовывоз со склада</option>
                                    <option value="2">Доставка курьером по городу</option>
                                </select>
                                <input type="text" name="street" placeholder="Улица" disabled>
                                <input type="text" name="build" placeholder="Дом / БЦ" disabled>
                                <input type="text" name="flat" placeholder="Квартира / Офис" disabled>

                                <textarea type="text" name="comment" placeholder="Введите комментарий" rows="8"
                                          maxlength="1024" style="resize: none"></textarea>
                                <small>Количество символов: 1024</small>

                            </div>
                            <div class="bottom-row" style="margin-top: 20px">
                                <button class="btn" type="submit">Оформить заказ</button>

                                <label class="neworder__checkbox" for="checkbox">
                                    <input type="checkbox" name="shopRules" id="checkbox"> Согласен с условиями,
                                    правилами пользования торговой площадкой и правилами возврата
                                    <span class="checkmark"></span>
                                </label>
                            </div>
                        </div>

                    </form>
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
  window.addEventListener('load', () => {
      jQuery('select[name="deliveryType"]').on('change', function (e) {
          let disabled = true;
          if (parseInt(this.value) === 2) {
              disabled = false;
          }
          jQuery('[name="street"]').val('').attr('disabled', disabled)
          jQuery('[name="build"]').val('').attr('disabled', disabled)
          jQuery('[name="flat"]').val('').attr('disabled', disabled)
      });

      jQuery('textarea[name="comment"]').on('input', function () {
          const length = 1024;
          (this.value.length <= length) ? jQuery(this).next().html(`Количество символов: ${length - this.value.length}`) : jQuery(this).val(this.value.substring(0, this.value.length - 1));
      });

      jQuery('#orderForm').on('submit', function (e) {
          e.preventDefault();
          const formData = new FormData(this);

          const response = apiRequest('addOrder', formData);

          if (response.ok === true) {
              window.location.href = '/account/orders';
          }
      });
  });
</script>
@endsection




