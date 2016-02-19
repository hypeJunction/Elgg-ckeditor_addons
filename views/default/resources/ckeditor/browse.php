<?php

elgg_gatekeeper();

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

$head = elgg_view('page/elements/head', array('title' => ''));
$foot = elgg_view('page/elements/foot');
echo elgg_view('page/elements/html', array(
	'head' => $head,
	'body' => elgg_format_element('div', array('class' => 'ckeditor-addons-browser'), $images . $foot),
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