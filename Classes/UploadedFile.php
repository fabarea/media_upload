<?php
namespace TYPO3\CMS\MediaUpload;
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

/**
 * Class that represents an Uploaded File
 */
class UploadedFile {

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
	 * Size of the file if available.
	 *
	 * @var int
	 */
	protected $size;

	/**
	 * @param string $fileName
	 * @return $this
	 */
	public function setFileName($fileName) {
		$this->fileName = $fileName;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getFileName() {
		return $this->fileName;
	}

	/**
	 * @param int $size
	 * @return $this
	 */
	public function setSize($size) {
		$this->size = $size;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getSize() {
		return $this->size;
	}

	/**
	 * @param string $temporaryFileNameAndPath
	 * @return $this
	 */
	public function setTemporaryFileNameAndPath($temporaryFileNameAndPath) {
		$this->temporaryFileNameAndPath = $temporaryFileNameAndPath;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getTemporaryFileNameAndPath() {
		return $this->temporaryFileNameAndPath;
	}


}

?>
