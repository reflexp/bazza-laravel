@extends('Admin.layouts.app')

@section('title', 'Пользователи')

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
                            <h3 class="card-title">Таблица клиентов</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="col-12 px-0 pb-3">
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalContainer">Добавить клиента</button>
                            </div>
                            <table id="datatable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Имя</th>
                                        <th>Телефон</th>
                                        <th>Почта</th>
                                        <th>Подтвержден</th>
                                        <th>Действия</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Имя</th>
                                        <th>Телефон</th>
                                        <th>Почта</th>
                                        <th>Подтвержден</th>
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
                      <h5 class="modal-title" id="modalTitle">Добавление клиента</h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>

                    <form id="createClientForm">
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="name">Имя клиента</label>
                                <input type="text" class="form-control" name="name" placeholder="Введите имя">
                            </div>
                            <div class="form-group">
                                <label for="login">Телефон</label>
                                <input type="tel" class="form-control" name="login" placeholder="+7" autocomplete="off">
                            </div>
                            <div class="form-group">
                                <label for="email">E-Mail клиента</label>
                                <input type="email" class="form-control" name="email" placeholder="E-Mail">
                            </div>
                            <div class="form-group">
                                <label for="password">Пароль</label>
                                <input type="password" class="form-control" name="password" placeholder="Пароль">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                            <button type="submit" class="btn btn-primary">Добавить клиента</button>
                        </div>
                    </form>

                    <form id="saveClientForm" class="d-none">
                        <div class="modal-body">
                            <input type="hidden" name="id">
                            <div class="form-group">
                                <label for="name">Имя клиента</label>
                                <input type="text" class="form-control" name="name" placeholder="Введите имя">
                            </div>
                            <div class="form-group">
                                <label for="login">Телефон</label>
                                <input type="tel" class="form-control" name="login" placeholder="+7" autocomplete="off">
                            </div>
                            <div class="form-group">
                                <label for="email">E-Mail клиента</label>
                                <input type="email" class="form-control" name="email" placeholder="E-Mail">
                            </div>
                            <div class="form-group">
                                <label for="password">Новый пароль</label>
                                <input type="password" class="form-control" name="password" placeholder="Пароль">
                                <small class="form-text text-muted">Если вы оставите это поле пустым, то пароль останется неизменным.</small>
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
        const datatable = $("#datatable").DataTable({
            "dom": 'lfrtip',
            // Blfrtip
            "ajax": {
                "type"   : "POST",
                "url"    : "/control/ajax/getClients",
                "dataSrc": "result.clients",
            },
            "columns": [
                { "data": "name" },
                { "data": "login" },
                { "data": "email" },
                { 
                    "data": null,
                    render:function(data, type, row) {
                        if (data.active === 1) {
                            return 'Активирован';
                        } else {
                            return 'Требует активации';
                        }
                    },
                    "targets": -1
                },
                {   
                    "orderable": false,
                    "searchable": false,
                    "data": null,
                    "className": "d-flex justify-content-center",
                    render:function(data)
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
            jQuery(document).on('submit', '#createClientForm', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const addData = {'test': 'test'};
                
                Object.keys(addData).map((current, index) => formData.append(current, addData[current]));
            
                const response = apiRequest('addClient', formData);

                if (response.ok === true) {
                    this.reset();
                    updateTable(datatable);
                    jQuery('#modalContainer').modal('hide');
                }
            });

            // Сохранение
            jQuery(document).on('submit', '#saveClientForm', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const addData = {'test': 'test'};

                Object.keys(addData).map((current, index) => formData.append(current, addData[current]));

                const response = apiRequest('editClient', formData);

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
                jQuery('#saveClientForm').removeClass('d-none');
                jQuery('#createClientForm').addClass('d-none');
                jQuery('#modalTitle').html('Изменение данных клиента');
                // Переключение форм и отображение модального окна
                jQuery('#saveClientForm').find('input[name="id"]').val(rowData['id']);
                jQuery('#saveClientForm').find('input[name="name"]').val(rowData['name']);
                jQuery('#saveClientForm').find('input[name="login"]').val(rowData['login']);
                jQuery('#saveClientForm').find('input[name="email"]').val(rowData['email']);
            });

            function deleteClient(id) {
                const formData = new FormData();
                formData.append('id', parseInt(id));
                const response = apiRequest('deleteClient', formData);
                jQuery('#modalNotyfContainer .btn-confirm').off('click');
                jQuery('#modalNotyfContainer').modal('hide');
            }

            // Модалка подтверждения удаления
            jQuery(document).on('click', '.btn-delete', function() {
                const rowData = datatable.row($(this).parents('tr')).data();
                jQuery('#modalNotyfContainer').modal('show');
                jQuery('#modalNotyfTitle').html('Удаление клиента');
                jQuery('#modalNotyfContainer .modal-body').html(`Вы точно уверены, что хотите удалить клиента - <b>${rowData['name']}</b>?`);
                jQuery('#modalNotyfContainer .btn-confirm').on('click', () => { deleteClient(rowData['id']); });
            });
            
            // Bootstrap event на закрытие модалки
            $('#modalContainer').on('hide.bs.modal', function (e) {
                jQuery('#saveClientForm').addClass('d-none');
                jQuery('#createClientForm').removeClass('d-none');
                jQuery('#modalTitle').html('Добавление клиента');
            });
        });
    </script>
@endsection
