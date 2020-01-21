<?php

return [
    'title' => 'Додатки',
    'createForm' => [
        'title' => 'Додати додаток',
        'addOn' => 'Додаток',
        'block' => 'Блок',
        'name' => 'Назва (dev змінна, [a-z])',
        'title' => 'Користувацький заголовок додатка'
    ],
    'list' => 'Список додатків',
    'addOns' => [
        'categories' => [
            'title' => 'Категорії',
            'description' => 'Допомагає категоризувати блок'
        ],
        'images' => [
            'title' => 'Додаткове зображення',
            'description' => 'Додає зображення до елемента'
        ],
        'paragraphs' => [
            'title' => 'Додатковий текст',
            'description' => 'Додає текстове поле до елемента'
        ],
        'strings' => [
            'title' => 'Текстове значення',
            'description' => 'Додає рядок до елемента'
        ]
    ],
    'addOnLocation' => ':addOn у блоці :block на сторінці :page',
    'category' => [
        'title' => 'Категорії',
        'createForm' => [
            'title' => 'Створити нову категорію',
            'addOnGroup' => 'Оберіть додаток',
            'addOn' => 'Додаток категорій',
            'pairGroup' => 'Пара категорії',
            'caption' => 'Видимий заголовок',
            'value' => 'HTML значення'
        ],
        'list' => 'Список категорій',
        'emptyList' => 'Ще не створено жодної категорії',
        'listItem' => ':title у блоці :block'
    ],
];