<?php
/**
 * Initialize the CKEditor script
 * 
 * Doing this inline enables the editor to initialize textareas loaded through ajax
 */

?>
<script>
require(['elgg', 'elgg/ckeditor', 'jquery', 'jquery.ckeditor'], function(elgg, elggCKEditor, $) {
	$('.elgg-input-longtext:not([data-cke-init])')
		.attr('data-cke-init', true)
		.ckeditor(elggCKEditor.init, elggCKEditor.config);
});
</script>
