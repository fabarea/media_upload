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

use TYPO3\CMS\Fluid\Core\Widget\AbstractWidgetController;

/**
 * Show uploaded Controller for widget ShowUploaded.
 */
class ShowUploadedController extends AbstractWidgetController {

	/**
	 * @var \Fab\MediaUpload\Service\UploadFileService
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
