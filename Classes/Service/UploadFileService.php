<?php
namespace Fab\MediaUpload\Service;

/*
 * This file is part of the Fab/MediaUpload project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\MediaUpload\FileUpload\UploadManager;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Fab\MediaUpload\FileUpload\UploadedFile;

/**
 * Uploaded files service.
 */
class UploadFileService
{

    /**
     * Return the list of uploaded files.
     *
     * @param string $property
     * @param ServerRequestInterface $request
     * @return string
     */
    public function getUploadedFileList($request, $property = '')
    {
        $parameters = $request->getParsedBody()["tx_mediaupload_pi1"] ?? $request->getQueryParams()["tx_mediaupload_pi1"] ?? [];
        return empty($parameters['uploadedFiles'][$property]) ? '' : $parameters['uploadedFiles'][$property];
    }

    /**
     * Return an array of uploaded files, done in a previous step.
     *
     * @param string $property
     * @param ServerRequestInterface $request
     * @return UploadedFile[]
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function getUploadedFiles($request, $property = '')
    {
        $files = array();
        $uploadedRelativeFiles = GeneralUtility::trimExplode(',', $this->getUploadedFileList($request, $property), TRUE);

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
        } elseif (count($pathSegments) === 1 && strpos($uploadedFileName, '..') === false) {
            $sanitizedFileNameAndPath = UploadManager::UPLOAD_FOLDER . $pathSegments[0];
        }
        return $sanitizedFileNameAndPath;
    }

    /**
     * Return the first uploaded files, done in a previous step.
     *
     * @param string $property
     * @param ServerRequestInterface $request
     * @return array
     */
    public function getUploadedFile($request, $property = '')
    {
        $uploadedFile = array();

        $uploadedFiles = $this->getUploadedFiles($request, $property);
        if (!empty($uploadedFiles)) {
            $uploadedFile = current($uploadedFiles);
        }

        return $uploadedFile;
    }

    /**
     * Count uploaded files.
     *
     * @param string $property
     * @param ServerRequestInterface $request
     * @return int
     */
    public function countUploadedFiles($request, $property = '')
    {
        return count(GeneralUtility::trimExplode(',', $this->getUploadedFileList($request, $property), TRUE));
    }

}
