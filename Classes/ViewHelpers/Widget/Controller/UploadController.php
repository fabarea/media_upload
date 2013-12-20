<?php
namespace TYPO3\CMS\MediaUpload\ViewHelpers\Widget\Controller;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012-2013 Fabien Udriot <fabien.udriot@typo3.org>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
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
use TYPO3\CMS\Media\Utility\PermissionUtility;

/**
 * MediaUpload Controller for widget Upload.
 */
class UploadController extends \TYPO3\CMS\Fluid\Core\Widget\AbstractWidgetController {

	/**
	 * @var \TYPO3\CMS\MediaUpload\Service\UploadFileService
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

?>