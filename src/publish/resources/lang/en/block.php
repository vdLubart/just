<?php

return [
    'form' => [
        'type' => 'Type',
        'width' => 'Block Width',
        'layoutClass' => 'Layout Class',
        'cssClass' => 'Additional CSS Classes'
    ],
    'content' => 'Content',
    'properties' => 'Properties',
    'preferences' => [
        'title' => 'Preferences',
        'settingsView' => [
            'title' => 'Settings View',
            'scale' => 'Settings View Scale',
            'scaleOption' => '{1} :width - :items Item In A Row|[2,*] :width - :items Items In A Row'
        ],
        'cropGroup' => [
            'title' => 'Image Cropping',
            'cropDimensions' => 'Crop Image With Dimensions (W:H)'
        ],
        'fieldsGroup' => [
            'title' => 'Item Fields',
            'ignoreCaption' => 'Ignore Caption Field',
            'ignoreDescription' => 'Ignore Description Field'
        ],
        'sizeGroup' => [
            'title' => 'Resize Images',
            'customSizes' => 'Choose Custom Size Set',
            'size' => '{1} Resize To :width Of Layout Width (:cols Column)|[2,*] Resize To :width Of Layout Width (:cols Columns)'
        ],
        'itemRoute' => [
            'title' => 'Item Route',
            'base' => 'Item Route Base'
        ],
        'orderDirection' => [
            'title' => 'Sorting',
            'asc' => 'New item appears in the end',
            'desc' => 'New item appears on the top'
        ]
    ],
    'create' => 'Create New Item',
    'edit' => 'Edit Item',
    'registrations' => 'Registrations',
    'relatedBlock' => [
        'title' => 'Related Blocks',
        'edit' => 'Edit Related Block',
        'create' => 'Create Related Block'
    ],
    'untitled' => 'Untitled'
];