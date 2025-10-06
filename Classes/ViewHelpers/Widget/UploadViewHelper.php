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

    public function initializeArguments(): void
    {
        $this->registerArgument(
            'allowedExtensions',
            'string',
            'Allowed extension to be uploaded.',
            false,
            ''
        );
        $this->registerArgument(
            'maximumSize',
            'int',
            'Maximum file size in Mo by default.',
            false,
            0
        );
        $this->registerArgument(
            'sizeUnit',
            'string',
            'Whether it is Ko or Mo.',
            false,
            'Mo'
        );
        $this->registerArgument(
            'storage',
            'int',
            'The final storage identifier to which the file will be added eventually.',
            true
        );
        $this->registerArgument(
            'maximumItems',
            'int',
            'Maximum items to be uploaded',
            false,
            10
        );
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

        // Get request parameters from rendering context if available, otherwise fallback to superglobals
        $requestParameters = [];
        $request = $renderingContext->getRequest();
        if ($request && method_exists($request, 'getQueryParams') && method_exists($request, 'getParsedBody')) {
            // Modern PSR-7 request approach
            $queryParams = $request->getQueryParams();
            $postParams = $request->getParsedBody() ?? [];
            $requestParameters = array_merge($queryParams, $postParams); // POST takes precedence
        }

        $uploadFileService = GeneralUtility::makeInstance(
            UploadFileService::class,
            $requestParameters
        );

        $arguments['maximumSizeLabel'] = self::getMaximumSizeLabel(
            (int)$arguments['maximumSize']
        );

        if ($arguments['maximumSize'] === 0) {
            $arguments['maximumSize'] = GeneralUtility::getMaxUploadFileSize() * 1024;
        }

        $arguments['uploadedFileList'] = $uploadFileService->getUploadedFileList(
            $arguments['property']
        );

        $arguments['widgetIdentifier'] = uniqid();

        /** @var StandaloneView $view */
        $view = GeneralUtility::makeInstance(StandaloneView::class);

        $view->setTemplatePathAndFilename(
            'EXT:media_upload/Resources/Private/Templates/ViewHelpers/Widget/Upload/Index.html'
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
    public static function getUploadedFileList(string $property = ''): string
    {
        // Fallback to direct superglobal access to avoid deprecated method
        $getParams = $_GET['tx_mediaupload_upload'] ?? [];
        $postParams = $_POST['tx_mediaupload_upload'] ?? [];
        $parameters = array_merge($getParams, $postParams); // POST takes precedence

        return empty($parameters['uploadedFiles'][$property])
            ? ''
            : $parameters['uploadedFiles'][$property];
    }
}
