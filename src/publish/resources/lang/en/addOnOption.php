<?php

return [
    'title' => 'Add-Ons',
    'category' => [
        'title' => 'Category Options',
        'createForm' => [
            'title' => 'Create New Category Option',
            'addOns' => 'Category Add-Ons',
            'option' => 'Option Title'
        ],
        'editForm' => [
            'title' => 'Edit Category Option',
        ],
        'list' => 'Category List',
        'emptyList' => 'No category is created yet',
        'listItem' => ':title on :block'
    ],
    'messages' => [
        'success' => [
            'created' => 'Option was created successfully',
            'updated' => 'Option was updated successfully',
            'activated' => 'Option was activated successfully',
            'deactivated' => 'Option was deactivated successfully',
            'moved' => 'Option was moved successfully',
            'deleted' => 'Option was deleted successfully'
        ]
    ]
];
