<?php

use hypeJunction\CKFile;

require_once __DIR__ . '/autoloader.php';

$settings = array(
	'upload_max_width' => 600,
);

foreach ($settings as $name => $value) {
	if (is_null(elgg_get_plugin_setting($name, 'ckeditor_addons'))) {
		elgg_set_plugin_setting($name, $value, 'ckeditor_addons');
	}
}

if (!update_subtype('object', 'ckeditor_file', CKFile::class)) {
	add_subtype('object', 'ckeditor_file', CKFile::class);
}