define(function (require) {
	var elgg = require('elgg');
	var $ = require('jquery');
	require('jquery.ckeditor');

	var CKEDITOR = require('ckeditor');

	$.each(elgg.ckeditor.plugins, function (index, plugin) {
		var names = plugin.names || index,
				path = plugin.path,
				fileName = plugin.filename || '';

		if (names && path) {
			CKEDITOR.plugins.addExternal(names, path, fileName);
		}
	});

	var elggCKEditor = {
		/**
		 * Toggles the CKEditor
		 *
		 * @param {Object} event
		 * @return void
		 */
		toggleEditor: function (event) {
			event.preventDefault();

			var target = $(this).attr('href');

			if (!$(target).data('ckeditorInstance')) {
				$(target).ckeditor(elggCKEditor.init, elggCKEditor.config);
				$(this).html(elgg.echo('ckeditor:html'));
			} else {
				$(target).ckeditorGet().destroy();
				$(this).html(elgg.echo('ckeditor:visual'));
			}
		},
		/**
		 * Initializes the ckeditor module
		 *
		 * @return void
		 */
		init: function (textarea) {
			// show the toggle-editor link which is hidden by default, so it will only show up if the editor is correctly loaded
			$('.ckeditor-toggle-editor[href="#' + textarea.id + '"]').show();
		},
		/**
		 * CKEditor has decided using width and height as attributes on images isn't
		 * kosher and puts that in the style. This adds those back as attributes.
		 * This is from this patch: http://dev.ckeditor.com/attachment/ticket/5024/5024_5.patch
		 * 
		 * @param {Object} event
		 * @return void
		 */
		fixImageAttributes: function (event) {
			event.editor.dataProcessor.htmlFilter.addRules({
				elements: {
					img: function (element) {
						var style = element.attributes.style;
						if (style) {
							var match = /(?:^|\s)width\s*:\s*(\d+)px/i.exec(style);
							var width = match && match[1];
							if (width) {
								element.attributes.width = width;
							}
							match = /(?:^|\s)height\s*:\s*(\d+)px/i.exec(style);
							var height = match && match[1];
							if (height) {
								element.attributes.height = height;
							}
						}
					}
				}
			});
		},
		/**
		 * CKEditor configuration
		 *
		 * You can find configuration information here:
		 * http://docs.ckeditor.com/#!/api/CKEDITOR.config
		 */
		config: require('elgg/ckeditor/config')

	};

	CKEDITOR.on('dialogDefinition', function (ev) {
	CKEDITOR.on('dialogDefinition', function (ev) {
		// Take the dialog name and its definition from the event data.
		var dialogName = ev.data.name;
		var dialogDefinition = ev.data.definition;

		// Check if the definition is from the dialog we are interested in (the 'link' dialog)
		if (dialogName == 'link') {

			var targetTab = dialogDefinition.getContents('target');
			var removeTargetTypes = ['frame', 'popup', '_top', '_parent', '_self'];
			targetTab.get('linkTargetType')['items'] = targetTab.get('linkTargetType')['items'].filter(function (e) {
				return removeTargetTypes.indexOf(e[1]) === -1;
			});

			var removeAttr = ['advId', 'advLangDir', 'advAccessKey', 'advName', 'advLangCode', 'advTabIndex', 'advContentType', 'advCSSClasses', 'advCharset', 'advStyles', 'advRel'];
			$.each(removeAttr, function (index, value) {
				dialogDefinition.getContents('advanced').remove(value);
			});
		}
	});

	CKEDITOR.on('instanceReady', elggCKEditor.fixImageAttributes);

	// Live handlers don't need to wait for domReady and only need to be registered once.
	$('.ckeditor-toggle-editor').live('click', elggCKEditor.toggleEditor);

	return elggCKEditor;
});
