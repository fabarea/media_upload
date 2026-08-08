<?php
namespace Fab\MediaUpload\Service;

/*
 * This file is part of the Fab/MediaUpload project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\MediaUpload\FileUpload\UploadManager;
use Fab\MediaUpload\UploadedFile;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Uploaded files service.
 */
class UploadFileService
{
    /**
     * @var array
     */
    protected $requestParameters = [];

    /**
     * Constructor to inject request parameters
     *
     * @param array $requestParameters
     */
    public function __construct(array $requestParameters = [])
    {
        $this->requestParameters = $requestParameters;
    }

    /**
     * Return the list of uploaded files.
     *
     * @param string $property
     * @return string
     */
    public function getUploadedFileList($property = '')
    {
        // Use injected parameters or fallback to legacy method for backwards compatibility
        if (!empty($this->requestParameters)) {
            $parameters = $this->requestParameters['tx_mediaupload_upload'] ?? [];
        } else {
            // Fallback for backwards compatibility - direct access to superglobals
            $getParams = $_GET['tx_mediaupload_upload'] ?? [];
            $postParams = $_POST['tx_mediaupload_upload'] ?? [];
            $parameters = array_merge($getParams, $postParams); // POST takes precedence
        }

        return empty($parameters['uploadedFiles'][$property]) ? '' : $parameters['uploadedFiles'][$property];
    }

    /**
     * Return an array of uploaded files, done in a previous step.
     *
     * @param string $property
     * @return UploadedFile[]
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function getUploadedFiles($property = '')
    {
        $files = array();
        $uploadedRelativeFiles = GeneralUtility::trimExplode(',', $this->getUploadedFileList($property), TRUE);

        $uploadedFiles = array_map(function ($item) {
            return UploadManager::UPLOAD_FOLDER.'/'.$item;
        }, $uploadedRelativeFiles);

        // Convert uploaded files into array
        foreach ($uploadedFiles as $uploadedFileName) {

            // Protection against directory traversal.
            $sanitizedFileNameAndPath = $this->sanitizeFileNameAndPath($uploadedFileName);

            if (!is_file($sanitizedFileNameAndPath)) {
                $message = sprintf(
                    'I could not find file "%s". Something went wrong during the upload? Or is it some cache effect?',
                    $uploadedFileName
                );
                throw new \RuntimeException($message, 1389550006);
            }

            $fileSize = filesize($sanitizedFileNameAndPath);
            $fileSize = $fileSize !== false ? round($fileSize / 1000) : 0;

            /** @var UploadedFile $uploadedFile */
            $uploadedFile = GeneralUtility::makeInstance(UploadedFile::class);
            $uploadedFile->setTemporaryFileNameAndPath($sanitizedFileNameAndPath)
                ->setFileName(basename($uploadedFileName))
                ->setSize($fileSize);

            $files[] = $uploadedFile;
        }

        return $files;
    }

    /**
     * Protection against directory traversal.
     *
     * @param string $uploadedFileName
     * @return string
     */
    protected function sanitizeFileNameAndPath($uploadedFileName)
    {
        // default return.
        $sanitizedFileNameAndPath = '';

        // Prepend slash in any case.
        $uploadedFileName = '/' . ltrim($uploadedFileName, '/');
        $pathSegments = GeneralUtility::trimExplode(UploadManager::UPLOAD_FOLDER, $uploadedFileName, true);

        // Also check the path does not contain any back segment like "..".
        if (count($pathSegments) === 2 && strpos($uploadedFileName, '..') === false) {
            $sanitizedFileNameAndPath = UploadManager::UPLOAD_FOLDER . $pathSegments[1];
        } elseif (count($pathSegments) === 1 && strpos($uploadedFileName, '..') === false) {
            $sanitizedFileNameAndPath = UploadManager::UPLOAD_FOLDER . $pathSegments[0];
        }

        if ($sanitizedFileNameAndPath === '') {
            return '';
        }

        // Resolve against public path (eID/CLI cwd is not always htdocs/).
        $absolutePath = Environment::getPublicPath() . '/' . ltrim($sanitizedFileNameAndPath, '/');
        return is_file($absolutePath) ? $absolutePath : $sanitizedFileNameAndPath;
    }

    /**
     * Return the first uploaded files, done in a previous step.
     *
     * @param string $property
     * @return array
     */
    public function getUploadedFile($property = '')
    {
        $uploadedFile = array();

        $uploadedFiles = $this->getUploadedFiles($property);
        if (!empty($uploadedFiles)) {
            $uploadedFile = current($uploadedFiles);
        }

        return $uploadedFile;
    }

    /**
     * Count uploaded files.
     *
     * @param string $property
     * @return array
     */
    public function countUploadedFiles($property = '')
    {
        return count(GeneralUtility::trimExplode(',', $this->getUploadedFileList($property), TRUE));
    }
}
