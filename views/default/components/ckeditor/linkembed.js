CKEDITOR.plugins.add('linkembed', {
	init: function (editor) {
		CKEDITOR.dialog.add('LinkEmbedDialog', function (instance) {
			return {
				title: 'Embed Link',
				contents: [{
						id: 'tab-linkembed',
						elements: [{
								id: 'input-linkembed',
								type: 'text',
								label: 'URL to Embed',
								autofocus: 'autofocus',
							}]
					}],
				onOk: function () {

					var href = this.getContentElement('tab-linkembed', 'input-linkembed').getValue();

					var a = instance.document.createElement('a');
					a.setAttribute('href', href);

					var img = instance.document.createElement('img');
					img.setAttribute('src', instance.config.linkembedPlaceholder);
					img.setAttribute('alt', 'linkembed');

					a.setHtml(img.getOuterHtml());
					instance.insertElement(a);

//					var iframe = instance.document.createElement('iframe');
//					iframe.setAttribute('src', instance.config.baseHref + 'url?iframe=1&url=' + href);
//					iframe.setAttribute('frameborder', 0);
//					iframe.setAttribute('seamless', 'seamless');
//					iframe.setAttribute('style', 'width: 100%; height: 125px;')
//
//					instance.insertElement(iframe);
				}
			};
		});

		editor.addCommand('LinkEmbed', new CKEDITOR.dialogCommand('LinkEmbedDialog', { 
//			allowedContent: 'iframe[*]'
		}));

		editor.ui.addButton('LinkEmbed', {
			label: 'Embed Link',
			command: 'LinkEmbed',
			icon: this.path + 'icons/linkembed.png',
			toolbar: 'insert,100',
			type: 'button'
		});
	}
});
