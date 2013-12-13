<?php
namespace TYPO3\CMS\MediaUpload\Controller;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Fabien Udriot <fabien.udriot@typo3.org>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Controller which handles actions related to Asset.
 */
class MediaUploadController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	/**
	 * @var \TYPO3\CMS\Media\Domain\Repository\AssetRepository
	 * @inject
	 */
	protected $assetRepository;

	/**
	 * @var \TYPO3\CMS\Media\Domain\Repository\VariantRepository
	 * @inject
	 */
	protected $variantRepository;

	/**
	 * @var \TYPO3\CMS\Core\Page\PageRenderer
	 * @inject
	 */
	protected $pageRenderer;

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

		// @todo clean me up $fileIdentifier must be part of the file name and must be not duplicated - time pressure! - it must be changed in EXT: media as well.
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
	 * @return string
	 */
	public function uploadAction() {

		/** @var $uploadManager \TYPO3\CMS\Media\FileUpload\UploadManager */
		$uploadManager = GeneralUtility::makeInstance('TYPO3\CMS\Media\FileUpload\UploadManager');

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
}
?>
