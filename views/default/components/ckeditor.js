define(function (require) {

	var elggCKEditor = require('elgg/ckeditor');
	var $ = require('jquery');

	$('.elgg-input-longtext:not([data-cke-init])')
			.attr('data-cke-init', true)
			.ckeditor(elggCKEditor.init, elggCKEditor.config);
});
