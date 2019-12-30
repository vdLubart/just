<?php

return [
    'title' => 'Додатки',
    'createForm' => [
        'title' => 'Додати додаток',
        'addon' => 'Додаток',
        'block' => 'Блок'
    ],
    'listTitle' => 'Список додатків',
    'list' => [
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
    'addonLocation' => ':addon у блоці :block на сторінці :page',
    'category' => [
        'title' => 'Категорії',
        'createForm' => [
            'title' => 'Створити нову категорію',
            'addon' => 'Додаток',
            'value' => 'Значення'
        ],
        'list' => 'Список категорій',
        'emptyList' => 'Ще не створено жодної категорії',
        'listItem' => ':title у блоці :block'
    ],
];