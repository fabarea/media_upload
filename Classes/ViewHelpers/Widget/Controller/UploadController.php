<?php
namespace Fab\MediaUpload\ViewHelpers\Widget\Controller;

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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\Widget\AbstractWidgetController;
use TYPO3\CMS\Media\Utility\PermissionUtility;

/**
 * MediaUpload Controller for widget Upload.
 */
class UploadController extends AbstractWidgetController {

	/**
	 * @var \Fab\MediaUpload\Service\UploadFileService
	 * @inject
	 */
	protected $uploadFileService;

	/**
	 * @return void
	 */
	public function indexAction() {
		$this->view->assign('allowedExtensions', $this->getAllowedExtensions());
		$this->view->assign('maximumSize', $this->getMaximumSize());
		$this->view->assign('maximumSizeLabel', $this->getMaximumSizeLabel());
		$this->view->assign('sizeUnit', $this->widgetConfiguration['sizeUnit']);
		$this->view->assign('storage', $this->widgetConfiguration['storage']);
		$this->view->assign('uniqueId', uniqid());

		$property = $this->widgetConfiguration['property'];
		$this->view->assign('property', $property);
		$this->view->assign('maximumItems', $this->widgetConfiguration['maximumItems']);
		$this->view->assign('uploadedFileList', $this->uploadFileService->getUploadedFileList($property));
	}

	/**
	 * Compute the maximum size allowed to be uploaded.
	 * Return a value in bytes.
	 *
	 * @return int
	 */
	public function getMaximumSizeLabel() {

		$maximumSize = GeneralUtility::getMaxUploadFileSize() / 1024;
		if (!empty($this->widgetConfiguration['maximumSize'])) {
			$maximumSize = $this->widgetConfiguration['maximumSize'];
		}

		return $maximumSize;
	}

	/**
	 * Compute the maximum size allowed to be uploaded.
	 * Return a value in bytes.
	 *
	 * @return int
	 */
	public function getMaximumSize() {

		$maximumSize = GeneralUtility::getMaxUploadFileSize() * 1024;
		if (!empty($this->widgetConfiguration['maximumSize'])) {
			$maximumSize = $this->widgetConfiguration['maximumSize'];

			if ($this->widgetConfiguration['sizeUnit'] == 'Ko') {
				$maximumSize = $maximumSize * 1024;
			} else {
				$maximumSize = $maximumSize * pow(1024, 2);
			}
		}

		return $maximumSize;
	}

	/**
	 * Compute the allowed extensions to be uploaded.
	 *
	 * @return string
	 */
	public function getAllowedExtensions() {
		$allowedExtensions = '';

		if (!empty($this->widgetConfiguration['allowedExtensions'])) {
			$allowedExtensions = GeneralUtility::trimExplode(',', $this->widgetConfiguration['allowedExtensions'], TRUE);
		} elseif ($this->widgetConfiguration['storage'] > 0) {
			$allowedExtensions = PermissionUtility::getInstance()->getAllowedExtensions($this->widgetConfiguration['storage']);
		}

		// Format to be eventually consumed by JavaScript.
		if (!empty($allowedExtensions)) {
			$allowedExtensions = implode("','", $allowedExtensions);
		}

		return $allowedExtensions;
	}
}
