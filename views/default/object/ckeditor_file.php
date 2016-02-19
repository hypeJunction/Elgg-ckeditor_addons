<?php

use hypeJunction\CKFile;

$entity = elgg_extract('entity', $vars);
/* @var $entity CKFile */

$img = elgg_format_element('img', array(
	'src' => elgg_normalize_url("ckeditor/image/$entity->owner_guid/$entity->hash/$entity->ext"),
	'class' => 'ckeditor-browser-image',
	'data-callback' => elgg_extract('ckeditor_callback', $vars),
	'width' => 100,
		));

echo elgg_format_element('div', array('class' => 'elgg-photo'), $img);
