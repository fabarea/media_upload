<?php
namespace Fab\MediaUpload\Controller;

/*
 * This file is part of the Fab/MediaUpload project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\MediaUpload\FileUpload\UploadManager;
use Fab\MediaUpload\Utility\UuidUtility;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Http\Stream;
use TYPO3\CMS\Core\Resource\Exception;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;

/**
 * Controller which handles actions related to Asset.
 *
 * Note: Do not call parent::__construct() — ActionController has no constructor
 * in TYPO3 v12/v13; EventDispatcher is injected via injectEventDispatcher().
 */
class MediaUploadController extends ActionController
{
    /**
     * Initialize actions. These actions are meant to be called by an logged-in FE User.
     */
    public function initializeAction(): void
    {

        // Perhaps it should go into a validator?
        // Check permission before executing any action.
        $allowedFrontendGroups = trim($this->settings['allowedFrontendGroups'] ?? '');
        $frontendUser = $this->getFrontendUser();
        $userData = is_array($frontendUser->user ?? null) ? $frontendUser->user : [];

        if ($allowedFrontendGroups === '*') {
            if ($userData === []) {
                throw new Exception('FE User must be logged-in.', 1387696171);
            }
        } elseif ($allowedFrontendGroups !== '') {

            $isAllowed = false;
            $frontendGroups = GeneralUtility::trimExplode(',', $allowedFrontendGroups, true);
            foreach ($frontendGroups as $frontendGroup) {
                if (GeneralUtility::inList($userData['usergroup'] ?? '', $frontendGroup)) {
                    $isAllowed = true;
                    break;
                }
            }

            // Throw exception if not allowed
            if (!$isAllowed) {
                throw new Exception('FE User does not have enough permission.', 1415211931);
            }
        }

        // Note: Signal replaced by PSR-14 events in TYPO3 v12
        // You would need to create a custom event class if needed
    }

    /**
     * Delete a file being just uploaded.
     */
    public function deleteAction(): ResponseInterface
    {
        $folderIdentifier = $this->request->getParsedBody()['qquuid'] ?? '';

        $error = '';

        // check uuid format
        if (UuidUtility::getInstance()->isValid($folderIdentifier)){

            /** @var UploadManager $uploadManager */
            $uploadManager = GeneralUtility::makeInstance(UploadManager::class);
            $uploadFolderPath = $uploadManager->getUploadFolder();

            if (is_dir($uploadFolderPath)) {
                $isRemoved = GeneralUtility::rmdir($uploadFolderPath, true);
                if (!$isRemoved) {
                    $error = 'Permission problem? I could not perform this action.';
                }
            } else {
                $error = 'File not found!';
            }
        } else {
            $error = 'File identifier is not correct'; // default error
        }

        if ($error !== '') {
            throw new \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException('File operation failed: ' . $error, 1387123456);
        }

        $jsonResponse = json_encode(['success' => true]);

        $body = new Stream('php://temp', 'rw');
        $body->write($jsonResponse);

        return (new Response())
            ->withHeader('content-type', 'application/json; charset=utf-8')
            ->withBody($body)
            ->withStatus(200);
    }

    /**
     * Handle file upload.
     */
    public function uploadAction(int $storageIdentifier): ResponseInterface
    {
        /** @var ResourceFactory $factory */
        $factory = GeneralUtility::makeInstance(ResourceFactory::class) ;

        $storage = $factory->getStorageObject($storageIdentifier);

        /** @var $uploadManager UploadManager */
        $uploadManager = GeneralUtility::makeInstance(UploadManager::class, $storage);

        try {
            $uploadedFile = $uploadManager->handleUpload();

            $result = [
                'success' => true,
                'viewUrl' => $uploadedFile->getPublicUrl(),
            ];
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage()];
        }

        $jsonResponse = json_encode($result);

        $body = new Stream('php://temp', 'rw');
        $body->write($jsonResponse);

        return (new Response())
            ->withHeader('content-type', 'application/json; charset=utf-8')
            ->withBody($body)
            ->withStatus(200);
    }

    /**
     * Returns an instance of the current Frontend User.
     */
    protected function getFrontendUser(): FrontendUserAuthentication
    {
        $request = $GLOBALS['TYPO3_REQUEST'] ?? null;
        if ($request !== null) {
            $frontendUser = $request->getAttribute('frontend.user');
            if ($frontendUser instanceof FrontendUserAuthentication) {
                return $frontendUser;
            }
        }

        if (isset($GLOBALS['TSFE']->fe_user) && $GLOBALS['TSFE']->fe_user instanceof FrontendUserAuthentication) {
            return $GLOBALS['TSFE']->fe_user;
        }

        return GeneralUtility::makeInstance(FrontendUserAuthentication::class);
    }
}
