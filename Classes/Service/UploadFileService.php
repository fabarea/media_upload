<?php
namespace TYPO3\CMS\MediaUpload\Service;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Fabien Udriot <fabien.udriot@typo3.org>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Media\FileUpload\UploadManager;

/**
 * Uploaded files service.
 */
class UploadFileService {

	/**
	 * Return the list of uploaded files.
	 *
	 * @param string $property
	 * @return string
	 */
	public function getUploadedFileList($property = '') {
		$parameters = GeneralUtility::_GPmerged('tx_mediaupload_pi1');
		return empty($parameters['uploadedFiles'][$property]) ? '' : $parameters['uploadedFiles'][$property];
	}

	/**
	 * Return an array of just uploaded files.
	 *
	 * @param string $property
	 * @return array
	 */
	public function getUploadedFiles($property = '') {

		$files = array();
		$uploadedFiles = GeneralUtility::trimExplode(',', $this->getUploadedFileList($property), TRUE);

		// Convert uploaded files into array
		foreach ($uploadedFiles as $uploadedFile) {
			$file = array();
			$file['name'] = $uploadedFile;
			$file['path'] = UploadManager::UPLOAD_FOLDER . '/' . $uploadedFile;
			$file['size'] = round(filesize($file['path']) / 1000);

			$files[] = $file;
		}

		return $files;
	}

	/**
	 * Count uploaded files.
	 *
	 * @param string $property
	 * @return array
	 */
	public function countUploadedFiles($property = '') {
		return count(GeneralUtility::trimExplode(',', $this->getUploadedFileList($property), TRUE));
	}
}
?>
