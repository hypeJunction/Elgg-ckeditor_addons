define(function (require) {

	var elggCKEditor = require('elgg/ckeditor');
	var $ = require('jquery');

	$(document).on('reset', 'form', function () {
		$(this).find('[data-cke-init]').each(function () {
			if ($(this).data('ckeditorInstance')) {
				$(this).ckeditorGet().setData('');
			}
		});
	});

	return function (selector) {
		var selector = selector || '.elgg-input-longtext:not([data-cke-init])';
		$(selector)
				.attr('data-cke-init', true)
				.ckeditor(elggCKEditor.init, elggCKEditor.config);
	};
});
