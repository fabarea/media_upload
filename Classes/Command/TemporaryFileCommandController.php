<?php
namespace Fab\MediaUpload\Command;

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

use DirectoryIterator;
use TYPO3\CMS\Extbase\Mvc\Controller\CommandController;
use TYPO3\CMS\Media\FileUpload\UploadManager;

/**
 * Command Controller which handles actions related to Temporary Files.
 */
class TemporaryFileCommandController extends CommandController
{

    /**
     * Flush all temporary files.
     *
     * @return void
     */
    public function flushCommand()
    {
        $iterator = new DirectoryIterator(UploadManager::UPLOAD_FOLDER);

        $counter = 0;
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $counter++;
                unlink(UploadManager::UPLOAD_FOLDER . DIRECTORY_SEPARATOR . $file->getFilename());
            }
        }

        $this->outputLine(sprintf('I have removed %s file(s).', $counter));
    }

    /**
     * List all temporary files before flushing.
     *
     * @return void
     */
    public function listCommand()
    {
        $iterator = new DirectoryIterator(UploadManager::UPLOAD_FOLDER);

        $counter = 0;
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $counter++;
                $this->outputLine($file->getFilename());
            }
        }

        $this->outputLine();
        $this->outputLine(sprintf('%s temporary file(s).', $counter));
    }

}
