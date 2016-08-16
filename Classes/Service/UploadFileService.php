<?php
namespace Fab\MediaUpload\Service;

/*
 * This file is part of the Fab/MediaUpload project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\MediaUpload\FileUpload\UploadManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Fab\MediaUpload\UploadedFile;

/**
 * Uploaded files service.
 */
class UploadFileService
{

    /**
     * Return the list of uploaded files.
     *
     * @param string $property
     * @return string
     */
    public function getUploadedFileList($property = '')
    {
        $parameters = GeneralUtility::_GPmerged('tx_mediaupload_pi1');
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
        $uploadedFiles = GeneralUtility::trimExplode(',', $this->getUploadedFileList($property), TRUE);

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

            $fileSize = round(filesize($sanitizedFileNameAndPath) / 1000);

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
        }
        return $sanitizedFileNameAndPath;
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
