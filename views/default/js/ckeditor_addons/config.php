<?php
$path = elgg_normalize_url('mod/ckeditor/vendors/ckeditor/');
$config = [
	'toolbar' => ckeditor_addons_get_toolbar(),
	'allowedContent' => true,
	'baseHref' => elgg_get_site_url(),
	'removePlugins' => ['tabletools', 'resize'],
	'extraPlugins' => ['blockimagepaste'],
	'defaultLanguage' => 'en',
	'language' => get_language(),
	'skin' => 'moono',
	'uiColor' => '#EEEEEE',
	'contentsCss' => elgg_get_simplecache_url('css', 'elgg/wysiwyg.css'),
	'disableNativeSpellChecker' => false,
	'disableNativeTableHandles' => false,
	'removeDialogTabs' => ['image:advanced', 'image:Link', 'link:advanced', 'link:target'],
];

$plugins = ['blockimagepaste' => ['path' => elgg_normalize_url('mod/ckeditor/views/default/js/elgg/ckeditor/blockimagepaste.js')]];

if (ckeditor_addons_is_enabled('Image')) {
	if (elgg_get_plugin_setting('allow_uploads', 'ckeditor_addons')) {
		$config = array_merge($config, [
			'filebrowserBrowseUrl' => elgg_normalize_url('ckeditor/browse'),
			'filebrowserUploadUrl' => elgg_add_action_tokens_to_url(elgg_normalize_url('action/ckeditor/upload')),
			'filebrowserImageWindowWidth' => '640',
			'filebrowserImageWindowHeight' => '480'
		]);
	}
}

if (ckeditor_addons_is_enabled('LinkEmbed') && elgg_is_active_plugin('hypeScraper')) {
	$config['extraPlugins'][] = 'linkembed';
	$plugins['linkembed'] = ['path' => elgg_normalize_url('mod/ckeditor_addons/views/default/js/ckeditor_addons/linkembed.js')];
}

if (ckeditor_addons_is_enabled('Tooltip')) {
	$config['extraPlugins'][] = 'tooltip';
	$plugins['tooltip'] = ['path' => elgg_normalize_url('mod/ckeditor_addons/views/default/js/ckeditor_addons/tooltip.js')];
}

$config['removePlugins'] = implode(',', $config['removePlugins']);
$config['extraPlugins'] = implode(',', $config['extraPlugins']);
$config['removeDialogTabs'] = implode(';', $config['removeDialogTabs']);

?>

//<script>

	elgg.ckeditor = elgg.ckeditor || {};
	CKEDITOR_BASEPATH = '<?php echo $path ?>';
	elgg.ckeditor.config = <?php echo json_encode($config) ?>;
	elgg.ckeditor.plugins = <?php echo json_encode($plugins) ?>;
	