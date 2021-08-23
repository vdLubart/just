<?php

return [
    'title' => 'Add-On Options',
    'createForm' => [
        'title' => 'Create New Option',
        'addOn' => 'Choose Add-On',
        'option' => 'Option Title'
    ],
    'editForm' => [
        'title' => 'Edit Option',
    ],
    'category' => [
        'list' => 'Category List',
        'emptyList' => 'No category is created yet'
    ],
    'tag' => [
        'list' => 'Tag List',
        'emptyList' => 'No tag is created yet',
    ],
    'listItem' => ':title in :block',
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
