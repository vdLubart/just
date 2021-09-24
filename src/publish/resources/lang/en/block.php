<?php

return [
    'create' => 'Create Block',
    'list' => 'Block List',
    'createForm' => [
        'title' => 'Create New Block',
        'blockData' => 'Block Data',
        'type' => 'Type',
        'width' => 'Block Width',
        'blockView' => 'Block View',
        'layoutClass' => 'Layout Class',
        'cssClass' => 'Additional CSS Classes'
    ],
    'editForm' => [
        'title' => "Block ':title'",
        'item' => 'Edit Item'
    ],
    'tabs' => [
        'content' => 'Block Content',
        'blockSettings' => 'Block Settings',
        'blockCustomization' => 'Block Customization',
        'createItem' => 'Create New Item'
    ],
    'itemTabs' => [
        'edit' => 'Edit Item',
        'createRelation' => 'Create Related Block',
        'relations' => 'Related Block List'
    ],
    'properties' => 'Properties',
    'customizations' => [
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
    'createItem' => 'Create New Item',
    'editItem' => 'Edit Item',
    'registrations' => 'Registrations',
    'untitled' => 'Untitled',
    'type' => [
        'articles' => [
            'title' => 'Articles',
            'description' => 'Blog or news line with set of articles'
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
    ],
    'messages' => [
        'success' => [
            'created' => 'Block was created successfully',
            'updated' => 'Block was updated successfully',
            'activated' => 'Block was published successfully',
            'deactivated' => 'Block was unpublished successfully',
            'moved' => 'Block was moved successfully',
            'deleted' => 'Block was deleted successfully',
            'customized' => 'Block parameters were saved successfully',
            'item' => [
                'activated' => 'Block item was published successfully',
                'deactivated' => 'Block item was unpublished successfully',
                'moved' => 'Block item was moved successfully',
                'deleted' => 'Block item was deleted successfully'
            ]
        ],

    ]
];
