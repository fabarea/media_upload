==========================
Media Upload for TYPO3 CMS
==========================

This extension for `TYPO3 CMS`_ provides a Fluid widget for (mass) uploading media on the Frontend using HTML5 techniques.
Once selected by the User, the Media will be directly uploaded to a temporary space within ``typo3temp``.
After the form is posted, uploaded File can be retrieved by an ``UploadService``.

If the form has a "show" step before the final submission, the uploaded images can be displayed by another widget.

The file upload is handled by Fine Uploader which is a Javascript plugin aiming to bring a user-friendly file-uploading experience over the web.
The plugin relies on HTML5 technology which enables Drag & Drop from the Desktop. File transfer is achieved by Ajax if supported. If not,
a fall back method with classical file upload is used by posting the file. (Though, the legacy approach still need to be tested more thoroughly).

.. _Fine Uploader: http://fineuploader.com/
.. _TYPO3 CMS: http://typo3.org/


.. image:: https://raw.github.com/fabarea/media_upload/master/Documentation/Upload-01.png

After a file has been uploaded, the user can validate and possibly remove it from the list.

.. image:: https://raw.github.com/fabarea/media_upload/master/Documentation/Upload-02.png

Installation
============

The installation is completed in two steps. Install the extension as normal in the Extension Manager.
Then, load the JavaScript / CSS for the pages that will contain the upload widget.
The extension assumes jQuery to be loaded::


	# CSS
	EXT:media_upload/Resources/Public/Build/media_upload.min.css

	# JavaScript
	EXT:media_upload/Resources/Public/Build/media_upload.min.js


Upload Widget
=============

You can make use of a Media Upload widget. Syntax is as follows::


	# Minimum syntax
	<mu:widget.upload storage="1"/>

	{namespace mu=Fab\MediaUpload\ViewHelpers}

	# With some more attributes... We assume we have a property "images" in our model
	# and this value could be something different like "documents" or whatever.
	<mu:widget.upload allowedExtensions="jpg, png" storage="1" property="images"/>


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
	# property = foo


To see the uploaded images in a second step::

	<mu:widget.showUploaded />

	<mu:widget.showUploaded property="images" />


	# The property to be used for retrieving the uploaded images, default NULL.
	# property = foo


Upload Service
==============

Once files have been uploaded on the Frontend and are placed in a temporary directory, we have to
to retrieve them and store them into their final location. This code can be used in your controller::

	/**
	 * @var \Fab\MediaUpload\Service\UploadFileService
	 * @inject
	 */
	protected $uploadFileService;

	/**
	 * @return void
	 */
	public function createAction() {

		/** @var array $uploadedFiles */
		$uploadedFiles = $this->uploadFileService->getUploadedFiles()

		# A property name is needed in case specified in the Fluid Widget
		# <mu:widget.upload property="images"/>
		$uploadedFiles = $this->uploadFileService->getUploadedFiles('images')

		# Process uploaded files and move them into a Resource Storage (FAL)
		foreach($uploadedFiles as $uploadedFile) {

			/** @var \Fab\MediaUpload\UploadedFile $uploadedFile */
			$uploadedFile->getTemporaryFileNameAndPath();

			$storage = ResourceFactory::getInstance()->getStorageObject(1);

			/** @var File $file */
			$file = $storage->addFile(
				$uploadedFile->getTemporaryFileNameAndPath(),
				$storage->getRootLevelFolder(),
				$uploadedFile->getFileName(),
				'changeName'
			);

			# Note: Use method `addUploadedFile` instead of `addFile` if file is uploaded
			# via a regular "input" control instead of the upload widget (fine uploader plugin)
			# $file = $storage->addUploadedFile()

			$fileReference = $this->objectManager->get(\YourVendor\YourExtensionKey\Domain\Model\FileReference::class);
			$fileReference->setFile($file);
			$yourDomainObject->addImages($fileReference);
		}
		...
	}



File Configuration in FAL
=========================

How to configure a field / property of type file?

SQL
---

::

	CREATE TABLE tx_domain_model_foo (
	        images varchar(255) DEFAULT '' NOT NULL,
	);


TCA
---

::

    $TCA['tx_domain_model_foo'] = array(
        'images' => array(
                'label' => 'Images',
                'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
                    'images',
                    array(
                        'appearance' => array(
                                'createNewRelationLinkTitle' => 'LLL:EXT:cms/locallang_ttc.xlf:images.addFileReference'
                        ),
                    'minitems' => 0,
                    'maxitems' => 1,
                ),
                $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']
            ),
        ),
    );


Model
-----

Your domain model, should then contain the method ``addImages`` for the purpose of ``$yourDomainObject->addImages($fileReference);``. See code above in the Upload Service.

::

    /**
     * Images
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     */
    protected $images;

    public function addImages(\TYPO3\CMS\Extbase\Domain\Model\FileReference $image) {
        $this->images->attach($image);
    }


File Reference Model
--------------------

We must extend the FileReference for the purpose of ``$fileReference->setFile($file);```. See code above in the Upload Service.

::

	namespace YourVendor\YourExtensionKey\Domain\Model;

	class FileReference extends \TYPO3\CMS\Extbase\Domain\Model\FileReference {

	    /**
	     * @params \TYPO3\CMS\Core\Resource\File $file
	     */
		public function setFile(\TYPO3\CMS\Core\Resource\File $file) {
			$this->originalFileIdentifier = (int)$file->getUid();
		}
	}


TypoScript
----------

Finally we must configure the persistence layer of Extbase.


::

	config.tx_extbase {
		persistence {
			# Enable this if you need the reference index to be updated
			updateReferenceIndex = 1
			classes {
				YourVendor\YourExtensionKey\Domain\Model\FileReference {
					mapping {
						tableName = sys_file_reference
						columns {
							uid_local.mapOnProperty = originalFileIdentifier
						}
					}
				}
			}
		}
		objects {
			TYPO3\CMS\Extbase\Domain\Model\FileReference.className = YourVendor\YourExtensionKey\Domain\Model\FileReference
		}
	}


Security
========

By default Media Upload require a Frontend User to be authenticated. This can be adjusted according to your needs by selecting
only allowed Frontend User Group. This behaviour can be configured by TypoScript.

::

	plugin.tx_mediaupload {

		settings {

			# "*", means every authenticated User can upload. (default)
			# "1,2", means every User belonging of Frontend Groups 1 and 2 are allowed.
			# no value, everybody can upload. No authentication is required. Caution!!

			allowedFrontendGroups = *
		}
	}

Scheduler tasks
===============

The temporary files contained within ``typo3temp`` can be flushed from time to time.
It could be files are left aside if the user has not finalized the upload.
The Command can be used via a scheduler task with a low redundancy, once per week as instance::

	# List all temporary files
	./typo3/cli_dispatch.phpsh extbase temporaryFile:list

	# Remove them.
	./typo3/cli_dispatch.phpsh extbase temporaryFile:flush


Building assets in development
==============================

The extension provides JS / CSS bundles which included all the necessary code. If you need to make a new build for those JS / CSS files,
consider that `Bower`_ and `Grunt`_ must be installed on your system as prerequisite.

Install the required Web Components::

	cd typo3conf/ext/media_upload

	# This will populate the directory Resources/Public/WebComponents.
	bower install

	# Install the necessary NodeJS package.
	npm install

Then you must build Fine Uploader from the source::

	cd Resources/Private/BowerComponents/fine-uploader

	# Install the necessary NodeJS package inside "fine-uploader".
	npm install

	# Do the packaging works. It will create a "_dist" directory containing the build.
	grunt package

Finally, you can run the Grunt of the extension to generate a build::

	cd typo3conf/ext/media_upload
	grunt build

While developing, you can use the ``watch`` which will generate the build as you edit files::

	grunt watch


.. _Bower: http://bower.io/
.. _Grunt: http://gruntjs.com/
