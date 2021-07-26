<?php

return [
    'title' => 'Додатки',
    'createForm' => [
        'title' => 'Додати додаток',
        'addOn' => 'Додаток',
        'block' => 'Блок',
        'name' => 'Назва (dev змінна, ^[a-z]+[a-zA-Z0-9]*$)',
        'userTitle' => 'Користувацький заголовок додатка'
    ],
    'editForm' => [
        'title' => "Додаток ':addOn'"
    ],
    'list' => 'Список додатків',
    'addOns' => [
        'categories' => [
            'title' => 'Категорії',
            'description' => 'Допомагає категоризувати блок'
        ],
        'image' => [
            'title' => 'Додаткове зображення',
            'description' => 'Додає зображення до елемента'
        ],
        'paragraph' => [
            'title' => 'Додатковий текст',
            'description' => 'Додає текстове поле до елемента'
        ],
        'phrase' => [
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
