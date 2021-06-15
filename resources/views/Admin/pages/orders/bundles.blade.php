@extends('Admin.layouts.app')

@section('title', 'Пакеты заказов')

@section('styles')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
@endsection

@section('content')
    <section class="content pt-3">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">@yield('title')</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table id="datatable" class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Дата</th>
                                    <th>Склад обработки</th>
                                    <th>Статус</th>
                                    <th>Комментарий</th>
                                    <th>***</th>
                                </tr>
                                </thead>
                                <tfoot>
                                <tr>
                                    <th>#</th>
                                    <th>Дата</th>
                                    <th>Склад обработки</th>
                                    <th>Статус</th>
                                    <th>Комментарий</th>
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

    <div class="modal fade" id="modalOrdersBundleInfo" >
        <div class="modal-dialog modal-dialog-centered modal-lg " role="document"> {{--            modal-dialog-scrollable--}}
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalNotyfTitle">Информация</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body row">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                </div>
            </div>
        </div>
    </div>
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
    <script>
        const bundleInfoRoute = '{{ route('Admin.ordersbundles') }}';

        const datatable = $("#datatable").DataTable({
            "dom": 'lfrtip',
            // Blfrtip
            "ajax": {
                "type"   : "POST",
                "url"    : "/control/ajax/getOrdersBundles",
                "dataSrc": "result.ordersbundles",
            },
            "columns": [
                { data: 'id' },
                {
                    data: null,
                    name: 'created_at',
                    render: function ( data, type, row ) {
                        return `${ moment(row['created_at']).format('HH:mm') } <br> ${ moment(row['created_at']).format('DD-MM-YYYY') }`;
                    }
                },
                {
                    data: null,
                    render: function ( row ) {
                        return `${ row['storageTitle'] ?? '' }`;
                    }
                },
                {
                    data: null,
                    render: function ( row ) {
                        return `<span class="badge badge-${row['status']['color']}">${row['status']['text']}</span>`;
                    }
                },
                {
                    data: null,
                    render: function ( row ) {
                        return `${ row['comment'] ?? '' }`;
                    }
                },
                {
                    data: null,
                    render: function ( data, type, row ) {
                        return `<a class="btn btn-primary" href="${ bundleInfoRoute }/${ data['id'] }">Подробнее</a>`;
                    }
                },

            ],
            "searching": false,
            "responsive": true,
            "lengthChange": true,
            "autoWidth": false,
            "paging": false,
            "ordering": false,
            "info": true,
            "language": {
                "emptyTable": "Таблица пуста",
                "info": "",
                "infoEmpty": "",
                "infoFiltered": "",
                "infoPostFix": "",
                "thousands": ",",
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
            let modalBody = jQuery('#modalOrdersBundleInfo .modal-body');
            const rowData = datatable.row($(this).parents('tr')).data();

            // Переключение форм и отображение модального окна
            jQuery('#modalOrdersBundleInfo').modal('show');

            modalBody.empty();
            modalBody.append(
                `<div class="col-lg-12"><h5>Товары</h5>
                <table class="table-orders table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Статус</th>
                            <th>Сумма</th>
                            <th>Клиент</th>
                            <th>Дата</th>
                            <th>Склад</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                  </table>`);

            let modalBodyProd = modalBody.find('.table-orders');

            modalBodyProd.append();
            jQuery.each(rowData['orders'], function (i, order) {
                modalBodyProd.append(`<tr>
                    <td>${ order['id'] }</td>
                    <td>${ order['status'] }</td>
                    <td>${ order['totalSum'] }</td>
                    <td>${ order['clientID'] }</td>
                    <td>${ order['created_at'] }</td>
                    <td>${ order['deliveryToStorageID'] ?? '' }</td>
                </tr>`);
            });
        });
    </script>
@endsection
