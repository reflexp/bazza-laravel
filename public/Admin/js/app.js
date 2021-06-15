setInterval(refreshToken, 1800000); // 1 hour

function apiRequest(method, dataSend = null) {
    let result = jQuery.ajax({
        url         : '/control/ajax/' + method,
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
        url         : `/control/ajax/${method}`,
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
        showDuration    : '15000',
        hideDuration    : '10000',
        timeOut         : '5000',
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
