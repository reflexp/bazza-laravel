@extends('Admin.layouts.app')

@section('title', 'Номенклатуры')

@section('styles')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <style>
        .card-body {
            min-height: calc(100vh - 56px - 0.5rem - 70px);
            max-height: calc(100vh - 56px - 0.5rem - 70px);
        }

        .text-left .card-body, .text-right .card-body {
            min-height: auto;
            max-height: auto;
        }

        .messages-container {
            overflow-y: auto;
            height: calc(100vh - 170px - 140px - 0.75rem); 
        }

        .messages-container .card-body b {
            font-size: 16px;
            font-weight: 700;
        }

        .messages-container .card-body p {
            font-size: 14px;
            margin-bottom: 0;
        }

        .messages-container .card {
            max-width: 90%;
        }

        .list-group {
            border: none;
            overflow-y: auto;
            height: calc(100vh - 170px); 
        }

        .custom-file-label {
            border: none;
            display: flex;
            cursor: pointer;
            align-items: center
        }

        .custom-file-label:after {
            display: none;
        }

        .client-name {
            display: flex;
            align-items: center;
        }

        .client-name i {
            margin-top: 2px;
            font-size: 10px;
        }

        .form__file {
            flex: 1;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            border-radius: 5px;
            margin-right: .5rem;
            background-color: #e3e3e3;
        }

        .form__file p {
            display: flex;
            font-size: 14px;
            margin-bottom: 0;
            align-items: center;
            justify-content: center;
        }

        .form__file p i {
            margin-right: .2rem;
        }

        .form__file input {
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            margin: 0;
            position: absolute;
        }
    </style>
@endsection

@section('content')
    <section class="content pt-3">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 id="mainHeader" class="card-title">Чат с клиентами</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body row">
                            <div class="col-md-4">
                                <div class="list-group sidebar__chats">
                                    {{-- Здесь будут доступные чаты для выбора --}}
                                </div>
                            </div>

                            <div class="col-md-8">
                                <div class="alert alert-light border-0" role="alert">
                                    <h4 class="alert-heading">Диалог не выбран</h4>
                                    <p>Для отображения сообщений с клиентом, необходимо кликнуть по одному из диалогов в левом меню.</p>
                                    <hr>
                                    <p class="mb-0">В случае если левое меню пустое, то клиент еще не связался с вами по чату.</p>
                                  </div>

                                <div class="messages-container pr-3 mt-3 mt-md-0 d-none">
                                    {{-- Здесь будут сообщения --}}
                                </div>

                                <form id="messageForm" class="mt-3 d-none">
                                    <div class="form-group">
                                      <textarea class="form-control" name="message" rows="3" placeholder="Введите сообщение" autocomplete="off" required></textarea>
                                    </div>
                                    <div class="d-flex justify-content-end">
                                        <div class="form__file">
                                            <p id="fileTitle"><i class="fad fa-paperclip"></i>Прикрепить файл</p>
                                            <input type="file" name="file" id="file">
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary">Отправить<i class="fad fa-comment-alt ml-2"></i></button>
                                    </div>
                                </form>
                            </div>
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
    <script>
        jQuery(document).ready(() => {
            /* Функция обновления чата */
            function updateChat() {
                try {
                    /* Проверяем URL */
                    const urlParameters = new URLSearchParams(window.location.search);
                    /* Если в URL есть query string с такими данными */
                    if (urlParameters.has('chatID')) {
                        /* Показываем контейнер с сообщениями и убираем уведомление */
                        jQuery('.alert').addClass('d-none');
                        jQuery('.messages-container, #messageForm').removeClass('d-none');
                        /* Добавляем активный класс к диалогу в сайдбаре */
                        jQuery('.sidebar__chats a').removeClass('active');
                        jQuery(`#dialog-${ urlParameters.get('chatID') }`).addClass('active');
                        /* Декларируем переменную */
                        const formData = new FormData();

                        formData.append('chatID', urlParameters.get('chatID'));
                        /* AJAX запрос на обновление */
                        const response = clearApiRequest('getNewMessages', formData);

                        if (response.status === 200 && response.responseJSON.length > 0) {
                            /* Ощичаем чат если появились новые сообщения */
                            jQuery('.messages-container').html('');
                            
                            /* Проводим сообщения через цикл */
                            for(const message of response.responseJSON) {
                                /* Вводим островок в случае если это администратор, если нет то клиент */
                                if (message.senderManagerID) {
                                    /* Разметка островка отвечающая за администратора */
                                    jQuery('.messages-container').append(`
                                        <div class="text-right">
                                            <div id="profile__chat-${ message.id }" class="card bg-light d-inline-block profile__chat-admin">
                                                <div class="card-body p-2 px-3">
                                                    <b class="card-title text-black">${ message.adminInfo.name }</b>
                                                    <div class="card-file"></div>
                                                    <p class="card-text text-left">${ message.messageText }</p>
                                                    <small class="card-time">${ moment(message.created_at).format('HH:mm') } ${ moment(message.created_at).format('DD.MM.YYYY') }</small>
                                                </div>
                                            </div>
                                        </div>
                                    `);

                                    /* Если в данном сообщении есть файл, то выводит его */
                                    if (message.messageFileID !== null) {
                                        if (message.file.name.split('.').pop() === 'jpeg' || message.file.name.split('.').pop() === 'jpg' || message.file.name.split('.').pop() === 'png') {
                                            jQuery(`#profile__chat-${ message.id } .card-body`).before(`<a href="${ message.file.path }" download><img class="card-img-top" src="${ message.file.path }" alt="Card image cap"></a>`)
                                        } else {
                                            jQuery(`#profile__chat-${ message.id } .card-file`).append(`<a href="${ message.file.path }" class="btn btn-dark my-2" download>Скачать файл ${ message.file.name.split('.').pop() }</a>`)
                                        }
                                    }

                                    /* Если сообщение было прочитано, то добавляет галочку */
                                    if (message.isRead === 1) {
                                        jQuery(`#profile__chat-${ message.id } .card-time`).after(`<small class="chat__read ml-2" title="Ваше сообщение было прочитано"><i class="fad fa-check-double"></i></small>`)
                                    }

                                } else {

                                    /* Разметка островка отвечающая за клиента */
                                    jQuery('.messages-container').append(`
                                        <div class="text-left">
                                            <div id="profile__chat-${ message.id }" class="card bg-primary d-inline-block profile__chat-client">
                                                <div class="card-body p-2 px-3">
                                                    <b class="card-title">${ message.clientInfo.name }</b>
                                                    <div class="card-file"></div>
                                                    <p class="card-text">${ message.messageText }</p>
                                                    <small class="card-time">${ moment(message.created_at).format('HH:mm') } ${ moment(message.created_at).format('DD.MM.YYYY') }</small>
                                                </div>
                                            </div>
                                        </div>
                                    `);

                                    /* Если в данном сообщении есть файл, то выводит его */
                                    if (message.messageFileID !== null) {
                                        if (message.file.name.split('.').pop() === 'jpeg' || message.file.name.split('.').pop() === 'jpg' || message.file.name.split('.').pop() === 'png') {
                                            jQuery(`#profile__chat-${ message.id } .card-body`).before(`<a href="${ message.file.path }" download><img class="card-img-top" src="${ message.file.path }" alt="Card image cap"></a>`)
                                        } else {
                                            jQuery(`#profile__chat-${ message.id } .card-file`).append(`<a href="${ message.file.path }" class="btn btn-warning my-2" download>Скачать файл ${ message.file.name.split('.').pop() }</a>`)
                                        }
                                    }
                                }
                            }
                        }
                    }
                } catch (error) {
                    console.info(error);
                    showNotification('error', error);
                }
            }

            /* Функция обновления чатов в сайдбаре */
            function updateDialogs() {
                try {
                    const formData = new FormData();

                    /* AJAX запрос на обновление */
                    const response = clearApiRequest('getDialogs', formData);
                    /* Если AJAX запрос вернулся с данными то репопулируем сайдбар */
                    if (response.status === 200 && response.responseJSON) {
                        /* Ощичаем сайдбар если появились новые диалоги */
                        jQuery('.sidebar__chats').html('');

                        for(const dialog of response.responseJSON) {
                            /* Разметка диалога в сайдбаре */
                            jQuery('.sidebar__chats').append(`
                                <a href="#" dialog="${ dialog.id }" id="dialog-${ dialog.id }" class="list-group-item list-group-item-action flex-column align-items-start border-0">
                                    <div class="d-flex w-100 align-items-center justify-content-between">
                                    <b class="m-0 client-name"><p class="m-0">${ dialog.clientName }</p></b>
                                    <small>${ moment(dialog.updated_at).format('HH:mm') } ${ moment(dialog.updated_at).format('DD.MM.YYYY') }</small>
                                    </div>
                                </a>
                            `);
                            /* Если последнее сообщение не прочитано то добавляет иконку */
                            if (dialog.chatInfo.isRead === 0) {
                                jQuery(`#dialog-${ dialog.id } .client-name p`).before('<i class="fad fa-circle text-danger mr-2"></i>');
                            }
                        }
                    }

                } catch (error) {
                    console.info(error);
                    showNotification('error', error);
                }
            }

            /* Функция отправления сообщения */
            jQuery(document).on('submit', '#messageForm', function(e) {
                e.preventDefault();
                const 
                    formData = new FormData(this),
                    urlParameters = new URLSearchParams(window.location.search);
                // Заполняем formdata
                formData.append('chatID', urlParameters.get('chatID'));
                formData.append('clientID', urlParameters.get('clientID'));
                // Запрос на добавление сообщения
                const response = apiRequest('addMessage', formData);

                if (response.ok === true) {
                    this.reset();
                    updateChat();
                    /* Скроллим вниз после каждого обновления */
                    jQuery('.messages-container').animate({ scrollTop: $(document).height() }, 500);
                }
            });

            /* Функция открытия выбранного чата */
            jQuery(document).on('click', '.sidebar__chats a', function(e) {
                e.preventDefault();
                /* Создание параметров для строки */
                let query = {
                    chatID: this.getAttribute('dialog'),
                }
                /* Генерация URL строки и вставка их в route */
                const queryStr = serializeQueryString(query);
                history.pushState('empty', 'Title', `/control/chat?${ queryStr }`);
                /* Убираем значок непрочитанных сообщений в диалоге */
                jQuery(`#dialog-${ query.chatID }`).find('.fad.fa-circle').remove();
                // Обновляем чат с данным клиентом и все чаты в сайдбаре
                updateDialogs();
                updateChat();
            });

            /* Функция для обновления надписи инпута */
            jQuery(document).on('change', '#file', function(e) {
                (this.value.slice(12).length > 0) ? jQuery('#fileTitle').html('<i class="fad fa-file"></i>' + this.value.slice(12)) : jQuery('#fileTitle').html('<i class="fad fa-paperclip"></i>Прикрепить файл')
            });

            /* Интервал обновления чата 15 секунд */
            setInterval(updateDialogs, 15000);
            setInterval(updateChat, 15000);

            /* После загрузки страницы сразу обновляем чаты */
            updateDialogs();
            updateChat();
            /* Скроллим вниз после каждого обновления */
            jQuery('.messages-container').animate({ scrollTop: $(document).height() }, 500);
        });
    </script>
@endsection
