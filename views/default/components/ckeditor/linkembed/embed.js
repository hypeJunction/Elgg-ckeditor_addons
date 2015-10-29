define(['elgg', 'jquery', 'scraper/play'], function (elgg, $) {
	$('a:has(img[alt="linkembed"])').each(function () {
		var $elem = $(this);
		var href = elgg.get_site_url() + 'url?iframe=1&url=' + $(this).attr('href');
		elgg.ajax(href, {
			success: function(response) {
				if (response) {
					$elem.replaceWith($(response));
				}
			}
		});
	});
});


