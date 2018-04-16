<?php
if (!defined('TYPO3_MODE')) die ('Access denied.');

$configuration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['media_upload']);
if (false === isset($configuration['autoload_typoscript']) || true === (bool)$configuration['autoload_typoscript']) {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript(
        'media_upload',
        'constants',
        '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:media_upload/Configuration/TypoScript/constants.typoscript">'
    );
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript(
        'media_upload',
        'setup',
        '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:media_upload/Configuration/TypoScript/setup.typoscript">'
    );
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Fab.media_upload',
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
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] = \Fab\MediaUpload\Command\TemporaryFileCommandController::class;