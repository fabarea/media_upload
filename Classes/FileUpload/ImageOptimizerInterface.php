<?php
namespace Fab\MediaUpload\FileUpload;

/*
 * This file is part of the Fab/MediaUpload project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * A interface for optimizing a file upload.
 */
interface ImageOptimizerInterface
{

    /**
     * Optimize the given uploaded image
     *
     * @param \Fab\MediaUpload\FileUpload\UploadedFileInterface $uploadedFile
     * @return \Fab\MediaUpload\FileUpload\UploadedFileInterface
     */
    public function optimize($uploadedFile);
}
