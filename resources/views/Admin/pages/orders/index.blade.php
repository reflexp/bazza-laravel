@extends('Admin.layouts.app')

@section('title', 'Заказы')

@section('styles')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('/plugins/datatables-select/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/plugins/datatables-select/css/select.bootstrap4.min.css') }}">
@endsection

@section('content')
    <section class="content pt-3">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                @yield('title')
                                <button type="button" class="btn btn-primary mx-2" onclick="jQuery('#modalBundleCreateConfirm').modal('show');">
                                    <i class="fa fa-box-open"></i> Собрать пакет
                                </button>

                            </h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table id="datatable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>#</th>
                                        <th>Дата</th>
                                        <th>Склад</th>
                                        <th>Сумма <br>Статус</th>
                                        <th>Клиент</th>
                                        <th>П</th>
                                        <th>***</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th>#</th>
                                        <th>Дата</th>
                                        <th>Склад</th>
                                        <th>Сумма <br>Статус</th>
                                        <th>Клиент</th>
                                        <th>П</th>
                                        <th>***</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <!-- /.card-body -->
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
    <!-- DataTables  & Plugins -->
    <script src="{{ asset('/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('/plugins/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('/plugins/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('/plugins/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>

    <script src="{{ asset('/plugins/datatables-select/js/dataTables.select.min.js') }}"></script>
    <script src="{{ asset('/plugins/datatables-select/js/select.bootstrap4.min.js') }}"></script>

    <script>
        const ordersPage = true; // Нужно для js функций, из за разного шаблона вывода
        const datatable = $("#datatable").DataTable({
            "dom": 'lfrtip',
            // Blfrtip
            ajax: {
                "type"   : "POST",
                "url"    : "/control/ajax/getOrders",
                "dataSrc": "data",
            },
            order: [[ 1, "desc" ]],
            select: {
                style: 'multi+shift',
                selector: 'td:not(:last-child)'
            },
            columns: [
                {
                    data: null,
                    targets: 0,
                    className: 'select-checkbox',
                    orderable: false,
                    render: function(data, type, full, meta) {
                        return '';
                    }
                },
                { data: 'id', name: 'id' },
                {
                    data: null,
                    name: 'created_at',
                    render: function ( data, type, row ) {
                        return `${ moment(row['created_at']).format('HH:mm') } <br> ${ moment(row['created_at']).format('DD-MM-YYYY') }`;
                    }
                },
                { data: 'storage.title', orderable: false },
                {
                    data: null,
                    name: 'totalSum',
                    render: function ( row ) {
                        return `${row['totalSum']} тнг.<br> <span class="badge badge-${row['status']['color']}">${row['status']['text']}</span>`;
                    }
                },
                {
                    name: 'clientPhone',
                    data: null,
                    orderable: false,
                    render: function ( row ) {
                        return `${ row['client']['name'] } <br> ${ row['client']['login'] }`;
                    }
                },
                {
                    name: 'inBundle',
                    data: null,
                    orderable: false,
                    render: function ( row ) {
                        if (row['inBundle'] === true)
                        {
                            return '<span class="fa fa-circle text-primary"></span>';
                        }
                        else
                        {
                            return '<span class="far fa-circle text-secondary"></span>';
                        }
                    }
                },
                {
                    data: null,
                    orderable: false,
                    render:function(row)
                    {

                        return `<div class="btn-group">
                                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Действие</button>
                                    <div class="dropdown-menu">
                                        <button class="dropdown-item btn-showfull">Подробнее</button>
                                        <button class="dropdown-item btn-control">Управление</button>
                                    </div>
                                </div>`;
                    },
                },

            ],

            processing: true,
            serverSide: true,
            language: {
                "emptyTable": "Таблица пуста",
                "info": "Записи с _START_ до _END_ из _TOTAL_ записей",
                "infoEmpty": "Записи с 0 до 0 из 0 записей",
                "infoFiltered": "(отфильтровано из _MAX_ записей)",

                "lengthMenu": "Отобразить _MENU_ элементов",
                "loadingRecords": "Загружается...",
                "processing": "Обрабатывается...",
                "search": "Поиск:",
                "zeroRecords": "Не найдено",
                "paginate": {
                    "first": "Первая",
                    "last": "Последняя",
                    "next": "Следующая",
                    "previous": "Предыдущая"
                },
                "aria": {
                    "sortAscending": ": активируйте для сортировки по возрастанию",
                    "sortDescending": ": активируйте для сортировки по убыванию"
                }
            }
        });

        /* Подробный просмотр */
        jQuery(document).on('click', '.btn-showfull', function() {
            let modalBody = jQuery('#modalOrderInfo .modal-body');
            const rowData = datatable.row($(this).parents('tr')).data();

            // Переключение форм и отображение модального окна
            jQuery('#modalOrderInfo').modal('show');

            modalBody.empty();
            modalBody.append(
                `<div class="col-lg-12"><h5>Товары</h5>
                <table class="table-products table table-bordered table-striped">
                  </table>`);

            modalBody.append(
                `<div class="col-lg-6">
                <h5>Заказ</h5>
                <table class="table table-bordered table-striped">
                    <tr>
                        <th width="40%">Номер</th>
                        <td>${ rowData['id'] }</td>
                    </tr>
                    <tr>
                        <th>Время и Дата</th>
                        <th>${moment(rowData['created_at']).format('HH:mm')} ${moment(rowData['created_at']).format('DD-MM-YYYY')}</th>
                    </tr>
                    <tr>
                        <th width="30%">Сумма заказа</th>
                        <td>${ rowData['totalSum'] }</td>
                    </tr>
                    <tr>
                        <th width="30%">Нужна предоплата</th>
                        <td>${ rowData['needPrePayment'] === 1 ? '<span class="text-danger">Да</span>' : 'Нет' }</td>
                    </tr>
                    <tr>
                        <th width="30%">Оплачено</th>
                        <td>${ rowData['paymentConfirmed'] === 1 ? '<span class="text-success">Да</span>' : '<span class="text-danger">Нет</span>' }</td>
                    </tr>
                    <tr>
                        <th width="30%">Статус</th>
                        <td><span class="badge badge-${rowData['status']['color']}">${rowData['status']['text']}</span></td>
                    </tr>
                    <tr>
                        <th width="30%">Склад</th>
                        <td>${ rowData['storage']['title']  }</td>
                    </tr>
                    <tr>
                        <th width="30%">Выдача</th>
                        <td>${ rowData['deliveryType'] === 1 ? 'Самовывоз со склада' : rowData['deliveryType'] === 2 ? 'Доставка курьером по городу' : '---' }</td>
                    </tr>
                    <tr>
                        <th width="30%">Адрес доставки</th>
                        <td>${ rowData['deliveryAddressInCity'] ?? '---' }</td>
                    </tr>
                    <tr>
                        <th width="30%">Комментарий </th>
                        <td>${ rowData['comment'] ?? '---' }</td>
                    </tr>
                </table></div>`);

            modalBody.append(
                `<div class="col-lg-6">
                <h5>Клиент</h5>
                <table class="table table-bordered table-striped">
                    <tr>
                        <th>Имя</th>
                        <td>${ rowData['client']['name'] }</td>
                    </tr>
                    <tr>
                        <th>Телефон</th>
                        <td>${ rowData['client']['login'] }</td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td>${ rowData['client']['email'] }</td>
                    </tr>
                    <tr>
                        <th>Тип</th>
                        <td>${ rowData['client']['buyerType'] === 1 ? 'Розничный' : rowData['client']['buyerType'] === 2 ? 'Оптовый' : '---' }</td>
                    </tr>
                </table></div>`);

            let modalBodyProd = modalBody.find('.table-products');

            modalBodyProd.append(`<tr>
                    <th>Артикул</th>
                    <th>Название</th>
                    <th>Завод</th>
                    <th>Цена</th>
                    <th>Кол-во</th>
                    <th>Статус</th>
                    <th>Поставщик</th>
                </tr>`);
            jQuery.each(rowData['products'], function (i, product) {
                modalBodyProd.append(`<tr>
                    <td>${ product['article'] }</td>
                    <td>${ product['title'] }</td>
                    <td>${ product['manufacturer'] ?? '' }</td>
                    <td>${ product['price'] }</td>
                    <td>${ product['amount'] }</td>
                    <td>${ product['status']['text'] }</td>
                    <td>${ product['supplierTitle'] }</td>
                </tr>`);
            });
        });

        /* Управление */
        jQuery(document).on('click', '.btn-control', function() {
            let modal = jQuery('#modalOrderControl');
            let modalBody = modal.find('.modal-body');

            const rowData = datatable.row($(this).parents('tr')).data();
            modal.data('order-id', rowData['id']);

            // Переключение форм и отображение модального окна
            jQuery('#modalOrderControl').modal('show');

            modalBody.empty();
            modalBody.append(
                `<div class="col-lg-12">
                    <button type="button" class="btn btn-success btn__showmodal-add-product my-3" data-order-id="${ rowData['id'] }">
                        Добавить товар
                        <i class="fa fa-check"></i>
                    </button>
                    <h5>Товары <small>(при сохранении позиции цена автоматически изменится)</small>
                </h5>
                <table class="table-products table table-bordered table-striped">
                  </table>`);
            let modalBodyProd = modalBody.find('.table-products');

            modalBody.append(
                `<div class="col-lg-6">
                <h5>Заказ</h5>
                <table class="table table-bordered table-striped">
                    <tr>
                        <th width="40%">Номер</th>
                        <td>${ rowData['id'] }</td>
                    </tr>
                    <tr>
                        <th>Время и Дата</th>
                        <th>${moment(rowData['created_at']).format('HH:mm')} ${moment(rowData['created_at']).format('DD-MM-YYYY')}</th>
                    </tr>
                    <tr>
                        <th>Сумма заказа</th>
                        <td>${ rowData['totalSum'] }</td>
                    </tr>
                    <tr>
                        <th width="30%">Нужна предоплата</th>
                        <td>${ rowData['needPrePayment'] === 1 ? '<span class="text-danger">Да</span>' : 'Нет' }</td>
                    </tr>
                    <tr>
                        <th>Оплачен</th>
                            <td>
                                <select class="form-control orderPayStatus">
                                    <option value="1" class="text-success">Да</option>
                                    <option value="0" class="text-danger">Нет</option>
                                </select>
                            </td>
                    </tr>
                    <tr>
                        <th>Статус</th>
                        <td>
                            <select class="form-control orderStatus">
                                <option value="1">Создан</option>
                                <option value="2">Принят в работу</option>
                                <option value="3">Подтверждён</option>
                                <option value="4">Ожидает оплату</option>
                                <option value="5">Ожидает подтверждения Клиентом</option>
                                <option value="6">Доставка</option>
                                <option value="7">Принят на складе выдачи</option>
                                <option value="8">Ожидает выдачи</option>
                                <option value="9">Выдан</option>
                                <option value="10">Выдан частично</option>
                                <option value="11">Отменён</option>
                                <option value="12">Возвращён</option>
                                <option value="13">Подтверждён клиентом</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>Клиент</th>
                        <td>
                            [   ${ rowData['client']['buyerType'] === 1 ? 'Розничный' : rowData['client']['buyerType'] === 2 ? 'Оптовый' : '---' }]
                            ${ rowData['client']['name'] }, ${ rowData['client']['login'] }
                        </td>
                    </tr>
                    <tr>
                        <th >Комментарий </th>
                        <td><input type="text" class="form-control orderComment" style="width: 100%" value="${ rowData['comment'] ?? ''}"></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="text-right">
                            <button type="button" class="btn btn-block btn-success btn__save-order" data-order-id="${ rowData['id'] }">Сохранить заказ <i class="fa fa-check"></i></button>
                        </td>
                    </tr>
                </table></div>`);

            modalBody.find(`.orderStatus option[value="${ rowData['status']['id'] }"]`).attr('selected', true);
            modalBody.find(`.orderPayStatus option[value="${ rowData['paymentConfirmed'] }"]`).attr('selected', true);

            modalBodyProd.append(`<tr>
                        <th>Артикул</th>
                        <th>Название</th>
                        <th>Завод</th>
                        <th>Цена</th>
                        <th>Кол-во</th>
                        <th>Статус</th>
                        <th>Поставщик</th>
                        <th>***</th>
                    </tr>`);
            jQuery.each(rowData['products'], function (i, product) {
                modalBodyProd.append(`<tr data-product-id="${ product['id'] }">
                        <td>${ product['article'] }</td>
                        <td>${ product['title'] }</td>
                        <td>${ product['manufacturer'] ?? '' }</td>
                        <td>${ product['price'] }</td>
                        <td><input type="number" min="1" max="10000" class="form-control productAmount" value="${ product['amount'] }"></td>
                        <td>
                                <select class="form-control productStatus ">
                                    <option value="1">Обработка</option>
                                    <option value="2">Доставка</option>
                                    <option value="3">Готов к выдаче</option>
                                    <option value="4">Выдан</option>
                                    <option value="5">Нет на складе</option>
                                    <option value="6">Возврат</option>
                                </select>
                        </td>
                        <td>${ product['supplierTitle'] }</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-success btn__save-product" title="Сохранить информацию"><i class="fa fa-xs fa-check"></i></button>
                            <button type="button" class="btn btn-sm btn-primary btn__showmodal-edit-product" title="Заменить товар"><i class="fa fa-xs fa-exchange"></i></button>
                            <button type="button" class="btn btn-sm btn-danger btn__remove-product" title="Удалить товар из заказа"><i class="fa fa-xs fa-times"></i></button>
                        </td>
                    </tr>`);
                modalBodyProd.find(`tr:last .productStatus option[value="${ product['status']['id'] }"]`).attr('selected', true);
            });

        });

        /*
         * Сохраняет новые данные о заказе
         * orderPayStatus
         * orderStatus
         * orderComment
         * */


        /*
        * Создание пакета из выбранных заказов
        * Создать можно только если выбран хоть один заказ
        * Если заказы имеют только статус Создан
        * */
        jQuery(document).on('click', '.btn__send-create-orders-bundle', function() {
            let selectedRows = $('#datatable').DataTable().rows('.selected').data();


            if(selectedRows.length === 0)
            {
                showNotification('error', 'Ошибка, не выбрано ни одной строки');
                return false
            }

            let orders = [];
            let error = false;
            jQuery.each(selectedRows, function (i, row) {

                console.log(row);
                if(row['inBundle'] === true)
                {
                    showNotification('error', `Ошибка, заказ #${row['id']} уже состоит в пакете`);
                    error = true;
                }

                if(row['status']['id'] !== 1)
                {
                    showNotification('error', `Ошибка, заказ #${row['id']} не имеет статус "Создан"`);
                    error = true;
                }
                else
                {
                    orders.push(row['id']);
                }

            });

            jQuery('#modalBundleCreateConfirm').modal('hide');

            if(!error)
            {
                const formData = new FormData();
                let comment = jQuery('#modalBundleCreateConfirm [name="comment"]').val();
                formData.append('orders', JSON.stringify(orders));
                formData.append('comment', comment);
                let response = apiRequest('createOrdersBundle', formData);
                if(response.ok === true)
                {
                    jQuery('#modalBundleCreateConfirm [name="comment"]').val('');
                    // jQuery('#modalBundleCreateConfirm').modal('hide');
                    window.location.href = '{{ route('Admin.ordersbundles') }}';
                }
            }

        });



    </script>
    @include('Admin.pages.orders.scripts')
@endsection

