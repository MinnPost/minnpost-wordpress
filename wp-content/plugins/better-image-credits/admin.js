(function($) {
	var $minLength = 3;
	var $appendTo = '.compat-attachment-fields';

	$('body').on('focus', '.license-auto', function() {
		$('.license-auto').autocomplete({
			source: ajaxurl + '?action=license_search',
			minLength: $minLength,
			appendTo: $appendTo,
			select: function(event, ui) {
				$.get(ajaxurl, {
						action: 'license_url_search',
						lic: ui.item.value },
					function(response) {
						var $url = $('.license-url-auto').focus().val('');

						if (Array.isArray(response)) {
							if (response.length == 1) {
								$url.val(response[0]).select();
							} else if (response.length > 1) {
								$url.autocomplete({
									source: response,
									minLength: 0,
									appendTo: $appendTo,
									select: function(event, ui) {
										$url.autocomplete({
											source: ajaxurl + '?action=license_url_search',
											minLength: $minLength,
											appendTo: $appendTo
										});
									}
								}).autocomplete('search', '');
							}
						}
					}, 'json');
			}
		});
	});

	$('body').on('focus', '.license-url-auto', function() {
		$('.license-url-auto').autocomplete({
			source: ajaxurl + '?action=license_url_search',
			minLength: $minLength,
			appendTo: $appendTo
		});
	});

})(jQuery);