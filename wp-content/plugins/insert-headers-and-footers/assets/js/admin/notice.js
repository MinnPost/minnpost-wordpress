/* global ihaf_admin_notices */

/**
 * IHAF Dismissible Notices.
 *
 * @since 1.6.1
 */

'use strict';

var IHAFAdminNotices = window.IHAFAdminNotices || (
	function ( document, window, $ ) {

		/**
		 * Public functions and properties.
		 *
		 * @since 1.6.1
		 *
		 * @type {object}
		 */
		var app = {

			/**
			 * Start the engine.
			 *
			 * @since 1.6.1
			 */
			init: function () {

				$( app.ready );
			},

			/**
			 * Document ready.
			 *
			 * @since 1.6.1
			 */
			ready: function () {

				app.events();
			},

			/**
			 * Dismissible notices events.
			 *
			 * @since 1.6.1
			 */
			events: function () {

				$( document ).on(
					'click',
					'.ihaf-notice .notice-dismiss, .ihaf-notice .ihaf-notice-dismiss',
					app.dismissNotice
				);
			},

			/**
			 * Dismiss notice event handler.
			 *
			 * @since 1.6.1
			 *
			 * @param {object} e Event object.
			 * */
			dismissNotice: function ( e ) {
				$.post(
					ihaf_admin_notices.ajax_url,
					{
						action: 'ihaf_notice_dismiss',
						nonce: ihaf_admin_notices.nonce,
					}
				);

				var $el = $( e.target ).closest( '.ihaf-notice' );
				// Do the same animation as the core one.
				$el.fadeTo( 100, 0, function() {
					$el.slideUp( 100, function() {
						$el.remove();
					} );
				} );
			},
		};

		return app;

	}( document, window, jQuery )
);

// Initialize.
IHAFAdminNotices.init();
