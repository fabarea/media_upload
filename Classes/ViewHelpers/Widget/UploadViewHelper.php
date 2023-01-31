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
class UploadViewHelper extends AbstractViewHelper
{
    protected $escapeOutput = false;
    
    public function initializeArguments()
    {
        $this->registerArgument(
            'allowedExtensions',
            'string',
            'Allowed extension to be uploaded.',
            false,
            '',
        )
            ->registerArgument(
                'maximumSize',
                'int',
                'Maximum file size in Mo by default.',
                false,
                0,
            )
            ->registerArgument(
                'sizeUnit',
                'string',
                'Whether it is Ko or Mo.',
                false,
                'Mo',
            )
            ->registerArgument(
                'storage',
                'int',
                'The final storage identifier to which the file will be added eventually.',
                true,
            )
            ->registerArgument(
                'maximumItems',
                'int',
                'Maximum items to be uploaded',
                false,
                10,
            )
            ->registerArgument(
                'property',
                'int',
                'The property name used for identifying and grouping uploaded files. Required if form contains multiple upload fields',
                false,
                '',
            );
    }

    #public function render(): string
    #{
    #    $uploadFileService = GeneralUtility::makeInstance(
    #        UploadFileService::class,
    #    );
    #    return static::renderStatic(
    #        [
    #            'allowedExtensions' => $this->arguments['allowedExtensions'],
    #            'maximumSize' => $this->arguments['maximumSize'],
    #            'maximumSizeLabel' => 'qwer' . self::getMaximumSizeLabel(
    #                (int) $this->arguments['maximumSize'],
    #            ),
    #            'sizeUnit' => $this->arguments['sizeUnit'],
    #            'storage' => $this->arguments['storage'],
    #            'maximumItems' => $this->arguments['maximumItems'],
    #            'property' => $this->arguments['property'],
    #            'uploadedFileList' => $uploadFileService->getUploadedFileList(
    #                $this->arguments['property'],
    #            ),
    #            'widgetIdentifier' => uniqid(),
    #        ],
    #        $this->buildRenderChildrenClosure(),
    #        $this->renderingContext,
    #    );
    #}

    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ): string {

        $uploadFileService = GeneralUtility::makeInstance(
            UploadFileService::class,
        );

        $arguments['maximumSizeLabel'] = self::getMaximumSizeLabel(
            (int)$arguments['maximumSize'],
        );

        if ($arguments['maximumSize'] === 0) {
            $arguments['maximumSize'] = GeneralUtility::getMaxUploadFileSize() * 1024;
        }

        $arguments['uploadedFileList'] = $uploadFileService->getUploadedFileList(
            $arguments['property'],
        );

        $arguments['widgetIdentifier'] = uniqid();
        /** @var StandaloneView $view */
        $view = GeneralUtility::makeInstance(StandaloneView::class);

        $view->setTemplatePathAndFilename(
            'EXT:media_upload/Resources/Private/Templates/ViewHelpers/Widget/Upload/Index.html',
        );
        $view->assignMultiple($arguments);
        return $view->render();
    }

    public static function getMaximumSizeLabel(int $maximumSize = 0): int
    {
        $maximumSizeLabel = GeneralUtility::getMaxUploadFileSize() / 1024;
        if ($maximumSize > 0) {
            $maximumSizeLabel = $maximumSize;
        }

        return (int) $maximumSizeLabel;
    }

    /**
     * @param string $property
     * @return string
     */
    public static function getUploadedFileList($property = ''): string
    {
        $parameters = GeneralUtility::_GPmerged('tx_mediaupload_upload');
        return empty($parameters['uploadedFiles'][$property])
            ? ''
            : $parameters['uploadedFiles'][$property];
    }
}
