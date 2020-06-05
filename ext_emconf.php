<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Media Upload',
    'description' => 'Fluid widget for mass uploading files on the Frontend using HTML5 techniques powered by Fine Uploader - http://fineuploader.com/',
    'category' => 'fe',
    'author' => 'Fabien Udriot',
    'author_email' => 'fabien@ecodev.ch',
    'state' => 'stable',
    'version' => '2.1.0-dev',
    'autoload' => [
        'psr-4' => ['Fab\\MediaUpload\\' => 'Classes']
    ],
    'constraints' =>
        [
            'depends' =>
                [
                    'typo3' => '9.5.0-9.5.99',
                ],
            'conflicts' =>
                [
                ],
            'suggests' =>
                [
                    'media' => '',
                    'metadata' => '',
                ],
        ],
];
