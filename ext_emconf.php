<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Media Upload',
    'description' => 'Fluid widget for mass uploading files on the Frontend using HTML5 techniques powered by Fine Uploader - http://fineuploader.com/',
    'category' => 'fe',
    'author' => 'Fabien Udriot',
    'author_email' => 'fabien@ecodev.ch',
    'state' => 'beta',
    'version' => '1.0.0-dev',
    'constraints' =>
        [
            'depends' =>
                [
                    'typo3' => '7.6.0-7.6.99',
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
