<?php
namespace Fab\MediaUpload;

    /**
     * This file is part of the TYPO3 CMS project.
     *
     * It is free software; you can redistribute it and/or modify it under
     * the terms of the GNU General Public License, either version 2
     * of the License, or any later version.
     *
     * For the full copyright and license information, please read the
     * LICENSE.txt file that was distributed with this source code.
     *
     * The TYPO3 project - inspiring people to share!
     */

/**
 * Class that represents an Uploaded File
 */
class UploadedFile
{

    /**
     * The temporary file name and path on the server.
     *
     * @var string
     */
    protected $temporaryFileNameAndPath;

    /**
     * The final file name.
     *
     * @var string
     */
    protected $fileName;

    /**
     * The sanitized final file name for FE display.
     *
     * @var string
     */
    protected $sanitizedFileName;

    /**
     * Size of the file if available.
     *
     * @var int
     */
    protected $size;

    /**
     * @param string $fileName
     * @return $this
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
        return $this;
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @param int $size
     * @return $this
     */
    public function setSize($size)
    {
        $this->size = $size;
        return $this;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param string $temporaryFileNameAndPath
     * @return $this
     */
    public function setTemporaryFileNameAndPath($temporaryFileNameAndPath)
    {
        $this->temporaryFileNameAndPath = $temporaryFileNameAndPath;
        return $this;
    }

    /**
     * @return string
     */
    public function getTemporaryFileNameAndPath()
    {
        return $this->temporaryFileNameAndPath;
    }

    /**
     * @return string
     */
    public function getSanitizedFileName()
    {
        return preg_replace('/^\w{8}-\w{4}-\w{4}-\w{4}-\w{12}-/', '', $this->fileName);
    }

}
