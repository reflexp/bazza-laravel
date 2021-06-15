<?php

return [
    'order' => [
        1 => [ 'color'      => 'primary',    'text'     => 'Создан' ],
        2 => [ 'color'      => 'info',       'text'     => 'Принят в работу' ],
        3 => [ 'color'      => 'info',       'text'     => 'Подтверждён' ],
        4 => [ 'color'      => 'warning',    'text'     => 'Ожидает оплату', 'shortText' => 'Ожд. оплату' ],
        5 => [ 'color'      => 'warning',    'text'     => 'Ожидает подтверждения Клиентом', 'shortText' => 'Ожд. пдтвржд. Кл.' ],
        6 => [ 'color'      => 'info',       'text'     => 'Доставка' ],
        7 => [ 'color'      => 'info',       'text'     => 'Принят на складе выдачи', 'shortText' => 'Прнт. на Скл. Выд.' ],
        8 => [ 'color'      => 'secondary',  'text'     => 'Ожидает выдачи' ],
        9 => [ 'color'      => 'success',    'text'     => 'Выдан' ],
        10 => [ 'color'     => 'warning',    'text'     => 'Выдан частично' ],
        11 => [ 'color'     => 'danger',     'text'     => 'Отменён' ],
        12 => [ 'color'     => 'danger',     'text'     => 'Возвращён' ],
        13 => [ 'color'     => 'info',       'text'     => 'Подтверждён клиентом' ],

    ],
    'product' => [
        1 => ['color' => 'info',         'text' => 'Обработка'],
        2 => ['color' => 'info',         'text' => 'Доставка'],
        3 => ['color' => 'secondary',    'text' => 'Готов к выдаче'],
        4 => ['color' => 'success',      'text' => 'Выдан'],
        5 => ['color' => 'danger',       'text' => 'Нет на складе'],
        6 => ['color' => 'danger',       'text' => 'Возврат'],
    ],
    'bundle' => [
        1 => ['color' => 'info',         'text' => 'Собран'],
        2 => ['color' => 'secondary',    'text' => 'Разобран'],
        3 => ['color' => 'success',      'text' => 'В работе'],
    ],
];
