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
        'category' => [
            'title' => 'Категорія',
            'description' => 'Допомагає категоризувати блок (одиничний вибір)'
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
            'description' => 'Додає однорядкову фразу до елемента'
        ]
    ],
    'addOnLocation' => ':addOn у блоці :block на сторінці :page',
    'messages' => [
        'success' => [
            'created' => 'Додаток успішно створено',
            'updated' => 'Додаток успішно оновлено',
            'activated' => 'Додаток успішно активовано',
            'deactivated' => 'Додаток успішно деактивовано',
            'deleted' => 'Додоток успішно видалено'
        ]
    ]
];
