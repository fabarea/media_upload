<?php
namespace Fab\MediaUpload\Utility;

/*
 * This file is part of the Fab/MediaUpload project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class that optimize an image according to some settings.
 */
class UuidUtility implements SingletonInterface
{

    /**
     * Returns a class instance.
     *
     * @return UuidUtility
     * @throws \InvalidArgumentException
     */
    static public function getInstance()
    {
        return GeneralUtility::makeInstance(self::class);
    }

    /**
     * Validate a Fine Uploader UUID used as temporary sub-folder name.
     *
     * Must be an exact UUID-like token: no path separators, traversal, or NTFS
     * stream markers (":") that could escape typo3temp/MediaUpload.
     *
     * @param mixed $uuid
     * @return bool
     */
    public function isValid($uuid)
    {
        if (!is_string($uuid) || $uuid === '') {
            return false;
        }

        // Reject path traversal / ADS markers even before the format check.
        if (strpbrk($uuid, '/\\:') !== false || strpos($uuid, '..') !== false) {
            return false;
        }

        // Anchored: the entire value must be the UUID (unanchored allowed "/../../..").
        return (bool)preg_match('/^\w{8}-\w{4}-\w{4}-\w{4}-\w{12}$/', $uuid);
    }

}
