<?php

return [
    'title' => 'Add-ons',
    'createForm' => [
        'title' => 'Add Add-on',
        'addon' => 'Add-on',
        'block' => 'Block'
    ],
    'listTitle' => 'Add-on list',
    'list' => [
        'categories' => [
            'title' => 'Categories',
            'description' => 'Helps categorize block'
        ],
        'images' => [
            'title' => 'Additional Image',
            'description' => 'Adds an image to the item'
        ],
        'paragraphs' => [
            'title' => 'Additional Text',
            'description' => 'Adds an article to the item'
        ],
        'strings' => [
            'title' => 'String Value',
            'description' => 'Adds a string to the item'
        ]
    ],
    'addonLocation' => ':addon in :block block at :page page',
    'category' => [
        'title' => 'Categories',
        'createForm' => [
            'title' => 'Create New Category',
            'addon' => 'Addon',
            'value' => 'Value'
        ],
        'list' => 'Category List',
        'emptyList' => 'No category is created yet',
        'listItem' => ':title on :block'
    ],
];