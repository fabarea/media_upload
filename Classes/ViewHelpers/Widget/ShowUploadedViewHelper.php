<?php
namespace Fab\MediaUpload\ViewHelpers\Widget;

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
class ShowUploadedViewHelper extends AbstractWidgetViewHelper {

	/**
	 * @var \Fab\MediaUpload\ViewHelpers\Widget\Controller\ShowUploadedController
	 * @inject
	 */
	protected $controller;

	/**
	 * @return void
	 */
	public function initializeArguments() {
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
