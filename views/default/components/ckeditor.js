define(function (require) {

	var elggCKEditor = require('elgg/ckeditor');
	var $ = require('jquery');

	return function (selector) {
		var selector = selector || '.elgg-input-longtext:not([data-cke-init])';
		$(selector)
				.attr('data-cke-init', true)
				.ckeditor(elggCKEditor.init, elggCKEditor.config);
	};
});
