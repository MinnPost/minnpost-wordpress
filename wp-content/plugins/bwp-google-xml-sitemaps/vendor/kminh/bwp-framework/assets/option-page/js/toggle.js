/* global jQuery */
jQuery(function($) {
	'use strict';

	function toggle_field($t) {
		var target_id     = $t.data('target');
		var toggle_value  = $t.data('toggleValue');
		var current_value = $.trim($t.val());
		var toggled_label = $t.data('toggledLabel');

		// if there's a toggled label, switch it with the current label, this is
		// for button only
		if ($t.is(':button') && toggled_label) {
			var current_label = $t.text() ? $t.text() : $t.val();
			$t.data('toggledLabel', current_label);
			$t.text(toggled_label);
		}

		// the target of this toggling can be a standalone element, or an element
		// contained in a standard BWP form item
		var $target = ! $('#' + target_id).is(':input') && ! $('#' + target_id).is('h3')
			? $('#' + target_id) : $('#' + target_id).parents('.bwp-clear');

		// no toggle value, just show the target immediately when the selected
		// value is not blank
		if (!toggle_value) {
			if ($t.is(':button')) {
				$target.toggleClass('bwp-no-display');
			} else if ($t.is(':checkbox')) {
				var cb_invert = $t.data('checkboxInvert');
				if (cb_invert) {
					$target.toggleClass('bwp-no-display', $t.prop('checked'));
				} else {
					$target.toggleClass('bwp-no-display', ! $t.prop('checked'));
				}
			} else {
				$target.toggleClass('bwp-no-display', '' === current_value);
			}
		} else if (!$t.is(':button')) {
			$target.toggleClass('bwp-no-display', toggle_value !== current_value);
		}

		$t.prop('disabled', false);
	}

	function call_callback_after($t) {
		var callback = $t.data('callbackAfter');

		if (typeof window[callback] === 'function') {
			window[callback]($, $t);
		}
	}

	function handle_switch_action($t) {
		var loader_id = $t.data('loader');
		var callback  = $t.data('callback');

		if (loader_id) {
			$('#' + loader_id).show();
		}

		$t.prop('disabled', true);

		// execute a callback if having one
		if (typeof window[callback] === 'function') {
			// if there's a loader we need to hide that loader when the callback
			// finishes processing
			if (loader_id) {
				window[callback]($, $t, function() {
					$('#' + loader_id).hide();
					toggle_field($t);
					call_callback_after($t);
				});
			} else {
				window[callback]($, $t);
				toggle_field($t);
				call_callback_after($t);
			}
		} else {
			$('#' + loader_id).hide();
			toggle_field($t);
			call_callback_after($t);
		}
	}

	// switch by on change event
	$('.bwp-switch-select').on('change', function(e) {
		handle_switch_action($(this));
		e.preventDefault();
	});

	// switch by on click event
	$('.bwp-switch-button').on('click', function(e) {
		handle_switch_action($(this));
		e.preventDefault();
	});

	// switch on first load
	$('.bwp-switch-on-load').each(function() {
		toggle_field($(this));
	});
});
