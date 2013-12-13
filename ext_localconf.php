<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	$_EXTKEY,
	'Pi1',
	array(
		'MediaUpload' => 'upload',
	),
	// non-cacheable actions
	array(
		'MediaUpload' => 'upload',
	)
);
?>
