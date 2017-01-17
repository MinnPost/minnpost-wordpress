/*global jQuery,ajaxurl,bwp_common*/
jQuery(function($) {
	'use strict';

	var message_timeout;

	$('body').on('click', '.bwp-button-modal', function(e) {
		e.preventDefault();

		var $t = $(this);
		var target_id = $t.data('target'); // this includes the '#'
		var ajax_action = $t.data('ajaxAction');

		// no ajax action needed, open the modal immediately
		if (typeof ajax_action === 'undefined') {
			$(target_id).modal();
		} else {
			var loader_id = $t.data('loader');
			var callback  = $t.data('ajaxCallback');

			// get the contents for the modal via ajax, with an id and a custom variable
			$.get(ajaxurl, {
					action: ajax_action,
					id: $t.data('id'),
					custom: $t.data('custom')
			}, function(r) {
				// fill modal contents with contents fetched via ajax
				$(target_id)
					.find('.bwp-modal-content')
					.html(r);

				bwp_common.enhance_form_fields($(target_id));

				if (typeof callback !== 'undefined') {
					window[callback]($, r, $t);
				}

				if (loader_id) {
					$('#' + loader_id).hide();
				}

				$(target_id).modal();
			});
		}
	});

	$('.bwp-modal').on('submit', 'form', function(e) {
		e.preventDefault();

		var $form = $(this);
		var $btn  = $form.parents('.bwp-modal').find('.bwp-button-modal-submit');

		var callback       = $btn.data('ajaxCallback');
		var error_callback = $btn.data('ajaxErrorCallback');

		// get data from the form by serializing it. It is important that the form
		// contains the ajax 'action' and the nonce parameter.
		var data = $form.serialize();

		// disable all inputs
		$form.find(':input').prop('disabled', true);

		// reset and show loading message
		var $loader = $btn
			.parents('.bwp-modal-footer')
			.find('.bwp-modal-message');

		$loader
			.removeClass('text-success text-danger')
			.text($loader.data('workingText'))
			.show();

		// submit the form, in case we're adding or updating anything, 'r.items'
		// should contain the data added/updated, and it is expected to be an array
		$.post(ajaxurl, data, function(r) {
			if (typeof callback !== 'undefined') {
				window[callback]($, r, $btn, $form);
			}

			// reset the form if there's no reset button
			if ($btn.parents('.bwp-modal-footer').find('.bwp-button-modal-reset').length === 0) {
				$form.trigger('reset');
			}

			// add correct class to loading message
			if (parseInt(r, 10) === 0 || r.error) {
				$loader.addClass('text-danger');
			} else {
				$loader.addClass('text-success');
			}
		}, 'json')
			.fail(function(r) {
				if (typeof error_callback !== 'undefined') {
					window[error_callback]($, r, $btn, $form);
				}

				$loader.addClass('text-danger');
			})
			.always(function(r) {
				// enable all inputs again
				$form.find(':input').prop('disabled', false);

				// a message is returned, show it first and hide after 5 secs
				if (r.message) {
					$loader.text(r.message);

					if (typeof message_timeout !== 'undefined') {
						clearTimeout(message_timeout);
					}

					message_timeout = setTimeout(function() {
						$loader.fadeOut('fast');
					}, 5000);
				} else {
					// otherwise just hide the loading message immediately
					$loader.fadeOut('fast');
				}
			});
	});

	$('.bwp-modal').on('click', '.bwp-button-modal-submit', function(e) {
		e.preventDefault();

		var $form = $(this)
			.parents('.bwp-modal')
			.find('form');

		$form.submit();
	});

	$('.bwp-modal').on('click', '.bwp-button-modal-reset', function(e) {
		e.preventDefault();

		var $t = $(this);

		$t.parents('.bwp-modal')
			.find('form')
			.trigger('reset');
	});

	// when the modal is displayed, we reset the form and loading message if any
	$('.bwp-modal').on('show.bs.modal', function(e) {
		var $t = $(this);

		$t.find('form')
			.trigger('reset');

		$t.find('.bwp-modal-message')
			.removeClass('text-success text-danger')
			.hide();
	});

	// vertically center any modal
	$('body').on('shown.bs.modal', '.bwp-modal', function(e) {
		var $dialog = $(this).find('.bwp-modal-dialog');

		$dialog.css({
			top: '50%',
			'margin-top': function() {
				return -($dialog.height() / 2);
			}
		});
	});
});
