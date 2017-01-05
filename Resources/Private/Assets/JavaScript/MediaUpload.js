/**
 * Media Upload
 */
(function($) {
	$(function() {

		/**
		 * @returns {string}
		 */
		var getUri = function() {
			var uri = window.location.pathname; // avoid bad surprise if root page is redirected
			if (/index.php/.test(window.location.pathname)) {
				uri += window.location.search;
			}
			return uri;
		};

		/**
		 * Ony works if MediaUpload is defined.
		 */
		if (typeof(MediaUpload) === 'object') {

			for (var i = 0; i < MediaUpload.instances.length; i++) {
				var instance = MediaUpload.instances[i];
				var settings = instance.settings;

				$('#jquery-wrapped-fine-uploader-' + instance.id)
					.fineUploader({
						multiple: true,
						debug: true,
						template: "media-upload-template",
						classes: {
							success: 'alert alert-success',
							fail: 'alert alert-error'
						},
						validation: {
							allowedExtensions: settings.allowedExtensions,
							itemLimit: settings.maximumItems,
							sizeLimit: settings.maximumSize // bytes
						},
						messages: settings.messages,
						showMessage: function(message) {
							alert(message);
						},

						// optional feature
						deleteFile: {
							enabled: true,
							method: "POST",
							endpoint: getUri(),
							params: {
								type: 1386871774
							}
						},
						request: {
							endpoint: getUri()
						}
					})
					.on('deleteComplete', function() {
						// Look up for successful uploaded files and feed a hidden field.
						var uploadedFiles = [];
						$('#qq-upload-list-' + settings.uniqueId)
							.find('li.alert-success')
							.find('.view-btn')
							.each(function(index, element) {
								var uri = $(element).attr('href');
								var basename = uri.replace(/.*\//, "");
								uploadedFiles.push(basename);
							});

						$('#uploaded-files-' + settings.property).val(uploadedFiles.join(','));

					})
					.on('submit', function() {
						var params = {};
						var parameterPrefix = 'tx_mediaupload_pi1';
						params[parameterPrefix + '[storageIdentifier]'] = settings.storage;
						params['type'] = '1386871773';
						$(this).fineUploader('setParams', params);
					})
					.on('complete', function(event, id, fileName, responseJSON) {
						var uniqueId = settings.uniqueId;
						var property = settings.property;
						var $fileEl = $(this).fineUploader("getItemByFileId", id),
							$viewBtn = $fileEl.find(".view-btn");

						if (responseJSON.success) {
							$viewBtn.removeClass('hide');
							$viewBtn.attr("href", responseJSON.viewUrl);

							// Look up for successful uploaded files and feed a hidden field.
							var uploadedFiles = [];
							$('#qq-upload-list-' + uniqueId)
								.find('li.alert-success')
								.find('.view-btn')
								.each(function(index, element) {
									var uri = $(element).attr('href');
									var basename = uri.replace("/typo3temp/MediaUpload/", "");
									uploadedFiles.push(basename);
								});

							$('#uploaded-files-' + property).val(uploadedFiles.join(','));
						}
					});
			} // end for
		} // end if


	});
})(jQuery);
