<?php
defined('TYPO3') or die();

call_user_func(static function () {
    $configuration = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
        \TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class
    )->get('media_upload');

    // Possible Static TS loading
    if (!empty($configuration['autoload_typoscript'])) {
    }
});
