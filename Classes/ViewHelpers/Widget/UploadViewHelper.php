<?php
namespace TYPO3\CMS\MediaUpload\ViewHelpers\Widget;

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

use TYPO3\CMS\Fluid\Core\Widget\AbstractWidgetViewHelper;

/**
 * Widget which displays a media upload.
 */
class UploadViewHelper extends AbstractWidgetViewHelper {

	/**
	 * @var \TYPO3\CMS\MediaUpload\ViewHelpers\Widget\Controller\UploadController
	 * @inject
	 */
	protected $controller;

	/**
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerArgument('allowedExtensions', 'string', 'Allowed extension to be uploaded.', FALSE, '');
		$this->registerArgument('maximumSize', 'int', 'Maximum file size in Mo by default.', FALSE, 0);
		$this->registerArgument('sizeUnit', 'string', 'Whether it is Ko or Mo.', FALSE, 'Mo');
		$this->registerArgument('storage', 'int', 'The final storage identifier to which the file will be added eventually.', TRUE);
		$this->registerArgument('maximumItems', 'int', 'Maximum items to be uploaded', FALSE, 10);
		$this->registerArgument('property', 'int', 'The property name used for identifying and grouping uploaded files. Required if form contains multiple upload fields', FALSE, '');
	}

	/**
	 * Returns an carousel widget
	 *
	 * @return string
	 */
	public function render() {
		return $this->initiateSubRequest();
	}
}
