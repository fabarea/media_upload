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
						multiple: (settings.maximumItems > 1),
						debug: false,
						template: "media-upload-template-" + settings.uniqueId,
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
					.on('deleteComplete', {_settings: settings}, function(event) {
						// Look up for successful uploaded files and feed a hidden field.
						var uploadedFiles = [];
						$('#qq-upload-list-' + event.data._settings.uniqueId)
							.find('li.alert-success')
							.find('.view-btn')
							.each(function(index, element) {
								var uri = $(element).attr('href');
								var basename = uri.replace(/.*\//, "");
								uploadedFiles.push(basename);
							});

						$('#uploaded-files-' + event.data._settings.property).val(uploadedFiles.join(','));

					})
					.on('submit', {_settings: settings}, function(event) {
						var params = {};
						var parameterPrefix = 'tx_mediaupload_pi1';
						params[parameterPrefix + '[storageIdentifier]'] = event.data._settings.storage;
						params['type'] = '1386871773';
						$(this).fineUploader('setParams', params);
					})
					.on('complete', {_settings: settings}, function(event, id, fileName, responseJSON) {
						var uniqueId = event.data._settings.uniqueId;
						var property = event.data._settings.property;
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
