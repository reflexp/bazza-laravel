@extends('Client.layouts.app')

@section('title', 'Корзина')

@section('styles')

@endsection

@section('content')
    <main class="main-container">
        <div class="main__left-column">
            <!-- Navbar -->
        @include('Client.layouts.navbar')

        <!-- Main content -->
            <div class="wrapper">
                <section class="cart-container">
                    <div class="cart__table @if (count($products) == 0) disabled @endif">
                        <table id="datatable" class="datatable-custom">
                            <thead>
                            <tr>
                                <th>Артикул</th>
                                <th>Наименование</th>
                                <th>Количество</th>
                                <th>Стоимость</th>
                                <th>Действие</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php
                                $totalPrice = 0;
                            @endphp

                            @foreach ($products as $product)
                                <tr data-product-id="{{ $product->productID }}">
                                    @php
                                        $totalPrice += $product->price * $product->amount;
                                    @endphp

                                    <td>{{ $product->article }}</td>
                                    <td>{{ $product->title }}</td>
                                    <td>
                                        <div class="cart__product-amount-container">
                                            <div class="product-amount-minus" data="-">
                                                <i class="fas fa-minus"></i>
                                            </div>

                                            <input class="product-amount" type="number" min="1"
                                                   value="{{ $product->amount }}">

                                            <div class="product-amount-plus" data="+">
                                                <i class="fas fa-plus"></i>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="product-price">{{ $product->price * $product->amount }}</td>
                                    <td>
                                        <button class="btn btn-remove-from-cart"><i class="fa fa-times fa-lg"
                                                                                    style="margin: 0"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr>
                                <th>Артикул</th>
                                <th>Наименование</th>
                                <th>Количество</th>
                                <th>Цена</th>
                                <th>Действие</th>
                            </tr>
                            </tfoot>
                        </table>

                    </div>

                    <div class="cart__totalSum @if (count($products) == 0) disabled @endif"">
                        <p>Сумма вашего заказа: {{ $totalPrice }} тенге</p>
                    </div>

                    <div class="cart__button @if (count($products) == 0) disabled @endif">
                        <a href="{{ Route('Client.order') }}" class="btn">Оформить заказ</a>
                    </div>

                    <div class="cart__empty @if (count($products) > 0) disabled @endif">
                        <h1>Ваша корзина пуста</h1>
                        <p>Вы можете перейти в <a href="{{ Route('Client.nomenclature') }}">каталог</a> и добавить
                            товары в корзину</p>
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
        window.addEventListener('load', () => {

            function recalculatePrice(element, operator = null) {
                const parent = jQuery(element).closest('tr');
                const productID = jQuery(parent).attr('data-product-id');
                let amount = parseInt(jQuery(parent).find('.product-amount').val());

                if (amount > 0) {
                    const formData = new FormData();

                    if (operator === '+' && amount > 0) {
                        amount++;
                    } else if (operator === '-' && amount > 1) {
                        amount--;
                    }

                    formData.append('productID', productID);
                    formData.append('amount', amount);

                    const response = apiRequest('editCartAmount', formData);

                    if (response) {
                        countCartItems();
                        jQuery(parent).find('.product-amount').val(response.amount);
                        jQuery(parent).find('.product-price').html(parseInt(response.amount) * parseInt(response.price));
                        jQuery(document).find('.cart__totalSum p').html(`Сумма вашего заказа: ${response.fullPrice} тенге`);
                    }
                }
            }

            jQuery(document).on('click', '.product-amount-minus', function (e) {
                const operator = this.getAttribute('data');
                recalculatePrice(this, operator);
            });

            jQuery(document).on('input', '.product-amount', function (e) {
                recalculatePrice(this);
            });

            jQuery(document).on('click', '.product-amount-plus', function (e) {
                const operator = this.getAttribute('data');
                recalculatePrice(this, operator);
            });

            // Удаление из корзины
            jQuery(document).on('click', '.btn-remove-from-cart', function (e) {
                const productID = jQuery(this).closest('tr').attr('data-product-id');
                const formData = new FormData();

                formData.append('productID', productID);

                const response = apiRequest('removeFromCart', formData);

                if (response.ok === true) {
                    countCartItems();
                    jQuery(this).closest('tr').fadeOut(200);
                    jQuery(document).find('.cart__totalSum p').html(`Сумма вашего заказа: ${response.result.fullprice} тенге`);
                    if (response.result.fullprice === 0) {
                        jQuery('.cart__table').addClass('disabled');
                        jQuery('.cart__totalSum').addClass('disabled');
                        jQuery('.cart__button').addClass('disabled');
                        jQuery('.cart__empty').removeClass('disabled');
                    }
                }
            });
        });
    </script>
@endsection




