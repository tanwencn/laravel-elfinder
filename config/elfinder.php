<?php

return [
    'roots' => [
        'default' => [
            'process' => Tanwencn\Elfinder\FinderProcess::class,
            'options' => [
                'disk' => 'public',
                'uploadOverwrite' => false,
                'uploadMaxSize' => '3M',
                'onlyMimes' => ['image'],
                'uploadOrder' => ['allow'],
                'path' => 'images',
                'alias' => 'Gallery'
            ]
        ]
    ]
];
