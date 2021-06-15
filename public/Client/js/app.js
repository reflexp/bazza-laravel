function apiRequest(method, dataSend = null) {
    let result = jQuery.ajax({
        url         : '/ajax/' + method,
        type        : 'POST',
        data        : dataSend,
        dataType    : 'json',
        async       : false,
        cache       : false,
        processData : false,
        contentType : false,
        success     : function(result) {
            return result;
        }
    });

    if (result.status == 500) {
        showNotification('error', '500: Ошибка сервера!');
        return [];
    } else if (result.status == 422) {
        let errorsArr = [];
        jQuery.each(result.responseJSON['errors'], function(i, elem) {
            showNotification('error', elem);
        });

        return [];
    } else if (result.status == 419) {
        showNotification('error', '419: Обновите страницу и выполните заново');
        // toastr.error('413: Размер файла слишком большой!');
        return [];
    } else if (result.status == 413) {
        showNotification('error', result.responseJSON['result']['message']);
        // toastr.error('413: Размер файла слишком большой!');
        return [];
    } else if (result.status == 200) {
        if (result.responseJSON['ok'] === true) {
            showNotification('success');
        } else if (result.responseJSON['ok'] === false) {
            showNotification('error', result.responseJSON['result']['message']);
        }
        return result.responseJSON;
    } else {
        toastr.error(result.status + ': HTTP ошибка.');
        // return result.responseJSON;
    }
}

function clearApiRequest(method, dataSend = null) {
    return jQuery.ajax({
        url         : `/ajax/${method}`,
        method      : 'POST',
        dataType    : 'json',
        data        : dataSend,
        async       : false,
        cache       : false,
        processData : false,
        contentType : false,
        success     : function(response) {
            return response;
        }
    });
}

// В каждый AJAX запрос будет отправлен в заголовках CSRF token
$.ajaxSetup({
    headers : {
        'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
    }
});

// Обновление CSRF-токена
function refreshToken() {
    $.get('/refreshToken').done(function(data) {
        jQuery('meta[name="csrf-token"]').attr('content', data); // the new token
        $.ajaxSetup({
            headers : {
                'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
            }
        });
    });
}

/*
InputMask на телефонные номера
*/
jQuery('input[type="tel"]').inputmask({ mask: '+7 999 999 9999', clearIncomplete: false });

/*
Обновление данных таблицы
*/
function updateTable(datatable) {
    datatable.ajax.reload();
}

/*
Toastr вызов уведомления
*/

function showNotification(type, msg = null) {
    let text;
    switch (type) {
        case 'success':
            text = 'Успешно выполнено';
            break;
        case 'error':
            text = 'Ошибка';
            break;
        case 'warning':
            text = 'Внимание';
            break;
        case 'info':
            text = 'Информация';
            break;

        default:
            type = 'warning';
            text = 'Неправильные настройки уведомлений';
            break;
    }

    if (msg != null) text = msg;

    toastr.options = {
        progressBar    : true,
        showDuration    : '20000',
        hideDuration    : '20000',
        timeOut         : '20000',
        extendedTimeOut : '20000'
    };

    toastr[type](text);
}

/*
  Конвертация объекта в query строку
*/
serializeQueryString = function(obj) {
    let str = [];
    for (var p in obj)
        if (obj.hasOwnProperty(p)) {
            str.push(encodeURIComponent(p) + '=' + encodeURIComponent(obj[p]));
        }
    return str.join('&');
};
/*
  Функция подсчета предметов в корзине и обновления кнопки
*/
function countCartItems() {
    jQuery.ajax({
        url         : '/ajax/getCartCount',
        method      : 'POST',
        dataType    : 'json',
        async       : false,
        cache       : false,
        processData : false,
        contentType : false,
        success     : function(response) {
            if (response > 99) {
                jQuery('.shopcart-btn').html(`<i class="fad fa-shopping-cart"></i>Корзина <span class="badge-cart">99+</span>`);
            } else if (response) {
                jQuery('.shopcart-btn').html(
                    `<i class="fad fa-shopping-cart"></i>Корзина <span class="badge-cart">${response}</span>`
                );
            } else if (response === 0) {
                jQuery('.shopcart-btn').html(`<i class="fad fa-shopping-cart"></i>Корзина</span>`);
            }
        }
    });
}
countCartItems();

/*
  Функция открытия и закрытия сайдбара
*/
const menuBtn = document.querySelector('.navbar__menu');
menuBtn.addEventListener('click', () => {
    toggleSidebar();
});

window.addEventListener('resize', (e) => {
    if (window.innerWidth >= 768) {
        toggleSidebar(true);
    }
});

function toggleSidebar(isDesktop) {
    const body = document.querySelector('body');
    const rightColumn = document.querySelector('.main__right-column');

    if (isDesktop) {
        body.classList.remove('active-sidebar');
        rightColumn.classList.remove('active');
    } else {
        body.classList.toggle('active-sidebar');
        rightColumn.classList.toggle('active');
    }
}

function closeModal() {
    jQuery('.modal-background-container').fadeOut(200);
    jQuery('.modal-container').fadeOut(200);
    jQuery.event.trigger({
        type : 'modalClosed'
    });
}

function openModal(modal) {
    jQuery('.modal-background-container').css('display', 'flex').hide().fadeIn(200);
    jQuery(modal).css('display', 'block').hide().fadeIn(200);
}

jQuery(document).on('click', '.modal-container .modal__close', closeModal);

jQuery(document).on('click', '.modal-container .modal__close-btn', closeModal);
