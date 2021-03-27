<?php



if (!defined('TYPO3_MODE')) die ('Access denied.');

call_user_func(function () {

    $configuration = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class) ->get('media_upload');

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
    /** @var \TYPO3\CMS\Core\Information\Typo3Version $version */
    $version = TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\Information\Typo3Version');
    if ($version->getMajorVersion()  < 10) {
        // Setting up a script that can be run from the cli_dispatch.phpsh script.
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] = \Fab\MediaUpload\Command\TemporaryFileCommandController::class;
    }
});