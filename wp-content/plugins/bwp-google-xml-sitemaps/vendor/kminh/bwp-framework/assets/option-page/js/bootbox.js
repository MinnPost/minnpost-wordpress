/*global jQuery,bootbox*/
var bwp_bootbox = (function($, bb) {
	'use strict';

	bb.setDefaults({
		animation: false,
		closeButton: false
	});

	return {
		alert: function(message, callback, options) {
			options = options || {};
			options = $.extend(options, {
				size: 'small',
				message: message,
				callback: callback
			});

			bb.alert(options);
		},

		// the callback here is different, it is called only when confirm returns true
		confirm: function(message, callback, options) {
			options = options || {};
			options = $.extend(options, {
				size: 'small',
				message: message,
				callback: function(r) {
					if (r) {
						callback();
					} else if (options.callback_cancel) {
						options.callback_cancel();
					}
				}
			});

			bb.confirm(options);
		}
	};
})(jQuery, bootbox);
