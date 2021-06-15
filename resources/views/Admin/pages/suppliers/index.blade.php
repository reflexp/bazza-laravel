@extends('Admin.layouts.app')

@section('title', 'Поставщики')

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
                            <h3 class="card-title">Таблица поставщиков</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="col-12 px-0 pb-3">
                                <a href="{{ Route('Admin.suppliers.create') }}">
                                    <button type="button" class="btn btn-primary">Добавить поставщика</button>
                                </a>
                            </div>
                            <table id="datatable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Имя</th>
                                        <th>Юридическая информация</th>
                                        <th>Действия</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Имя</th>
                                        <th>Юридическая информация</th>
                                        <th>Действия</th>
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
    {{-- Инициализирование Datatable --}}
    <script>
        const datatable = $("#datatable").DataTable({
            "dom": 'lfrtip',
            // Blfrtip
            "ajax": {
                "type"   : "POST",
                "url"    : "/control/ajax/getSuppliers",
                "dataSrc": "result.suppliers",
            },
            "columns": [
                { "data": "title" },
                { "data": "yurInfo" },
                {   
                    "orderable": false,
                    "searchable": false,
                    "data": null,
                    "className": "d-flex justify-content-center",
                    render:function(data)
                    {
                        return `<div class="btn-group"><button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Действие</button><div class="dropdown-menu"><a href="/control/suppliers/edit/${data['id']}" class="dropdown-item btn-edit">Изменить</a></div></div>`;
                    }, 
                    "targets": -1
                },
            ],
            "responsive": true,
            "lengthChange": true,
            "autoWidth": false,
            "paging": true,
            "ordering": true,
            "info": true,
            "buttons": [
                {
                    extend: 'copy',
                    text: 'Скачать Excel'
                },
                {
                    extend: 'csv',
                    text: 'Скачать CSV'
                },
                {
                    extend: 'excel',
                    text: 'Скачать Excel'
                },
                {
                    extend: 'pdf',
                    text: 'Скачать PDF'
                },
                {
                    extend: 'print',
                    text: 'Печать'
                },
            ],
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
    </script>
@endsection
