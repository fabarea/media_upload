<?php

use Fab\MediaUpload\Controller\MediaUploadController;

defined('TYPO3') or die();

call_user_func(static function () {

    $configuration = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
        \TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class
    )->get('media_upload');

    if (!isset($configuration['autoload_typoscript']) || !empty($configuration['autoload_typoscript'])) {
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
        'MediaUpload',
        'Upload',
        [
            MediaUploadController::class => 'upload',
        ],
        // non-cacheable actions
        [
            MediaUploadController::class => 'upload',
        ]
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'MediaUpload',
        'Delete',
        [
            MediaUploadController::class => 'delete',
        ],
        // non-cacheable actions
        [
            MediaUploadController::class => 'delete',
        ]
    );
    // command line is replaced by symfony command:
    // ./vendor/bin/typo3 mediaupload:removeTempFiles rundry=1

});
