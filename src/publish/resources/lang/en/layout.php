<?php

return [
    'title' => 'Layouts',
    'createForm' => [
        'title' => 'Create New Layout',
        'themeTitle' => 'Theme Title',
        'themeClass' => 'Theme Class',
        'width' => 'Layout Width',
        'panel' => 'Panel',
        'panelType' => 'Panel Type',
        'addPanel' => 'Add Panel',
        'removePanel' => 'Remove Panel'
    ],
    'setDefaultForm' => [
        'title' => 'Set Default',
        'defaultLayout' => 'Default Layout',
        'forAllPages' => 'Set This Layout For All Pages'
    ],
    'list' => 'Layout List',
    'editForm' => [
        'title' => 'Edit Layout ":layout"'
    ],
    'messages' => [
        'success' => [
            'created' => 'Layout was created successfully',
            'updated' => 'Layout was updated successfully',
            'deleted' => 'Layout was deleted successfully'
        ],

        'error' => [
            'classInUse' => 'Class ":class" is already used in ":layout" layout',
            'protectedLayout' => 'This layout is default and cannot be changed',
            'usedOnPage' => 'Layout cannot be deleted because page ":page" is using it'
        ]
    ]
];