<?php
namespace Fab\MediaUpload\ViewHelpers\Widget\Controller;

/*
 * This file is part of the Fab/MediaUpload project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\Core\Widget\AbstractWidgetController;

/**
 * Show uploaded Controller for widget ShowUploaded.
 */
class ShowUploadedController extends AbstractWidgetController
{

    /**
     * @var \Fab\MediaUpload\Service\UploadFileService
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $uploadFileService;

    /**
     * @return void
     */
    public function indexAction()
    {
        $property = $this->widgetConfiguration['property'];
        $this->view->assign('property', $property);
        $this->view->assign('uploadedFileList', $this->uploadFileService->getUploadedFileList($property));
        $this->view->assign('uploadedFiles', $this->uploadFileService->getUploadedFiles($property));
    }
}
