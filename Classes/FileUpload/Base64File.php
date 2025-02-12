<?php
namespace Fab\MediaUpload\FileUpload;

/*
 * This file is part of the Fab/MediaUpload project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use RuntimeException;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Handle a posted file encoded in base 64.
 */
class Base64File extends \Fab\MediaUpload\FileUpload\UploadedFileAbstract
{

    /**
     * @var string
     */
    protected $inputName = 'qqfile';

    /**
     * @var string
     */
    protected $uploadFolder;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $image;

    /**
     * @var string
     */
    protected $extension;

    /**
     * @return \Fab\MediaUpload\FileUpload\Base64File
     * @throws RuntimeException
     */
    public function __construct()
    {

        // Processes the encoded image data and returns the decoded image
        $encodedImage = GeneralUtility::_POST($this->inputName);
        if (preg_match('/^data:image\/(jpg|jpeg|png)/i', $encodedImage, $matches)) {
            $this->extension = $matches[1];
        } else {
            throw new RuntimeException('File extension is not recognized', 1469376026);
        }

        // Remove the mime-type header
        $data = reset(array_reverse(explode('base64,', $encodedImage)));

        // Use strict mode to prevent characters from outside the base64 range
        $this->image = base64_decode($data, true);

        if (!$this->image) {
            throw new RuntimeException('No data could be decoded', 1469376027);
        }

        $this->setName(uniqid('', true) . '.' . $this->extension);
    }

    /**
     * Save the file to the specified path
     *
     * @return boolean TRUE on success
     * @throws RuntimeException
     */
    public function save()
    {

        if (is_null($this->uploadFolder)) {
            throw new RuntimeException('Upload folder is not defined', 1362587741);
        }

        if (is_null($this->name)) {
            throw new RuntimeException('File name is not defined', 1362587742);
        }

        return file_put_contents($this->getFileWithAbsolutePath(), $this->image) > 0;
    }

    /**
     * Get the original file name.
     *
     * @return string
     */
    public function getOriginalName()
    {
        return $this->getName();
    }

    /**
     * Get the file size
     *
     * @throws \Exception
     * @return integer file-size in byte
     */
    public function getSize()
    {
        if (isset($GLOBALS['_SERVER']['CONTENT_LENGTH'])) {
            return (int)$GLOBALS['_SERVER']['CONTENT_LENGTH'];
        } else {
            throw new RuntimeException('Getting content length is not supported.');
        }
    }

    /**
     * Get MIME type of file.
     *
     * @return string|boolean MIME type. eg, text/html, FALSE on error
     * @throws \RuntimeException
     */
    public function getMimeType()
    {
        $this->checkFileExistence();
        if (function_exists('finfo_file')) {
            $fileInfo = new \finfo();
            return $fileInfo->file($this->getFileWithAbsolutePath(), FILEINFO_MIME_TYPE);
        } elseif (function_exists('mime_content_type')) {
            return mime_content_type($this->getFileWithAbsolutePath());
        }
        return FALSE;
    }
}
