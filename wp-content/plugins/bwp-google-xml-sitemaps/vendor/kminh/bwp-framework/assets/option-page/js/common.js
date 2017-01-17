var bwp_common = (function($) {
	'use strict';

	return {
		enhance_form_fields: function($t) {
			// apply the enhancement globally or for a specific target
			$t = $t || $('body');

			// add an asterisk to a required field
			var asterisk = ' <span class="text-danger">*</span>';
			$t.find('.bwp-label-required').each(function() {
				$(this).html($(this).text() + asterisk);
			});

			// add a datepicker, using jquery-ui-datepicker if available
			if (typeof $.datepicker !== 'undefined') {
				$t.find('.bwp-datepicker').each(function() {
					var $t = $(this);

					// if this field has an icon sibling, use it as a toggle button to open
					// the calendar
					var show_on = 'focus';
					var $icon = $t.next('.bwp-form-control-icon');
					if ($icon.length > 0) {
						show_on = 'button';
						$icon.on('click', function(e) {
							e.preventDefault();
							$t.datepicker('show');
						});
					}

					// support custom date format
					var format = $t.data('dateFormat');
					format = format || 'yy-mm-dd';

					// finally init the datepicker
					$t.datepicker({
						dateFormat: format,
						showOn: show_on
					});
				});
			}
		}
	};
})(jQuery);
