@extends('Admin.layouts.app')

@section('title', 'Сотрудники')

@section('styles')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/plugins/select2/css/select2-bootstrap4.min.css') }}">
@endsection

@section('content')
    <section class="content pt-3">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Таблица сотрудников</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="col-12 px-0 pb-3">
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalContainer">Добавить сотрудника</button>
                            </div>
                            <table id="datatable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Имя</th>
                                        <th>Телефон</th>
                                        <th>Почта</th>
                                        <th>Роль</th>
                                        <th>Действия</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Имя</th>
                                        <th>Телефон</th>
                                        <th>Почта</th>
                                        <th>Роль</th>
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
                      <h5 class="modal-title" id="modalTitle">Добавление сотрудника</h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>

                    <form id="createUserForm">
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="name">Имя сотрудника</label>
                                <input type="text" class="form-control" name="name" placeholder="Введите имя">
                            </div>
                            <div class="form-group">
                                <label for="email">E-Mail сотрудника</label>
                                <input type="email" class="form-control" name="email" placeholder="E-Mail">
                            </div>
                            <div class="form-group">
                                <label for="role">Роль сотрудника</label>
                                <select name="role" class="browser-default custom-select">
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group d-none storageAttachment">
                                <label for="storage">Связанные склады</label>
                                <select name="storages[]" class="select2-element">
                                    @foreach ($storages as $storage)
                                        <option value="{{ $storage->id }}">{{ $storage->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="login">Телефон</label>
                                <input type="tel" class="form-control" name="login" placeholder="+7" minlength="" autocomplete="off">
                            </div>
                            <div class="form-group">
                                <label for="password">Пароль</label>
                                <input type="password" class="form-control" name="password" placeholder="Пароль">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                            <button type="submit" class="btn btn-primary">Добавить сотрудника</button>
                        </div>
                    </form>

                    <form id="saveUserForm" class="d-none">
                        <div class="modal-body">
                            <input type="hidden" name="id">
                            <div class="form-group">
                                <label for="name">Имя сотрудника</label>
                                <input type="text" class="form-control" name="name" placeholder="Введите имя">
                            </div>
                            <div class="form-group">
                                <label for="email">E-Mail сотрудника</label>
                                <input type="email" class="form-control" name="email" placeholder="E-Mail">
                            </div>
                            <div class="form-group">
                                <label for="role">Роль сотрудника</label>
                                <select name="role" class="browser-default custom-select">
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group d-none storageAttachment">
                                <label for="storage">Связанные склады</label>
                                <select name="storages[]" class="select2-element">
                                    @foreach ($storages as $storage)
                                        <option value="{{ $storage->id }}">{{ $storage->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="login">Телефон</label>
                                <input type="tel" class="form-control" name="login" placeholder="+7" autocomplete="off">
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
    <!-- Select2 -->
    <script src="{{ asset('/plugins/select2/js/select2.min.js') }}"></script>
    {{-- Инициализирование Datatable --}}
    <script>
        const roles = apiRequest('getRoles');

        const datatable = $("#datatable").DataTable({
            "dom": 'lfrtip',
            // Blfrtip
            "ajax": {
                "type"   : "POST",
                "url"    : "/control/ajax/getUsers",
                "dataSrc": "result.users",
            },
            "columns": [
                { "data": "name" },
                { "data": "login" },
                { "data": "email" },
                {
                    "data": null,
                    render:function(data, type, row) {
                        for(const role of roles) {
                            if (role.id === data.roleID) {
                                return role.title;
                            }
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
            jQuery('select[name="role"]').on('change', function(e) {
            (parseInt(this.value) === 2) ? jQuery('.storageAttachment').removeClass('d-none') : jQuery('.storageAttachment').addClass('d-none');
            });

            jQuery(".select2-element").select2({
                placeholder: "Выберите склады",
                data: null,
                multiple: true,
                theme: "bootstrap4"
            });

            // Создание
            jQuery(document).on('submit', '#createUserForm', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const addData = {'test': 'test'};

                Object.keys(addData).map((current, index) => formData.append(current, addData[current]));

                const response = apiRequest('addUser', formData);

                if (response.ok === true) {
                    updateTable(datatable);
                    this.reset();
                    jQuery('#modalContainer').modal('hide');
                }
            });

            // Сохранение
            jQuery(document).on('submit', '#saveUserForm', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const addData = {'test': 'test'};

                Object.keys(addData).map((current, index) => formData.append(current, addData[current]));

                const response = apiRequest('editUser', formData);

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
                jQuery('#saveUserForm').removeClass('d-none');
                jQuery('#createUserForm').addClass('d-none');
                jQuery('#modalTitle').html('Изменение данных сотрудника');
                // Переключение форм и отображение модального окна
                jQuery('#saveUserForm').find('input[name="id"]').val(rowData['id']);
                jQuery('#saveUserForm').find('input[name="name"]').val(rowData['name']);
                jQuery('#saveUserForm').find('input[name="email"]').val(rowData['email']);
                jQuery('#saveUserForm').find(`option[value="${rowData['roleID']}"]`).prop('selected', true);
                jQuery('#saveUserForm').find('input[name="login"]').val(rowData['login']);
                if (rowData['roleID'] === 2) {
                    const formData = new FormData();
                    formData.append('id', rowData['id']);
                    const response = apiRequest('getUser', formData);
                    const storages = [];

                    for(storage of response) {
                        storages.push(storage['storageID']);
                    }

                    jQuery('#saveUserForm').find('.select2-element').val(storages);
                    jQuery('#saveUserForm').find('.select2-element').trigger('change');
                    jQuery('.storageAttachment').removeClass('d-none');
                }
            });

            function deleteUser(id) {
                const formData = new FormData();
                formData.append('id', parseInt(id));
                const response = apiRequest('deleteUser', formData);
                jQuery('#modalNotyfContainer .btn-confirm').off('click');
                jQuery('#modalNotyfContainer').modal('hide');
            }

            // Модалка подтверждения удаления
            jQuery(document).on('click', '.btn-delete', function() {
                const rowData = datatable.row($(this).parents('tr')).data();
                jQuery('#modalNotyfContainer').modal('show');
                jQuery('#modalNotyfTitle').html('Удаление сотрудника');
                jQuery('#modalNotyfContainer .modal-body').html(`Вы точно уверены, что хотите удалить сотрудника - <b>${rowData['name']}</b>?`);
                jQuery('#modalNotyfContainer .btn-confirm').on('click', () => { deleteUser(rowData['id']); });
            });

            // Bootstrap event на закрытие модалки
            $('#modalContainer').on('hide.bs.modal', function (e) {
                jQuery('#saveUserForm').addClass('d-none');
                jQuery('#createUserForm').removeClass('d-none');
                jQuery('#modalTitle').html('Добавление сотрудника');
                jQuery('.storageAttachment').addClass('d-none');
            });
        });
    </script>
@endsection
