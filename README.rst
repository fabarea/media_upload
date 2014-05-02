==========================
Media Upload for TYPO3 CMS
==========================

Media Upload provides a Fluid widget for uploading a Media on the Frontend. Once selected by the User, the Media will be directly uploaded
to a temporary space within ``typo3temp``. After the form is posted, uploaded File can be retrieved by an ``UploadService``.

If the form has a "show" step, uploaded images, can be displayed by another widget.


File Upload API
===============

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

Upload Service
==============

To retrieve the uploaded images within your controller::

	/**
	 * @var \TYPO3\CMS\MediaUpload\Service\UploadFileService
	 * @inject
	 */
	protected $uploadFileService;

	/**
	 * @return void
	 */
	public function createAction() {
		$this->uploadFileService->getUploadedFiles()
	}


Security
========

It can be tell what FE Group is authorized to upload to what storages (not implemented).

Scheduler tasks
===============

The space within ``typo3temp`` can be flushed sometimes if User does not finalize their upload (not implemented).


Upload Widget
=============

You can make use of a Media Upload widget. Syntax is as follows::


	<mu:widget.upload storage="1"/>

	{namespace mu=TYPO3\CMS\MediaUpload\ViewHelpers}

	# With some attribute
	<mu:widget.upload allowedExtensions="jpg, png" storage="1" property="foo"/>


	# Required attributes:
	# --------------------
	#
	# - storage

	# Default values:
	# ---------------
	#
	# The Storage identifier to get some automatic settings, such as allowedExtensions, default NULL.
	# storage = 1
	#
	# Allowed extension to be uploaded. Override the allowed extension list from the storage. default NULL.
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
	#
	# The property to be used for retrieving the uploaded images, default NULL.
	# properties = foo


To see the uploaded images in a second step::

	<mu:widget.showUploaded />

	<mu:widget.showUploaded property="foo" />


	# The property to be used for retrieving the uploaded images, default NULL.
	# properties = foo

