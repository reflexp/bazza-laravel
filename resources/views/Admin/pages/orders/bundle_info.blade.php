@extends('Admin.layouts.app')

@section('title', 'Просмотр пакета заказов')

@section('styles')
    <style>
        .block__orderInfo { border: 1px solid #8d8d8d; }
    </style>
@endsection

@section('content')
    <section class="content pt-3">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <span class="badge badge-{{ $ordersbundleInfo['status']['color'] }}">{{ $ordersbundleInfo['status']['text'] }}</span>
                                @yield('title') #{{ $ordersbundleInfo['id'] }}
                            </h3>
                                <button type="button" class="btn btn-primary float-right"><i class="fa fa-file-excel mr-2"></i> Работа с Excel</button>

                        </div>
                        <div class="card block__orders">
                            @foreach($ordersbundleInfo['orders'] as $order)
                                <div class="card block__orderInfo" data-order-id="{{ $order['orderID'] }}">
                                <div class="card-body ">
                                    <div class="card-title w-100">
                                        <div class="d-inline-block">
                                            <div class="d-flex">

                                                <h5 >Заказ #{{ $order['orderID'] }}</h5>
                                                <span class="mx-3"> | </span> {{ date('H:i Y-m-d', strtotime($order['created_at'])) }}
                                            </div>
                                        </div>
                                        <div class="d-inline-block mx-3" style="width: 180px">
                                            <select class="form-control orderStatus " >
                                                @php $os = $order['status']['id'] @endphp
                                                <option value="1" {{ $os === 1 ? 'selected' : '' }}>Создан</option>
                                                <option value="2" {{ $os === 2 ? 'selected' : '' }}>Принят в работу</option>
                                                <option value="3" {{ $os === 3 ? 'selected' : '' }}>Подтверждён</option>
                                                <option value="4" {{ $os === 4 ? 'selected' : '' }}>Ожидает оплату</option>
                                                <option value="5" {{ $os === 5 ? 'selected' : '' }}>Ожидает подтверждения Клиентом</option>
                                                <option value="6" {{ $os === 6 ? 'selected' : '' }}>Доставка</option>
                                                <option value="7" {{ $os === 7 ? 'selected' : '' }}>Принят на складе выдачи</option>
                                                <option value="8" {{ $os === 8 ? 'selected' : '' }}>Ожидает выдачи</option>
                                                <option value="9" {{ $os === 9 ? 'selected' : '' }}>Выдан</option>
                                                <option value="10 {{ $os === 10 ? 'selected' : '' }}">Выдан частично</option>
                                                <option value="11 {{ $os === 11 ? 'selected' : '' }}">Отменён</option>
                                                <option value="12 {{ $os === 12 ? 'selected' : '' }}">Возвращён</option>
                                                <option value="13 {{ $os === 13 ? 'selected' : '' }}">Подтверждён клиентом</option>
                                            </select>
                                        </div>
                                        <button type="button" class="btn btn-danger btn__remove_orderbundle float-right ml-3" data-order-id="{{ $order['orderID'] }}">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                        <button type="button" class="btn btn-success btn__save-order float-right ml-3" data-order-id="{{ $order['orderID'] }}">
                                            Сохранить заказ
                                            <i class="fa fa-check"></i>
                                        </button>
                                        <button type="button" class="btn btn-info btn__showmodal-add-product float-right ml-3" data-order-id="{{ $order['orderID'] }}">
                                            Добавить товар
                                            <i class="fa fa-check"></i>
                                        </button>

                                    </div>
                                    <div class="card-text d-flex justify-content-between">
                                        <div class="mr-5 my-3">
                                            <b>Статус: </b>
                                            <span class="badge badge-{{ $order['status']['color'] }}">{{ $order['status']['text'] }}</span>
                                            <br>
                                            <b>Оплачено: </b> {{ $order['paymentConfirmed'] ? 'Да' : 'Нет' }}
                                            <br>
                                            <b>Нужна предоплата: </b> {{ $order['needPrePayment'] ? 'Да' : 'Нет' }}
                                            <br>
                                            <b>Сумма заказа: </b> {{ $order['totalSum'] }} тнг
                                        </div>
                                        <div class="mr-5 my-3">
                                            <b>Склад: </b>  {{ $order['storage']['title'] }}
                                            <br>
                                            <b>Выдача: </b>  {{ $order['deliveryType'] ? 'Самовывоз со склада' : 'Доставка курьером по городу'}}
                                            <br>
                                            <b>Адрес доставки: </b>  {{ $order['deliveryAddressInCity'] ?? '---' }}
                                            <br>
                                            <b>Комментарий: </b>  {{ $order['comment'] ?? '---' }}
                                        </div>
                                        <div class="mr-5 my-3">
                                            <b>Имя: </b> {{ $order['clientName'] }}
                                            <br>
                                            <b>Телефон: </b> {{ $order['clientLogin'] }}
                                            <br>
                                            <b>Email: </b> {{ $order['clientEmail'] }}
                                            <br>
                                            <b>Тип: </b> {{ $order['buyerType'] ? 'Розничный' : 'Оптовый'}}
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table  table-hover table-striped table-sm">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th scope="col">Артикул</th>
                                                    <th scope="col">Название</th>
                                                    <th scope="col">Завод</th>
                                                    <th scope="col">Цена</th>
                                                    <th scope="col">Кол-во</th>
                                                    <th scope="col">Статус</th>
                                                    <th scope="col">Поставщик</th>
                                                    <th scope="col">***</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($order['products'] as $product)
                                                <tr data-product-id="{{ $product['id'] }}">
                                                    <td>{{ $product['article'] }}</td>
                                                    <td style="max-width: 300px">{{ $product['title'] }}</td>
                                                    <td>{{ $product['manufacturer'] }}</td>
                                                    <td>{{ $product['totalPrice'] }}</td>
                                                    <td>
                                                            <input type="number" min="1" max="10000" class="form-control productAmount" style="width: 70px" value="{{ $product['amount'] }}">
                                                    </td>
                                                    <td>

                                                            <select class="form-control productStatus ">
                                                                <option value="1" {{ $product['status']['id'] === 1 ? 'selected' : '' }}>Обработка</option>
                                                                <option value="2" {{ $product['status']['id'] === 2 ? 'selected' : '' }}>Доставка</option>
                                                                <option value="3" {{ $product['status']['id'] === 3 ? 'selected' : '' }}>Готов к выдаче</option>
                                                                <option value="4" {{ $product['status']['id'] === 4 ? 'selected' : '' }}>Выдан</option>
                                                                <option value="5" {{ $product['status']['id'] === 5 ? 'selected' : '' }}>Нет на складе</option>
                                                                <option value="6" {{ $product['status']['id'] === 6 ? 'selected' : '' }}>Возврат</option>
                                                            </select>
{{--                                                    <td><span class="badge badge-{{ $product['status']['color'] }}">{{ $product['status']['text'] }}</span></td>--}}
                                                    </td>
                                                    <td>{{ $product['supplierTitle'] }}</td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-success btn__save-product" title="Сохранить информацию"><i class="fa fa-xs fa-check"></i></button>
                                                        <button type="button" class="btn btn-sm btn-primary btn__showmodal-edit-product" title="Заменить товар"><i class="fa fa-xs fa-exchange"></i></button>
                                                        <button type="button" class="btn btn-sm btn-danger btn__remove-product" title="Удалить товар из заказа"><i class="fa fa-xs fa-times"></i></button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>

    @include('Admin.pages.orders.modals')

@endsection


@section('scripts')
    <script>
        const BUNDLE_ID = '{{ $ordersbundleInfo['id'] }}';
        const ordersPage = false;  // Нужно для js функций, из за разного шаблона вывода
    </script>

    @include('Admin.pages.orders.scripts')
@endsection
