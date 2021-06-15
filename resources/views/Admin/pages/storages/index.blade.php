@extends('Admin.layouts.app')

@section('title', 'Склады')

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
                            <h3 class="card-title">Таблица складов</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="col-12 px-0 pb-3">
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalContainer">Добавить новый склад</button>
                            </div>
                            <table id="datatable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Наименование</th>
                                        <th>Город</th>
                                        <th>Адрес</th>
                                        <th>Телефон</th>
                                        <th>Действия</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Наименование</th>
                                        <th>Город</th>
                                        <th>Адрес</th>
                                        <th>Телефон</th>
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
            <div class="modal fade" id="modalContainer" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="modalTitle">Добавление склада</h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>

                    <form id="createStorageForm">
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="title">Наименование склада</label>
                                <input type="text" class="form-control" name="title" placeholder="Введите наименование склада">
                            </div>
                            <div class="form-group">
                                <label for="city">Город</label>
                                <select name="city" class="browser-default custom-select">
                                    @foreach ($cities as $city)
                                        <option value="{{ $city->id }}">{{ $city->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="address">Адрес</label>
                                <input type="text" class="form-control" name="address" placeholder="Адрес">
                            </div>
                            <div class="form-group">
                                <label for="contactPhone">Телефон</label>
                                <input type="tel" class="form-control" name="contactPhone" placeholder="+7" autocomplete="off">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                            <button type="submit" class="btn btn-primary">Создать склад</button>
                        </div>
                    </form>

                    <form id="saveStorageForm" class="d-none">
                        <input type="hidden" name="id">
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="title">Наименование склада</label>
                                <input type="text" class="form-control" name="title" placeholder="Введите наименование склада">
                            </div>
                            <div class="form-group">
                                <label for="city">Город</label>
                                <select name="city" class="browser-default custom-select">
                                    @foreach ($cities as $city)
                                        <option value="{{ $city->id }}">{{ $city->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="address">Адрес</label>
                                <input type="text" class="form-control" name="address" placeholder="Адрес">
                            </div>
                            <div class="form-group">
                                <label for="contactPhone">Телефон</label>
                                <input type="tel" class="form-control" name="contactPhone" placeholder="+7" autocomplete="off">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                            <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                        </div>
                    </form>
                  </div>
                </div>
            </div>

            <div class="modal fade" id="modalNotyfContainer" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="modalNotyfTitle">Modal title</h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                    <div class="modal-body">
                        ...
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                      <button type="button" class="btn btn-danger btn-confirm">Подтвердить удаление</button>
                    </div>
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
    {{-- Инициализирование Datatable --}}
    <script>
        const cities = apiRequest('getCities');
        
        const datatable = $("#datatable").DataTable({
            "dom": 'lfrtip',
            // Blfrtip
            "ajax": {
                "type"   : "POST",
                "url"    : "/control/ajax/getStorages",
                "dataSrc": "result.storages",
            },
            "columns": [
                { "data": "title" },
                { 
                    "data": null,
                    render:function(data, type, row) {
                        for(const city of cities) {
                            if (city.id === data.cityID) {
                                return city.title;
                            }
                        }
                    },
                    "targets": -1
                },
                { "data": "address" },
                { "data": "contactPhone" },
                {   
                    "orderable": false,
                    "searchable": false,
                    "data": null,
                    "className": "d-flex justify-content-center",
                    render:function(data, type, row)
                    {
                        return `<div class="btn-group"><button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Действие</button><div class="dropdown-menu"><button class="dropdown-item btn-delete">Удалить</button><button class="dropdown-item btn-edit">Изменить</button></div></div>`;
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
    <script>
        window.addEventListener('load', () => {
            // Создание
            jQuery(document).on('submit', '#createStorageForm', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                // const addData = {'test': 'test'};

                // Object.keys(addData).map((current, index) => formData.append(current, addData[current]));

                const response = apiRequest('addStorage', formData);

                if (response.ok === true) {
                    this.reset();
                    updateTable(datatable);
                    jQuery('#modalContainer').modal('hide');
                }
            });

            // Сохранение
            jQuery(document).on('submit', '#saveStorageForm', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const addData = {'test': 'test'};

                Object.keys(addData).map((current, index) => formData.append(current, addData[current]));

                const response = apiRequest('editStorage', formData);

                if (response.ok === true) {
                    this.reset();
                    updateTable(datatable);
                    jQuery('#modalContainer').modal('hide');
                }
            });

            // Редактирование
            jQuery(document).on('click', '.btn-edit', function() {
                const rowData = datatable.row($(this).parents('tr')).data();
                // Переключение форм и отображение модального окна
                jQuery('#modalContainer').modal('show');
                jQuery('#saveStorageForm').removeClass('d-none');
                jQuery('#createStorageForm').addClass('d-none');
                jQuery('#modalTitle').html('Изменение данных склада');
                // Переключение форм и отображение модального окна
                jQuery('#saveStorageForm').find('input[name="id"]').val(rowData['id']);
                jQuery('#saveStorageForm').find('input[name="title"]').val(rowData['title']);
                jQuery('#saveStorageForm').find('input[name="address"]').val(rowData['address']);
                jQuery('#saveStorageForm').find(`option[value="${rowData['cityID']}"]`).prop('selected', true);
                jQuery('#saveStorageForm').find('input[name="contactPhone"]').val(rowData['contactPhone']);
            });

            function deleteStorage(id) {
                const formData = new FormData();
                formData.append('id', parseInt(id));
                const response = apiRequest('deleteStorage', formData);
                jQuery('#modalNotyfContainer .btn-confirm').off('click');
                jQuery('#modalNotyfContainer').modal('hide');
            }

            // Модалка подтверждения удаления
            jQuery(document).on('click', '.btn-delete', function() {
                const rowData = datatable.row($(this).parents('tr')).data();
                jQuery('#modalNotyfContainer').modal('show');
                jQuery('#modalNotyfTitle').html('Удалить склад');
                jQuery('#modalNotyfContainer .modal-body').html(`Вы точно уверены, что хотите удалить следующий склад - <b>${rowData['title']}</b>?`);
                jQuery('#modalNotyfContainer .btn-confirm').on('click', () => { deleteStorage(rowData['id']); });
            });

            // Bootstrap event на закрытие модалки
            $('#modalContainer').on('hide.bs.modal', function (e) {
                jQuery('#saveUserForm').addClass('d-none');
                jQuery('#createUserForm').removeClass('d-none');
                jQuery('#modalTitle').html('Добавление склада');
            });
        });
    </script>
@endsection
