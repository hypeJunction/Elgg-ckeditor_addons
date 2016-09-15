<?php
require_once __DIR__ . '/autoloader.php';

elgg_register_event_handler('init', 'system', 'ckeditor_addons_init');

/**
 * Initialize the plugin
 * @return void
 */
function ckeditor_addons_init() {

	elgg_register_simplecache_view('components/ckeditor/setup.js');
	elgg_extend_view('elgg.js', 'components/ckeditor/setup.js');

	elgg_register_action('ckeditor_addons/settings/save', __DIR__ . '/actions/settings/save.php', 'admin');
	elgg_register_action('ckeditor/upload', __DIR__ . '/actions/ckeditor/upload.php');

	elgg_register_page_handler('ckeditor', 'ckeditor_addons_page_handler');

	if (elgg_is_active_plugin('hypeScraper')) {
		elgg_extend_view('elgg.css', 'components/ckeditor/linkembed.css');
		elgg_extend_view('output/longtext', 'ckeditor/linkembed');
	}

	elgg_extend_view('elgg.css', 'components/ckeditor/browser.css');

	elgg_register_plugin_hook_handler('view', 'output/longtext', 'ckeditor_addons_rewrite_asset_urls');
}

/**
 * Returns toolbar settings
 *
 * @param string $type User type 'admin'|'user'
 * @return array
 */
function ckeditor_addons_get_toolbar($type = null) {
	$toolbar_config = elgg_get_plugin_setting('toolbar_config', 'ckeditor_addons');
	if ($toolbar_config) {
		$toolbar_config = unserialize($toolbar_config);
	} else {
		$toolbar_config = array();
	}

	if (!$type) {
		$type = (elgg_is_admin_logged_in()) ? 'admin' : 'user';
	}

	$toolbar = elgg_extract($type, $toolbar_config, ckeditor_addons_get_toolbar_defaults());

	$options = ckeditor_addons_get_toolbar_options();
	foreach ($options as $group => $buttons) {
		foreach ($buttons as $key => $button) {
			if (!in_array($button, $toolbar)) {
				unset($options[$group][$key]);
			}
		}
		if (empty($options[$group])) {
			unset($options[$group]);
		} else {
			$options[$group] = array_values($options[$group]);
		}
	}

	return array_values($options);
}

/**
 * Returns buttons enabled by default
 * @return array
 */
function ckeditor_addons_get_toolbar_defaults() {
	$defaults = ['Bold', 'Italic', 'Underline', 'RemoveFormat', 'Strike', 'NumberedList', 'BulletedList', 'Undo', 'Redo', 'Link', 'Unlink', 'Image', 'Blockquote', 'Paste', 'PasteFromWord', 'Maximize'];
	return elgg_trigger_plugin_hook('toolbar:defaults', 'ckeditor', null, $defaults);
}

/**
 * Returns toolbar buttons by group
 * @return array
 */
function ckeditor_addons_get_toolbar_options() {
	$options = [
		'basicstyles' => ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', 'RemoveFormat'],
		'paragraph' => ['NumberedList', 'BulletedList', 'Outdent', 'Indent', 'Blockquote', 'CreateDiv', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', 'BidiLtr', 'BidiRtl', 'Language'],
		'clipboard' => ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', 'Undo', 'Redo'],
		'editing' => ['Find', 'Replace', 'SelectAll', 'Scayt'],
		'links' => ['Link', 'Unlink', 'Anchor'],
		'insert' => ['Image', 'LinkEmbed', 'Tooltip', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe'],
		'styles' => ['Styles', 'Format', 'Font', 'FontSize'],
		'colors' => ['TextColor', 'BGColor'],
		'tools' => ['Maximize', 'ShowBlocks'],
		'document' => ['Source', 'Templates'],
	];

	return elgg_trigger_plugin_hook('toolbar:options', 'ckeditor', null, $options);
}

/**
 * Checks if given addon is enabled
 *
 * @param string $button Button
 * @param string $type   User type
 * @return boolean
 */
function ckeditor_addons_is_enabled($button = '', $type = null) {

	$options = ckeditor_addons_get_toolbar($type);
	foreach ($options as $group => $buttons) {
		if (in_array($button, $buttons)) {
			return true;
		}
	}
	return false;
}

/**
 * Handles browser pages
 *
 * @param array $segments An array of URL segments
 * @return boolean
 */
function ckeditor_addons_page_handler($segments) {

	if (!elgg_get_plugin_setting('allow_uploads', 'ckeditor_addons')) {
		return false;
	}

	switch ($segments[0]) {
		case 'browse':
			echo elgg_view_resource('ckeditor/browse');
			return true;

		case 'image' :
			$user_guid = $segments[1];
			$hash = $segments[2];
			$ext = $segments[3];

			if (!$user_guid || !$hash) {
				header("HTTP/1.1 400 Bad Request");
				exit;
			}

			if (!in_array($ext, array('jpg', 'gif'))) {
				$ext = 'jpg';
			}

			if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && trim($_SERVER['HTTP_IF_NONE_MATCH']) == "\"$hash\"") {
				header("HTTP/1.1 304 Not Modified");
				exit;
			}

			$file = new ElggFile();
			$file->owner_guid = $user_guid;
			$file->setFilename("ckeditor/{$hash}.{$ext}");

			if (!$file->exists()) {
				header("HTTP/1.1 404 Not Found");
				exit;
			}

			$file->open('read');
			$contents = $file->grabFile();
			$file->close();

			if (md5($contents) != $hash) {
				header("HTTP/1.1 403 Forbidden");
				exit;
			}

			header("Content-type: image/jpeg");
			header("Etag: $hash");
			header('Expires: ' . date('r', time() + 864000));
			header("Pragma: public");
			header("Cache-Control: public");
			header("Content-Length: " . $file->getSize());

			ob_get_clean();

			echo $contents;
			exit;

		case 'assets' :
			array_unshift($segments, 'ckeditor');
			$view = implode('/', $segments);
			forward(elgg_get_simplecache_url($view));
			break;
	}

	return false;
}

/**
 * Rewrite static asset URLs to simplecache URLs
 *
 * @param string $hook   "view"
 * @param string $type   "output/longtext"
 * @param string $return View
 * @param array  $params Hook params
 * @return string
 */
function ckeditor_addons_rewrite_asset_urls($hook, $type, $return, $params) {

	$callback = function($m) {
		$site_url = elgg_get_site_url();
		$url = elgg_normalize_url($m[0]);
		if (strpos($url, $site_url) === 0) {
			$url = substr($url, strlen($site_url));
		}

		$segments = explode('/', parse_url($url, PHP_URL_PATH));

		if ($segments[0] == 'ckeditor' && $segments[1] == 'assets') {
			$url = elgg_get_simplecache_url(implode('/', $segments));
		}

		return elgg_normalize_url($url);
	};

	$pattern = "/(ht|f)tps?:\/\/[^\s\r\n\t<>\"\']+/";
	return preg_replace_callback($pattern, $callback, $return);
}