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
use TYPO3\CMS\Fluid\Core\Widget\AbstractWidgetController;

/**
 * Show uploaded Controller for widget ShowUploaded.
 */
class ShowUploadedController extends AbstractWidgetController {

	/**
	 * @var \TYPO3\CMS\MediaUpload\Service\UploadFileService
	 * @inject
	 */
	protected $uploadFileService;

	/**
	 * @return void
	 */
	public function indexAction() {
		$property = $this->widgetConfiguration['property'];
		$this->view->assign('property', $property);
		$this->view->assign('uploadedFileList', $this->uploadFileService->getUploadedFileList($property));
		$this->view->assign('uploadedFiles', $this->uploadFileService->getUploadedFiles($property));
	}
}

?>