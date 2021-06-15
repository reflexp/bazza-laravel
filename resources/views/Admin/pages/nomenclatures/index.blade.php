@extends('Admin.layouts.app')

@section('title', 'Номенклатуры')

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
                            <h3 class="card-title">Таблица номенклатур</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="col-12 px-0 pb-3">
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalContainer">Загрузить номенклатуру</button>
                            </div>
                            <table id="datatable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Артикул</th>
                                        <th>Артикул поставщика</th>
                                        <th>Поставщик</th>
                                        <th>Наименование</th>
                                        <th>Цена</th>
                                        <th>Количество</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Артикул</th>
                                        <th>Артикул поставщика</th>
                                        <th>Поставщик</th>
                                        <th>Наименование</th>
                                        <th>Цена</th>
                                        <th>Количество</th>
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
            <div class="modal fade" id="modalContainer" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="modalTitle">Загрузить номенклатуру</h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>

                    <form id="uploadForm" enctype='multipart/form-data'>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="article">Колонка артикула</label>
                                <input type="number" min="1" class="form-control" name="article" placeholder="Например: 1">
                            </div>
                            <div class="form-group">
                                <label for="supplierID">Выбор поставщика</label>
                                <select name="supplierID" class="browser-default custom-select">
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="supplierArticle">Колонка артикула поставщика</label>
                                <input type="number" min="1" class="form-control" name="supplierArticle" placeholder="Например: 3">
                            </div>
                            <div class="form-group">
                                <label for="title">Колонка наименования товара</label>
                                <input type="number" min="1" class="form-control" name="title" placeholder="Например: 4">
                            </div>
                            <div class="form-group">
                                <label for="manufacturer">Колонка производителя</label>
                                <input type="number" min="1" class="form-control" name="manufacturer" placeholder="Например: 5">
                            </div>
                            <div class="form-group">
                                <label for="price">Колонка цены</label>
                                <input type="number" min="1" class="form-control" name="price" placeholder="Например: 6">
                            </div>
                            <div class="form-group">
                                <label for="amount">Колонка количества</label>
                                <input type="number" min="1" class="form-control" name="amount" placeholder="Например: 7">
                            </div>
                            <div class="form-group">
                                <label for="additionText">Колонка дополнительного текста</label>
                                <input type="number" min="1" class="form-control" name="additionText" placeholder="Например: 8">
                            </div>
                            <div class="form-group">
                                <label for="startRow">Строка с которой начинается таблица (включительно)</label>
                                <input type="number" min="1" class="form-control" name="startRow" placeholder="Например: 12">
                            </div>
                            <div class="form-group">
                                <label for="file">Номенклатура</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="file">
                                    <label class="custom-file-label" for="file">Файл номенклатуры</label>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                            <button type="submit" class="btn btn-primary">Загрузить номенклатуру</button>
                        </div>
                    </form>
                  </div>
                </div>
            </div>
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
    <script>
        const datatable = $("#datatable").DataTable({
            "dom": 'lfrtip',
            // Blfrtip
            "ajax": {
                "type"   : "POST",
                "url"    : "/control/ajax/getNomenclatures",
                "dataSrc": "data",
            },
            "columns": [
                { "data": "article" },
                { "data": "supplierArticle" },
                { "data": "supplierName" },
                { "data": "title" },
                { "data": "price" },
                { "data": "amount" },
            ],
            "responsive": true,
            "lengthChange": true,
            "autoWidth": false,
            "paging": true,
            "ordering": true,
            "info": true,
            "processing": true,
            "serverSide": true,
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
    <script>
        window.addEventListener('load', () => {          
            jQuery(document).on('submit', '#uploadForm', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const addData = {'test': 'test'};

                Object.keys(addData).map((current, index) => formData.append(current, addData[current]));

                const response = apiRequest('uploadFile/nomenclature', formData);

                if (response.ok === true) {
                    updateTable(datatable);
                    this.reset();
                    jQuery('#modalContainer').modal('hide');
                }
            });
        });
    </script>
@endsection
