<?php

if (!headers_sent()) {
	header("Content-type: text/html");
}

if (!elgg_get_plugin_setting('allow_uploads', 'ckeditor_addons')) {
	echo elgg_echo('ckeditor:upload:not_allowed');
	exit;
}

if ($_FILES['upload']['error'] != UPLOAD_ERR_OK) {
	echo elgg_get_friendly_upload_error($_FILES['upload']['error']);
	exit;
}

$user = elgg_get_logged_in_user_entity();

$max_width = (int) elgg_get_plugin_setting('upload_max_width', 'ckeditor_addons', 600);
$resized = get_resized_image_from_existing_file($_FILES['upload']['tmp_name'], $max_width, $max_width);

if (!$resized) {
	echo elgg_echo('ckeditor:upload:fail');
	exit;
}

$hash = md5($resized);

$file = new ElggFile();
$file->owner_guid = $user->guid;
$file->setFilename("ckeditor/{$hash}.jpg");
$file->open('write');
$file->write($resized);
$file->close();

$callback = get_input('CKEditorFuncNum');
$url = elgg_normalize_url("ckeditor/image/{$user->guid}/{$hash}");

?>
<script>
	window.parent.CKEDITOR.tools.callFunction('<?php echo $callback ?>', '<?php echo $url ?>', '');
</script>
<?php
exit;