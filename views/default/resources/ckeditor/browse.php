<?php
elgg_gatekeeper();

$assets = '';
if (elgg_is_admin_logged_in()) {
	$views = elgg_list_views();
	$images = array_filter($views, function($view) {
		$extension = pathinfo($view, PATHINFO_EXTENSION);
		return 0 === strpos($view, 'ckeditor/assets/') && in_array($extension, ['jpg', 'gif', 'png', 'svg']);
	});

	if (!empty($images)) {
		$assets .= '<ul class="elgg-gallery">';
		foreach ($images as $image) {
			$assets .= '<li class="elgg-item">';
			$img = elgg_format_element('img', array(
				'src' => elgg_normalize_url($image),
				'class' => 'ckeditor-browser-image',
				'data-callback' => get_input('CKEditorFuncNum'),
				'width' => 100,
			));

			$assets .= elgg_format_element('div', array('class' => 'elgg-photo'), $img);
			$assets .= '</li>';
		}
		$assets .= '</ul>';

		$assets = elgg_view_module('info', elgg_echo('ckeditor:browse:assets'), $assets);
	}
}

$user = elgg_get_logged_in_user_entity();

$images = elgg_list_entities(array(
	'types' => 'object',
	'subtypes' => 'ckeditor_file',
	'owner_guids' => (int) $user->guid,
	'limit' => 20,
	'pagination' => elgg_is_active_plugin('hypeLists'),
	'pagination_type' => 'default',
	'list_type' => 'gallery',
	'ckeditor_callback' => get_input('CKEditorFuncNum'),
	'no_results' => elgg_echo('ckeditor:browse:no_uploads'),
		));

$images = elgg_view_module('info', elgg_echo('ckeditor:browse:uploads'), $images);

$head = elgg_view('page/elements/head', array('title' => ''));
$foot = elgg_view('page/elements/foot');
echo elgg_view('page/elements/html', array(
	'head' => $head,
	'body' => elgg_format_element('div', array('class' => 'ckeditor-addons-browser'), $assets . $images . $foot),
));
?>
<script>
	require(['jquery'], function ($) {
		$(document).on('click', '.ckeditor-browser-image[data-callback]', function (e) {
			e.preventDefault();
			window.opener.CKEDITOR.tools.callFunction($(this).data('callback'), $(this).attr('src'), '');
			window.close();
		});
	});
</script>