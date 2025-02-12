<?php
namespace Fab\MediaUpload\ViewHelpers\Widget;

/*
 * This file is part of the Fab/MediaUpload project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\View\ViewFactoryData;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;


/**
 * Widget which displays a media upload.
 */
class ShowUploadedViewHelper extends AbstractViewHelper
{
    protected array $settings;

    public function __construct(
        protected \Fab\MediaUpload\Service\UploadFileService $uploadFileService,
        protected \TYPO3\CMS\Core\View\ViewFactoryInterface $viewFactory,
        ConfigurationManagerInterface $configurationManager
    )
    {
        $this->settings = $configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK,
            'MediaUpload',
            'Upload'
        );
    }

    /**
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument('property', 'int', 'The property name used for identifying and grouping uploaded files. Required if form contains multiple upload fields', FALSE, '');
    }

    private function getRequest(): ServerRequestInterface|null
    {
        if ($this->renderingContext->hasAttribute(ServerRequestInterface::class)) {
            return $this->renderingContext->getAttribute(ServerRequestInterface::class);
        }
        return null;
    }

    /**
     * Returns an carousel widget
     *
     * @return string
     */
    public function render()
    {
        # generate a standalone view and render the template ViewHelpers/Widget/Upload/Index.html
        $viewFactoryData = new ViewFactoryData(
            templateRootPaths: $this->settings["view"]["templateRootPaths"],
            partialRootPaths: $this->settings["view"]["partialRootPaths"],
            layoutRootPaths: $this->settings["view"]["layoutRootPaths"],
        );

        $view = $this->viewFactory->create($viewFactoryData);


        $property = $this->arguments['property'] ?? '';
        $request = $this->getRequest();
        debug($request->getParsedBody()["tx_mediaupload_pi1"]);
        $view->assign('property', $property);
        $view->assign('uploadedFileList', $this->uploadFileService->getUploadedFileList($request, $property));
        $view->assign('uploadedFiles', $this->uploadFileService->getUploadedFiles($request, $property));

        return $view->render("ShowUploaded.html");
    }
}
