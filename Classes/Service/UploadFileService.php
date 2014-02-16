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
use TYPO3\CMS\MediaUpload\UploadedFile;

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
	 * Return an array of uploaded files, done in a previous step.
	 *
	 * @param string $property
	 * @throws \Exception
	 * @return UploadedFile[]
	 */
	public function getUploadedFiles($property = '') {

		$files = array();
		$uploadedFiles = GeneralUtility::trimExplode(',', $this->getUploadedFileList($property), TRUE);

		// Convert uploaded files into array
		foreach ($uploadedFiles as $uploadedFileName) {

			$temporaryFileNameAndPath = UploadManager::UPLOAD_FOLDER . '/' . $uploadedFileName;

			if (!file_exists($temporaryFileNameAndPath)) {
				$message = sprintf('I could not find file "%s". Something went wrong during the upload? Or is it some cache effect?', $file['path']);
				throw new \Exception($message, 1389550006);
			}
			$fileSize = round(filesize($temporaryFileNameAndPath) / 1000);

			/** @var \TYPO3\CMS\MediaUpload\UploadedFile $uploadedFile */
			$uploadedFile = GeneralUtility::makeInstance('TYPO3\CMS\MediaUpload\UploadedFile');
			$uploadedFile->setTemporaryFileNameAndPath($temporaryFileNameAndPath)
				->setFileName($uploadedFileName)
				->setSize($fileSize);

			$files[] = $uploadedFile;
		}

		return $files;
	}

	/**
	 * Return the first uploaded files, done in a previous step.
	 *
	 * @param string $property
	 * @return array
	 */
	public function getUploadedFile($property = '') {
		$uploadedFile = array();

		$uploadedFiles = $this->getUploadedFiles($property);
		if (!empty($uploadedFiles)) {
			$uploadedFile = current($uploadedFiles);
		}

		return $uploadedFile;
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
