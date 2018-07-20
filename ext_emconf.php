<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Media Upload',
    'description' => 'Fluid widget for mass uploading files on the Frontend using HTML5 techniques powered by Fine Uploader - http://fineuploader.com/',
    'category' => 'fe',
    'author' => 'Fabien Udriot',
    'author_email' => 'fabien@ecodev.ch',
    'state' => 'beta',
    'version' => '1.0.2',
    'autoload' => [
        'psr-4' => ['Fab\\MediaUpload\\' => 'Classes']
    ],
    'constraints' =>
        [
            'depends' =>
                [
                    'typo3' => '7.6.0-8.7.99',
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
