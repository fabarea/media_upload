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

		# Notice code is simplified for demo purposes

		/** @var $uploadManager \TYPO3\CMS\Media\FileUpload\UploadManager */
		$uploadManager = GeneralUtility::makeInstance('TYPO3\CMS\Media\FileUpload\UploadManager');

		$uploadedFile = $uploadManager->handleUpload();


.. _Fine Uploader: http://fineuploader.com/


Upload Widget
-------------------

You can make use of a Media Upload widget. Syntax is as follows::


	<mu:widget.mediaUpload />

	{namespace mu=TYPO3\CMS\MediaUpload\ViewHelpers}

	# With some attribute
	<mu:widget.mediaUpload allowedExtensions="jpg, png" storage="1"/>


	# Required attributes:
	# --------------------
	#
	# There are no attribute required.

	# Default values:
	# ---------------
	#
	# The Storage identifier to get some automatic settings, such as allowedExtensions, default NULL.
	# storage = 1
	#
	# Allowed extension to be uploaded, default NULL.
	# allowedExtensions = "jpg, png"
	#
	# Maximum size allowed by the plugin, default 0.
	# maximumSize =
	#
	# The unit used for computing the maximumSize, default Mo.
	# sizeUnit = Mo
	#
	# Maximum items to be uploaded, default 10.
	# maximumItems = 10


