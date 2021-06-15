@extends('Client.layouts.app')

@section('title', 'Каталог')

@section('styles')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
@endsection

@section('content')
<main class="main-container">
    <div class="main__left-column">
      <!-- Navbar -->
      @include('Client.layouts.navbar')

      <!-- Main content -->
      <div class="wrapper">
        <section class="nomenclature-container">
            <div class="nomenclature__header">
              <h1>Передать поиск специалисту</h1>
            </div>
            
            <div class="nomenclature__form">
                <form id="searchForm" method="POST">
                    <input type="text" name="name" placeholder="Поиск по названию автозапчасти">
                    <input type="text" name="article" placeholder="Поиск по артикулу">
                    <select name="model">
                        <option>Выберите модель авто</option>
                        <option value="ВАЗ">ВАЗ</option>
                        <option value="УАЗ">УАЗ</option>
                        <option value="ГАЗ">ГАЗ</option>
                        <option value="ПАЗ">ПАЗ</option>
                        <option value="Тракторы">Тракторы</option>
                    </select>
                    <button id="searchBtn" class="btn" type="button">Найти автозапчасть</button>
                </form>
            </div>

            <div class="nomenclature__table">
              <table id="datatable" class="datatable-custom">
                <thead>
                    <tr>
                        <th>Артикул</th>
                        <th>Наименование</th>
                        <th>Свойства</th>
                        <th>Наличие</th>
                        <th>Цена</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>Артикул</th>
                        <th>Наименование</th>
                        <th>Свойства</th>
                        <th>Наличие</th>
                        <th>Цена</th>
                        <th>Действия</th>
                    </tr>
                </tfoot>
              </table>
            </div>
        </section>

        <section class="modal-background-container">
            <section class="modal-container modal-large description">
                <div class="modal__close"><i class="fas fa-times"></i></div>

                <div class="modal__header">
                    <h1 class="modal__title">Имя</h1>
                    <small class="modal__subtitle">Артикул: <span>-</span></small>
                </div>

                <div class="modal__content">
                    <ul>
                        <li>Производитель: <span class="desc-manufacturer"></span></li>
                        <li>Количество на складе: <span class="desc-amount"></span></li>
                        <li>Цена товара: <span class="desc-price"></span></li>
                        <li>Дополнительный текст: <span class="desc-text"></span></li>
                    </ul>
                </div>
            </section>

            <section class="modal-container modal-large amount">
                <div class="modal__close"><i class="fas fa-times"></i></div>

                <div class="modal__header">
                    <h1 class="modal__title">Добавление в корзину</h1>
                </div>

                <div class="modal__content">
                    <p>Пожалуйста, укажите какое количество товара вам необходимо.</p>

                    <input type="number" name="amount" min="1" value="1" minlength="1" placeholder="Например: 400">
                </div>

                <div class="modal__buttons">
                    <button class="btn modal__close-btn">Закрыть</button>
                    <button class="btn modal__confirm-btn">Добавить в корзину</button>
                </div>
            </section>
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
    <!-- DataTables  & Plugins -->
    <script src="{{ asset('/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    {{-- Инициализирование Datatable --}}
    <script>
        const url = new URL(window.location.href);
        const datatable = $("#datatable").DataTable({
            "dom": 'lfrtip',
            // Blfrtip
            "ajax": {
                "type"   : "POST",
                "url"    : "{{ url('/ajax/getNomenclatures') }}",
                "dataSrc": "data",
            },
            "columns": [
                { "data": "article" },
                { 
                    "data": "title",
                    render:function(data)
                    {
                        return `<span class="title-wrapper"><button class="btn btn-read-description"><i class="fad fa-info-circle"></i></button>${ data }</span>`
                    }
                },
                { 
                    "data": "properties",
                    "orderable": false,
                    "searchable": false,
                    render:function(data)
                    {
                        return `Пусто`
                    }
                },
                {
                    "data": null,
                    "orderable": false,
                    "searchable": false,
                    render:function(data)
                    {
                        if(data['amount'] > 0)
                            return '<span>Есть</span>';
                        else
                            return '<span>На заказ</span>'
                    },
                },
                { "data": "price" },
                {   
                    "orderable": false,
                    "searchable": false,
                    "data": null,
                    "className": "action-wrapper",
                    render:function(data)
                    {
                        return `<button class="btn btn-add-to-cart"><i class="fad fa-shopping-cart mr-2"></i>Добавить в корзину</button>`;
                    }, 
                    "targets": -1
                },
            ],
            "lengthChange": true,
            "autoWidth": false,
            "paging": true,
            "ordering": true,
            "info": true,
            "searching": false,
            "processing": true,
            "serverSide": true,
            "lengthChange": false,
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
        $(document).ready(function() {
            // datatable.on('preXhr', function(e, settings, data) {
            //     const urlData = {};
            //     const urlParameters = new URLSearchParams(window.location.search);
            //     const elements = document.querySelector('#searchForm').elements;
            //     const dataTablesData = datatable.ajax.params();
            //     let query;

            //     // Перемещаем параметры из URL строки в пустой объект
            //     urlParameters.forEach((value, key) => urlData[key] = value);

            //     if (url.pathname === '/nomenclature/search' && dataTablesData.draw === 2) {
            //         // Repopulate search form
            //         jQuery('#searchForm').find('input[name="name"]').val(urlData.name);
            //         jQuery('#searchForm').find('input[name="article"]').val(urlData.article);
            //         jQuery('#searchForm').find(`option[value="${urlData.model}"]`).prop('selected', true);
            //         // Добавляем в FormData параметры из строки
            //         data['methodType'] = 'URL';
            //         Object.keys(urlData).map((key) => data[key] = urlData[key]);
            //         // Параметры для создания URL строки
            //         query = {
            //             model: urlData.model,
            //             article: urlData.article,
            //             name: urlData.name,
            //             start: urlData.start,
            //             length: urlData.length,
            //             elemCount: urlData.elemCount,
            //             sortField: urlData.sortField,
            //             sortType: urlData.sortType,
            //         };
            //     } else {
            //         // Добавляем в FormData параметры из строки
            //         data['methodType'] = 'SEARCH';
            //         data['article'] = jQuery('#searchForm').find('input[name="article"]').val();
            //         data['model'] = jQuery('#searchForm').find('select[name="model"]').val();
            //         data['name'] = jQuery('#searchForm').find('input[name="name"]').val();
            //         // Параметры для создания URL строки
            //         query = {
            //             model: elements.model.value,
            //             article: elements.article.value,
            //             name: elements.name.value,
            //             start: dataTablesData.start,
            //             length: dataTablesData.length,
            //             elemCount: dataTablesData.length,
            //             sortField: dataTablesData.order[0]['column'],
            //             sortType: dataTablesData.order[0]['dir'],
            //         };
            //     }
            //     // Генерация URL строки и вставка их в route
            //     const queryStr = serializeQueryString(query);
            //     history.pushState('empty', 'Title', `/nomenclature/search?${queryStr}`);
            // });
            // // Если url будет настроен поиск, то обновит таблицу с данными из url строки
            // if (url.pathname === '/nomenclature/search') {
            //     setTimeout(() => {
            //         updateTable(datatable);
            //     }, 500);
            // }

            datatable.on('preXhr', function(e, settings, data) {
                
                const 
                searchForm = document.querySelector('#searchForm').elements, 
                dataTablesData = datatable.ajax.params();

                // Создание параметров для строки на основе данных поступающих от таблицы
                let query = {
                    search: 1,
                    model: searchForm.model.value,
                    article: searchForm.article.value,
                    name: searchForm.name.value,
                    start: dataTablesData.start,
                    length: dataTablesData.length,
                    elemCount: dataTablesData.length,
                    sortField: dataTablesData.order[0]['column'],
                    sortType: dataTablesData.order[0]['dir'],
                };
                
                // Игнорируем обновление URL при первых отрисовках таблицы, для поиска по данным URL до их обновления текущими данными из таблицы
                if (dataTablesData.draw > 2) {
                    // Генерация URL строки и вставка их в route
                    const queryStr = serializeQueryString(query);
                    history.pushState('empty', 'Title', `/nomenclature?${queryStr}`); 
                }

                // Берем обновленные данные с URL
                const urlParameters = new URLSearchParams(window.location.search);

                // Перемещаем параметры из URL строки в FormData
                urlParameters.forEach((value, key) => data[key] = value);

                for(const key of Object.keys(query)) {
                    // Проверка на соответствие правильности написание ключей
                    if (!urlParameters.has(key)) data['isQueryKeysCorrect'] = false;
                }

                for(const urlKey of urlParameters.keys()) {
                    // Проверка на соответствие правильности значения в ключе сортировки
                    if (urlKey === 'sortType' && !['asc', 'desc'].includes(urlParameters.get(urlKey))) data['isQueryValuesCorrect'] = false;
                    
                    // Проверка на соответствие правильности значений во всей строке, кроме model, article, name
                    if (urlKey === 'start' && urlParameters.get(urlKey).length === 0 || urlKey === 'length' && urlParameters.get(urlKey).length === 0 || urlKey === 'elemCount' && urlParameters.get(urlKey).length === 0 || urlKey === 'sortField' && urlParameters.get(urlKey).length > 4 && urlParameters.get(urlKey).length < 0) data['isQueryValuesCorrect'] = false;
                }

                // Отображение активной страницы в зависимости от элементов и обновление данных в форме
                if (url.search.length > 0) {
                    jQuery('#searchForm').find('input[name="name"]').val(data.name);
                    jQuery('#searchForm').find('input[name="article"]').val(data.article);
                    jQuery('#searchForm').find(`option[value="${ data.model }"]`).prop('selected', true);
                    datatable.page(urlParameters.get('start') / urlParameters.get('length'));
                }
            });

            if (url.search.length > 0) {
                setTimeout(() => {
                    updateTable(datatable);
                }, 500);
            }

            // По нажатии кнопки поиск обновляет таблицу
            jQuery(document).on('click', '#searchBtn', function() {
                updateTable(datatable);
            });

            // Функция открытия модалки с описанием товара
            jQuery(document).on('click', '.btn-read-description', function(e) {
                const rowData = datatable.row($(this).parents('tr')).data();
                // Появление модалок
                openModal('.modal-container.description');
                // Репопуляция модалки
                jQuery('.modal-container.description').find('.modal__title').html(rowData['title']);
                jQuery('.modal-container.description').find('.modal__subtitle span').html(rowData['article']);
                jQuery('.modal-container.description').find('.desc-manufacturer').html(rowData['manufacturer']);
                jQuery('.modal-container.description').find('.desc-amount').html(rowData['amount']);
                jQuery('.modal-container.description').find('.desc-price').html(rowData['price']);
                jQuery('.modal-container.description').find('.desc-text').html(rowData['additionText']);
            });

            // Функция добавление товара в корзину
            function addToCart(data) {
                const formData = new FormData();
                const amount = jQuery('.modal-container.amount').find('input[name="amount"]').val();
                // Добавляем в formdata данные из строки в номенклатуре
                Object.keys(data).map((current, index) => formData.append(current, data[current]));
                // Перезаписываем amount в то количество, которое выбрал пользователь
                formData.append('amount', parseInt(amount));
                const response = apiRequest('addToCart', formData);
                
                if (response.ok === true) {
                    // Функция подсчета предметов в корзине и обновления кнопки
                    countCartItems();
                    jQuery('#modalNotyfContainer .btn-confirm').off('click');
                    closeModal();
                }
            }

            // Функция открытия модалки с подтвреждением о количестве
            jQuery(document).on('click', '.btn-add-to-cart', function() {
                const rowData = datatable.row($(this).parents('tr')).data();
                // Появление модалок
                openModal('.modal-container.amount');
                // Сброс количества товара на 1
                jQuery('.modal-container.amount').find('input[name="amount"]').val(1);
                // Навешивание event на подтверждение
	            jQuery('.modal-container.amount').find('.modal__confirm-btn').on('click', () => {
                    addToCart(rowData);
                })
            });

            // Служит для удаления ивента, при закрытии модалки
            jQuery(document).on('modalClosed', function() {
                jQuery('.modal-container.amount').find('.modal__confirm-btn').off('click');
            })
        });
    </script>
@endsection




