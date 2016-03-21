<?php
/**
 * Initialize the CKEditor script
 * 
 * Doing this inline enables the editor to initialize textareas loaded through ajax
 */
?>
<script>
	require(['components/ckeditor'], function(func) {
		func.call();
	});
</script>
