<?php

return [
    'title' => 'Add-Ons',
    'createForm' => [
        'title' => 'Add Add-On',
        'addOn' => 'Add-On',
        'block' => 'Block',
        'name' => 'Name (dev variable, [a-z])',
        'title' => 'User\'s Add-On Title'
    ],
    'list' => 'Add-On list',
    'addOns' => [
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
    'addOnLocation' => ':addOn in :block block at :page page',
    'category' => [
        'title' => 'Categories',
        'createForm' => [
            'title' => 'Create New Category',
            'addOnGroup' => 'Choose Add-On',
            'addOn' => 'Categories Add-On',
            'pairGroup' => 'Category Pair',
            'caption' => 'Visible Caption',
            'value' => 'HTML Value'
        ],
        'list' => 'Category List',
        'emptyList' => 'No category is created yet',
        'listItem' => ':title on :block'
    ],
];