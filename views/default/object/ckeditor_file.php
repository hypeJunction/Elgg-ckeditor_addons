<?php

use hypeJunction\CKFile;

$entity = elgg_extract('entity', $vars);
/* @var $entity CKFile */

$hash = md5(file_get_contents($entity->getFilenameOnFilestore()));
switch ($entity->getMimeType()) {
	case 'image/gif' :
		$ext = 'gif';
		break;
	default :
		$ext = 'jpg';
}

$img = elgg_format_element('img', array(
	'src' => elgg_normalize_url("ckeditor/image/$entity->owner_guid/$hash/$ext"),
	'class' => 'ckeditor-browser-image',
	'data-callback' => elgg_extract('ckeditor_callback', $vars),
	'width' => 100,
		));

echo elgg_format_element('div', array('class' => 'elgg-photo'), $img);
