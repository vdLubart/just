<?php

return [
    'title' => 'Add-Ons',
    'createForm' => [
        'title' => 'Add Add-On',
        'addOn' => 'Add-On',
        'block' => 'Block',
        'name' => 'Name (dev variable, ^[a-z]+[a-zA-Z0-9]*$)',
        'userTitle' => 'User\'s Add-On Title',
        'isRequired' => 'Make Add-On Field Required'
    ],
    'editForm' => [
        'title' => "Add-On ':addOn'"
    ],
    'list' => 'Add-On list',
    'addOns' => [
        'category' => [
            'title' => 'Category',
            'description' => 'Helps categorize block (single choice)'
        ],
        'image' => [
            'title' => 'Additional Image',
            'description' => 'Adds an image to the item'
        ],
        'paragraph' => [
            'title' => 'Additional Text',
            'description' => 'Adds an article to the item'
        ],
        'phrase' => [
            'title' => 'String Value',
            'description' => 'Adds a single line string to the item'
        ]
    ],
    'addOnLocation' => ':addOn in the ":block" block at :page page',
    'messages' => [
        'success' => [
            'created' => 'Add-on was created successfully',
            'updated' => 'Add-on was updated successfully',
            'activated' => 'Add-on was activated successfully',
            'deactivated' => 'Add-on was deactivated successfully',
            'deleted' => 'Add-on was deleted successfully'
        ]
    ]
];
