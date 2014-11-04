<?php
namespace Fab\MediaUpload\Controller;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Core\Resource\Exception;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Controller which handles actions related to Asset.
 */
class MediaUploadController extends ActionController {

	/**
	 * @var \TYPO3\CMS\Core\Page\PageRenderer
	 * @inject
	 */
	protected $pageRenderer;

	/**
	 * Initialize actions. These actions are meant to be called by an logged-in FE User.
	 */
	public function initializeAction() {

		// Action below are only allowed when a is logged it.
		if (empty($this->getFrontendUser()->user)) {
			$message = 'FE User must be logged-in.';
			throw new Exception($message, 1387696171);
		}
	}

	/**
	 * Delete a file being just uploaded.
	 *
	 * @return string
	 */
	public function deleteAction() {

		$fileIdentifier = GeneralUtility::_POST('qquuid');

		/** @var $uploadManager \TYPO3\CMS\Media\FileUpload\UploadManager */
		$uploadManager = GeneralUtility::makeInstance('TYPO3\CMS\Media\FileUpload\UploadManager');
		$uploadFolderPath = $uploadManager->getUploadFolder();

		$fileNameAndPath = sprintf('%s/%s', $uploadFolderPath, $fileIdentifier);

		// @todo clean me up $fileIdentifier must be part of the file name and must be not duplicated - time pressure! - it must be changed in EXT:media as well.
		$files = glob($fileNameAndPath . '*');
		$fileWithIdentifier = current($files);
		if (file_exists($fileWithIdentifier)) {
			unlink($fileWithIdentifier);
		}

		$file = str_replace($fileIdentifier . '-', '', $fileWithIdentifier);
		if (file_exists($file)) {
			unlink($file);
		}

		$result = array(
			'success' => TRUE,
		);
		return json_encode($result);
	}

	/**
	 * Handle file upload.
	 *
	 * @todo implement validator that Fe Group is allowed to access the storage.
	 * @param int $storageIdentifier
	 * @return string
	 */
	public function uploadAction($storageIdentifier) {

		$storage = ResourceFactory::getInstance()->getStorageObject($storageIdentifier);

		/** @var $uploadManager \TYPO3\CMS\Media\FileUpload\UploadManager */
		$uploadManager = GeneralUtility::makeInstance('TYPO3\CMS\Media\FileUpload\UploadManager', $storage);

		try {
			$uploadedFile = $uploadManager->handleUpload();

			$result = array(
				'success' => TRUE,
				'viewUrl' => $uploadedFile->getPublicUrl(),
			);

			// @todo clean me up $fileIdentifier must be part of the file name and must be not duplicated - time pressure! - it must be changed in EXT: media as well.
			// @todo rename getName() by getSanitizeName()
			// Create duplicate to be able to delete
			$fileIdentifier = GeneralUtility::_POST('qquuid');
			$duplicate = sprintf('%s/%s-%s', $uploadManager->getUploadFolder(), $fileIdentifier, $uploadedFile->getName());
			copy($uploadedFile->getFileWithAbsolutePath(), $duplicate);

		} catch (\Exception $e) {
			$result = array('error' => $e->getMessage());
		}

		return json_encode($result);
	}

	/**
	 * Returns an instance of the current Frontend User.
	 *
	 * @return \TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication
	 */
	protected function getFrontendUser() {
		return $GLOBALS['TSFE']->fe_user;
	}
}
