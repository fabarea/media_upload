<?php
namespace Fab\MediaUpload\ViewHelpers\Widget;

/*
 * This file is part of the Fab/MediaUpload project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\Core\Widget\AbstractWidgetViewHelper;

/**
 * Widget which displays a media upload.
 */
class ShowUploadedViewHelper extends AbstractWidgetViewHelper
{

    /**
     * @var \Fab\MediaUpload\ViewHelpers\Widget\Controller\ShowUploadedController
     * @inject
     */
    protected $controller;

    /**
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument('property', 'int', 'The property name used for identifying and grouping uploaded files. Required if form contains multiple upload fields', FALSE, '');
    }

    /**
     * Returns an carousel widget
     *
     * @return string
     */
    public function render()
    {
        return $this->initiateSubRequest();
    }
}
