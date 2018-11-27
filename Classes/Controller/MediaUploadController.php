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
use TYPO3\CMS\Core\Resource\Exception;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;

/**
 * Controller which handles actions related to Asset.
 */
class MediaUploadController extends ActionController
{

    /**
     * Initialize actions. These actions are meant to be called by an logged-in FE User.
     * @throws \TYPO3\CMS\Core\Resource\Exception
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException
     */
    public function initializeAction()
    {

        // Perhaps it should go into a validator?
        // Check permission before executing any action.
        $allowedFrontendGroups = trim($this->settings['allowedFrontendGroups']);
        if ($allowedFrontendGroups === '*') {
            if (empty($this->getFrontendUser()->user)) {
                throw new Exception('FE User must be logged-in.', 1387696171);
            }
        } elseif (!empty($allowedFrontendGroups)) {

            $isAllowed = FALSE;
            $frontendGroups = GeneralUtility::trimExplode(',', $allowedFrontendGroups, TRUE);
            foreach ($frontendGroups as $frontendGroup) {
                if (GeneralUtility::inList($this->getFrontendUser()->user['usergroup'], $frontendGroup)) {
                    $isAllowed = TRUE;
                    break;
                }
            }

            // Throw exception if not allowed
            if (!$isAllowed) {
                throw new Exception('FE User does not have enough permission.', 1415211931);
            }
        }

        $this->emitBeforeHandleUploadSignal();
    }

    /**
     * Delete a file being just uploaded.
     *
     * @return string
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     * @throws \InvalidArgumentException
     */
    public function deleteAction()
    {
        $folderIdentifier = GeneralUtility::_POST('qquuid');

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
            $this->throwStatus(404, $error);
        }

        return json_encode(['success' => true]);
    }

    /**
     * Handle file upload.
     *
     * @param int $storageIdentifier
     * @return string
     * @throws \InvalidArgumentException
     */
    public function uploadAction($storageIdentifier)
    {
        $storage = ResourceFactory::getInstance()->getStorageObject($storageIdentifier);

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

        return json_encode($result);
    }

    /**
     * Returns an instance of the current Frontend User.
     *
     * @return \TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication
     */
    protected function getFrontendUser()
    {
        return $GLOBALS['TSFE']->fe_user;
    }

    /**
     * Signal that is emitted before upload processing is called.
     *
     * @return void
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException
     */
    protected function emitBeforeHandleUploadSignal()
    {
        $this->getSignalSlotDispatcher()->dispatch(MediaUploadController::class, 'beforeHandleUpload');
    }

    /**
     * Get the SignalSlot dispatcher.
     *
     * @return Dispatcher
     */
    protected function getSignalSlotDispatcher()
    {
        return $this->objectManager->get(Dispatcher::class);
    }
}
