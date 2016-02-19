<?php

use hypeJunction\CKFile;
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

$callback = get_input('CKEditorFuncNum');

$user = elgg_get_logged_in_user_entity();

$file = new CKFile();
$file->owner_guid = $user->guid;

$mimetype = $file->detectMimeType($_FILES['upload']['tmp_name'], $_FILES['upload']['type']);

switch ($mimetype) {
	case 'image/png' :
	case 'image/jpeg' :
	case 'image/jpg' :
		$ext = 'jpg';
		$max_width = (int) elgg_get_plugin_setting('upload_max_width', 'ckeditor_addons', 600);
		$contents = get_resized_image_from_existing_file($_FILES['upload']['tmp_name'], $max_width, $max_width);
		break;

	case 'image/gif' :
		$ext = 'gif';
		$contents = file_get_contents($_FILES['upload']['tmp_name']);
		break;
}

if (!$contents) {
	echo elgg_echo('ckeditor:upload:fail');
	exit;
}

$hash = md5($contents);
$file->setFilename("ckeditor/{$hash}.{$ext}");

$file->open('write');
$file->write($contents);
$file->close();

$file->access_id = ACCESS_PUBLIC;
$file->hash = $hash;
$file->ext = $ext;

$file->setMimeType($mimetype);
$file->simpletype = 'image';
$file->originalfilename = $_FILES['upload']['name'];

if (!$file->exists() || !$file->save()) {
	$file->delete();
	echo elgg_echo('ckeditor:upload:fail');
	exit;
}

$url = elgg_normalize_url("ckeditor/image/{$user->guid}/{$hash}/{$ext}");
?>
<script>
	window.parent.CKEDITOR.tools.callFunction('<?php echo $callback ?>', '<?php echo $url ?>', '');
</script>
<?php
exit;
