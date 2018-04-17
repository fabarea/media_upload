<?php
if (!defined('TYPO3_MODE')) die ('Access denied.');

call_user_func(function () {
    $configuration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['media_upload']);
    // Possible Static TS loading
    if (true === isset($configuration['autoload_typoscript']) && true === (bool)$configuration['autoload_typoscript']) {
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('media_upload', 'Configuration/TypoScript', 'Media upload');
    }
});
