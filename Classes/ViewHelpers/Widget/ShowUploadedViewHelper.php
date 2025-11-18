<?php
namespace Fab\MediaUpload\ViewHelpers\Widget;

/*
 * This file is part of the Fab/MediaUpload project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\MediaUpload\Service\UploadFileService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Widget which displays a media upload.
 */
class ShowUploadedViewHelper extends AbstractViewHelper
{
    protected $escapeOutput = false;

    /**
     * @return void
     */
    public function initializeArguments(): void
    {
        $this->registerArgument(
            'property',
            'string',
            'The property name used for identifying and grouping uploaded files. Required if form contains multiple upload fields',
            false,
            ''
        );
    }

    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ): string {
        $uploadFileService = GeneralUtility::makeInstance(UploadFileService::class);

        $arguments['uploadedFileList'] = $uploadFileService->getUploadedFileList(
            $arguments['property']
        );
        $arguments['uploadedFiles'] = $uploadFileService->getUploadedFiles(
            $arguments['property']
        );

        /** @var StandaloneView $view */
        $view = GeneralUtility::makeInstance(StandaloneView::class);

        $view->setTemplatePathAndFilename(
            'EXT:media_upload/Resources/Private/Templates/ViewHelpers/Widget/ShowUploaded/Index.html'
        );
        $view->assignMultiple($arguments);
        return $view->render();
    }
}
