<?php
if (!defined('TYPO3_MODE')) die ('Access denied.');

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'media_upload',
    'Pi1',
    array(
        'MediaUpload' => 'upload',
    ),
    // non-cacheable actions
    array(
        'MediaUpload' => 'upload',
    )
);

// Setting up a script that can be run from the cli_dispatch.phpsh script.
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] = 'Fab\MediaUpload\Command\TemporaryFileCommandController';