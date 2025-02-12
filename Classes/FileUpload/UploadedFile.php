<?php
namespace Fab\MediaUpload\FileUpload;

/*
 * This file is part of the Fab/MediaUpload project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
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
    protected string $temporaryFileNameAndPath;

    /**
     * The final file name.
     *
     * @var string
     */
    protected string $fileName;

    /**
     * The sanitized final file name for FE display.
     *
     * @var string
     */
    protected string $sanitizedFileName;

    /**
     * Size of the file if available.
     *
     * @var int
     */
    protected int $size;

    /**
     * @param string $fileName
     * @return $this
     */
    public function setFileName(string $fileName): static
    {
        $this->fileName = $fileName;
        return $this;
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * @param int $size
     * @return $this
     */
    public function setSize(int $size): static
    {
        $this->size = $size;
        return $this;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @param string $temporaryFileNameAndPath
     * @return $this
     */
    public function setTemporaryFileNameAndPath(string $temporaryFileNameAndPath): static
    {
        $this->temporaryFileNameAndPath = $temporaryFileNameAndPath;
        return $this;
    }

    /**
     * @return string
     */
    public function getTemporaryFileNameAndPath(): string
    {
        return $this->temporaryFileNameAndPath;
    }

}
