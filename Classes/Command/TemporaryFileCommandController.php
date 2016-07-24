<?php
namespace Fab\MediaUpload\Command;

/*
 * This file is part of the Fab/MediaUpload project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\MediaUpload\FileUpload\UploadManager;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\CommandController;

/**
 * Command Controller which handles actions related to Temporary Files.
 */
class TemporaryFileCommandController extends CommandController
{

    /**
     * Flush all temporary files.
     *
     * @return void
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function flushCommand()
    {
        $structure = $this->getStructureOfFiles();

        GeneralUtility::rmdir(UploadManager::UPLOAD_FOLDER, true);
        GeneralUtility::mkdir_deep(UploadManager::UPLOAD_FOLDER);

        $this->outputLine(sprintf('I have removed %s file(s).', $structure['numberOfFiles']));
    }

    /**
     * List all temporary files before flushing.
     *
     * @return void
     */
    public function listCommand()
    {

        $structure = $this->getStructureOfFiles();

        $this->outputLine(implode(PHP_EOL, $structure['files']));
        $this->outputLine();
        $this->outputLine(sprintf('%s temporary file(s).', $structure['numberOfFiles']));
    }

    /**
     * @return array
     */
    protected function getStructureOfFiles() {
        $Directory = new RecursiveDirectoryIterator(UploadManager::UPLOAD_FOLDER);
        $iterator = new RecursiveIteratorIterator($Directory);

        $counter = 0;
        $structure = [];
        /** @var SplFileInfo $file */
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $counter++;
                $structure['files'][] = $file->getPath() . DIRECTORY_SEPARATOR . $file->getFilename();
            }
        }

        $structure['numberOfFiles'] = $counter;
        return $structure;
    }

}
