<?php

$EM_CONF['media_upload'] = [
    'title' => 'Media Upload',
    'description' => 'Fluid widget for mass uploading files on the Frontend using HTML5 techniques powered by Fine Uploader - http://fineuploader.com/',
    'category' => 'fe',
    'author' => 'Fabien Udriot',
    'author_email' => 'fabien@ecodev.ch',
    'state' => 'stable',
    'autoload' => [
        'psr-4' => ['Fab\\MediaUpload\\' => 'Classes']
    ],
    'constraints' =>
        [
            'depends' =>
                [
                    'typo3' => '12.4.0-13.4.99',
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
