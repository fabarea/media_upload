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
     * @param string $uuid
     * @return bool
     */
    public function isValid($uuid)
    {
        return preg_match('/^\w{8}-\w{4}-\w{4}-\w{4}-\w{12}/', $uuid);
    }

}
