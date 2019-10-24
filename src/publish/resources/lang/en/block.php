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
    'untitled' => 'Untitled',
    'list' => [
        'articles' => [
            'title' => 'Articles',
            'description' => 'Blog or newsline with set of articles'
        ],
        'contact' => [
            'title' => 'Contact',
            'description' => 'Shows contact information'
        ],
        'events' => [
            'title' => 'Events',
            'description' => 'Adds event block to the page'
        ],
        'features' => [
            'title' => 'Feature',
            'description' => 'Adds short feature description with icon'
        ],
        'feedback' => [
            'title' => 'Feedback',
            'description' => 'Adds feedback form to the website'
        ],
        'gallery' => [
            'title' => 'Photo Gallery',
            'description' => 'Shows photo gallery on the website'
        ],
        'html' => [
            'title' => 'HTML Block',
            'description' => 'Adds HTML piece of code'
        ],
        'langs' => [
            'title' => 'Languages',
            'description' => 'Makes localizations available'
        ],
        'link' => [
            'title' => 'Link',
            'description' => 'Shows data from other blocks'
        ],
        'logo' => [
            'title' => 'Logo',
            'description' => 'Website logo'
        ],
        'menu' => [
            'title' => 'Menu',
            'description' => 'Menu builder'
        ],
        'slider' => [
            'title' => 'Slider',
            'description' => 'Adds image slider with or without text descriptions'
        ],
        'space' => [
            'title' => 'Empty Space',
            'description' => 'Adds empty space with fixed height'
        ],
        'text' => [
            'title' => 'Text',
            'description' => 'Adds well-formatted text with the caption to the website'
        ],
        'twitter' => [
            'title' => 'Twitter',
            'description' => 'Adds Twitter block to the website'
        ]
    ]
];