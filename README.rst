============================
Media Upload for TYPO3 CMS
============================


File Upload API
=================

File upload is handled by `Fine Uploader`_ which is a Javascript plugin aiming to bring a user-friendly file-uploading experience over the web.
The plugin relies on HTML5 technology which enables Drag & Drop from the Desktop. File transfer is achieved by Ajax if supported. If not,
a fall back method with classical file upload is used by posting the file. (Though, the legacy approach still need to be tested more thoroughly).

On the server side, there is an API for file upload which handles transparently whether the file come from an XHR request or a Post request.

::

		# Notice code is simplified from the real implementation.
		# For more detail check EXT:media/Classes/Controller/AssetController.php @ uploadAction

		/** @var $uploadManager \TYPO3\CMS\Media\FileUpload\UploadManager */
		$uploadManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Media\FileUpload\UploadManager');
		try {
			/** @var $uploadedFileObject \TYPO3\CMS\Media\FileUpload\UploadedFileInterface */
			$uploadedFileObject = $uploadManager->handleUpload();
		} catch (\Exception $e) {
			$response = array('error' => $e->getMessage());
		}

		$targetFolderObject = \TYPO3\CMS\Media\ObjectFactory::getInstance()->getContainingFolder();
		$newFileObject = $targetFolderObject->addFile($uploadedFileObject->getFileWithAbsolutePath(), $uploadedFileObject->getName());

.. _Fine Uploader: http://fineuploader.com/



Carousel Widget
-------------------

By default, the View Helper generates a Carousel Gallery based on the markup of `Twitter Bootstrap`_
and is assuming jQuery to be loaded. Syntax is as follows::

	# Note categories attribute can be an array categories="{1,3}"
	<mu:widget.mediaUpload />
	{namespace mu=TYPO3\CMS\MediaUpload\ViewHelpers}


	# Required attributes:
	# --------------------
	#
	# No attribute is required. However if you don't define a category *all images* will be displayed from the repository. It may take long!!

	# Default values:
	# ---------------
	#
	# Max height of the image
	# height = 600
	#
	# Max width of the image
	# width = 600
	#
	# Categories to be taken as filter.
	# categories = array()
	#
	# Interval value of time between the slides. "O" means no automatic sliding.
	# interval = 0
	#
	# Whether to display the title and description or not.
	# caption = true
	#
	# The field name to sort out.
	# sort =
	#
	# The direction to sort.
	# order = asc


The underlying template can be overridden by TypoScript. The default configuration looks as::

	config.tx_extbase {
		view {
			widget {
				TYPO3\CMS\Media\ViewHelpers\Widget\CarouselViewHelper {
					# Assuming a template file is under ViewHelpers/Widget/Carousel/Index.html
					templateRootPath = EXT:media/Resources/Private/Templates
				}
			}
		}
	}

.. _Twitter Bootstrap: http://twitter.github.io/bootstrap/examples/carousel.html

