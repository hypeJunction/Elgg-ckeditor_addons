<?php

use hypeJunction\CKFile;

if (!headers_sent()) {
	header("Content-type: text/html");
}

if (!elgg_get_plugin_setting('allow_uploads', 'ckeditor_addons')) {
	echo elgg_echo('ckeditor:upload:not_allowed');
	exit;
}

$uploads = elgg_get_uploaded_files('upload');
$upload = array_shift($uploads);
/* @var $upload \Symfony\Component\HttpFoundation\File\UploadedFile */

if (!$upload) {
	echo elgg_format_element('div', [
		'class' => 'elgg-box-error',
			], elgg_echo('ckeditor:upload:fail'));
	exit;
}

if (!$upload->isValid()) {
	echo elgg_format_element('div', [
		'class' => 'elgg-box-error',
			], elgg_get_friendly_upload_error($upload->getError()));
	exit;
}

$callback = get_input('CKEditorFuncNum');

$user = elgg_get_logged_in_user_entity();

$file = new CKFile();
$file->owner_guid = $user->guid;
$file->access_id = ACCESS_PUBLIC;

$file->acceptUploadedFile($upload);

switch ($file->getMimeType()) {

	case 'image/gif' :
		$result = $file->exists();
		break;

	default :
		$max_width = (int) elgg_get_plugin_setting('upload_max_width', 'ckeditor_addons', 600);
		$result = elgg_save_resized_image($file->getFilenameOnFilestore(), null, [
			'w' => 600,
			'h' => 600,
			'square' => false,
			'upscale' => false,
		]);
		break;

}

if (!$result) {
	$file->delete();
	echo elgg_format_element('div', [
		'class' => 'elgg-box-error',
			], elgg_echo('ckeditor:upload:fail'));
	exit;
}

$hash = md5(file_get_contents($file->getFilenameOnFilestore()));
$ext = $upload->getClientOriginalExtension();

$file->transfer($file->owner_guid, "ckeditor/{$hash}.{$ext}");

$file->hash = $hash;
$file->ext = $ext;
$file->save();

$url = elgg_normalize_url("ckeditor/image/{$user->guid}/{$hash}/{$ext}");
?>
<script>
	window.parent.CKEDITOR.tools.callFunction(<?= json_encode($callback) ?>, <?= json_encode($url) ?>, '');
</script>
<?php
exit;
