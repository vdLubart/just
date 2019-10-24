<?php

return [
    'form' => [
        'type' => 'Тип',
        'width' => 'Ширина блоку',
        'layoutClass' => 'Клас макету',
        'cssClass' => 'Додатковий CSS клас'
    ],
    'content' => 'Вміст',
    'properties' => 'Властивості',
    'preferences' => [
        'title' => 'Налаштування',
        'settingsView' => [
            'title' => 'Зовнішній вигляд',
            'scale' => 'Масштаб',
            'scaleOption' => '{1} :width - :items елемент у ряді|[2,4] :width - :items елементи у ряді|[5,*] :width - :items елементів у ряді'
        ],
        'cropGroup' => [
            'title' => 'Обрізка зображення',
            'cropDimensions' => 'Обрізати зображення за розміром (Ш:В)'
        ],
        'fieldsGroup' => [
            'title' => 'Поля елемента',
            'ignoreCaption' => 'Ігнорувати полу заголовоку',
            'ignoreDescription' => 'Ігнорувати поле опису'
        ],
        'sizeGroup' => [
            'title' => 'Розмір зображень',
            'customSizes' => 'Обрати розмір зображень',
            'size' => '{1} :width ширини макету (:cols колонка)|[2,4] :width ширини макету (:cols колонки)|[5,*] :width ширини макету (:cols колонок)'
        ],
        'itemRoute' => [
            'title' => 'Шлях до елемента',
            'base' => 'Базова частина шляху до елемента'
        ],
        'orderDirection' => [
            'title' => 'Сортування',
            'asc' => "Новий елемент з'являється у кінці",
            'desc' => "Новий елемент з'являється на початку"
        ]
    ],
    'create' => 'Створити новий елемент',
    'edit' => 'Редагувати елемент',
    'registrations' => 'Реєстрації',
    'relatedBlock' => [
        'title' => "Пов'язані блоки",
        'edit' => "Редагувати пов'язаний блок",
        'create' => "Створити пов'язаний блок"
    ],
    'untitled' => 'Без заголовку',
    'list' => [
        'articles' => [
            'title' => 'Статті',
            'description' => 'Блоґ або новинна стрічка з набором статей'
        ],
        'contact' => [
            'title' => 'Контакти',
            'description' => 'Додає контакту інформацію'
        ],
        'events' => [
            'title' => 'Події',
            'description' => 'Додає на сторінку блок з подіями'
        ],
        'features' => [
            'title' => 'Ознака',
            'description' => 'Додає піктограму з коротким підписом'
        ],
        'feedback' => [
            'title' => 'Відгуки',
            'description' => 'Додає блок відгуків'
        ],
        'gallery' => [
            'title' => 'Галерея',
            'description' => 'Додає галерею зображень на сторінку'
        ],
        'html' => [
            'title' => 'Блок HTML',
            'description' => 'Вставляє HTML код на сторінці'
        ],
        'langs' => [
            'title' => 'Вибір мов',
            'description' => 'Додає інструмент локалізації'
        ],
        'link' => [
            'title' => 'Відображення',
            'description' => 'Відображає дані іншого блоку'
        ],
        'logo' => [
            'title' => 'Логотип',
            'description' => 'Додає логотип на сторінку'
        ],
        'menu' => [
            'title' => 'Меню',
            'description' => 'Конструктор меню'
        ],
        'slider' => [
            'title' => 'Слайдер',
            'description' => 'Додає слайдер зображень з підписами чи без них'
        ],
        'space' => [
            'title' => 'Відступ',
            'description' => 'Створює відступ зазначеного розміру'
        ],
        'text' => [
            'title' => 'Текст',
            'description' => 'Додає форматований текст із заголовком'
        ],
        'twitter' => [
            'title' => 'Twitter',
            'description' => 'Відобрадає додаток Twitter'
        ]
    ]
];