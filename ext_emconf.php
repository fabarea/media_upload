<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Media Upload',
    'description' => 'Fluid widget for mass uploading files on the Frontend using HTML5 techniques powered by Fine Uploader - http://fineuploader.com/',
    'category' => 'fe',
    'author' => 'Fabien Udriot',
    'author_email' => 'fabien@ecodev.ch',
    'state' => 'stable',
    'version' => '3.0.3-dev',
    'autoload' => [
        'psr-4' => ['Fab\\MediaUpload\\' => 'Classes']
    ],
    'constraints' =>
        [
            'depends' =>
                [
                    'typo3' => '11.5.0-11.5.99',
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
