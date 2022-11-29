<?php
/*
 * We recommend all plugins for your site are
 * loaded in code, either from a file like this
 * one or from your theme (if the plugins are
 * specific to your theme and do not need to be
 * loaded as early as this in the WordPress boot
 * sequence.
 *
 * @see https://vip.wordpress.com/documentation/vip-go/understanding-your-vip-go-codebase/
 */

// wpcom_vip_load_plugin( 'plugin-name' );
/**
 * Note the above requires a specific naming structure: /plugin-name/plugin-name.php
 * You can also specify a specific root file: wpcom_vip_load_plugin( 'plugin-name/plugin.php' );
 *
 * wpcom_vip_load_plugin only loads plugins from the `WP_PLUGIN_DIR` directory.
 * For client-mu-plugins `require __DIR__ . '/plugin-name/plugin-name.php'` works.
 */

// active plugins
wpcom_vip_load_plugin( 'ad-code-manager/ad-code-manager.php' );
wpcom_vip_load_plugin( 'ads-txt/ads-txt.php' );
wpcom_vip_load_plugin( 'ap-dates-and-times/ap-dates-and-times.php' );
wpcom_vip_load_plugin( 'arcads-dfp-acm-provider/arcads-dfp-acm-provider.php' );
wpcom_vip_load_plugin( 'better-image-credits/better-image-credits.php' );
wpcom_vip_load_plugin( 'category-pagination-fix/category-pagefix.php' );
wpcom_vip_load_plugin( 'cmb2/init.php' );
wpcom_vip_load_plugin( 'cmb2-attached-posts/cmb2-attached-posts-field.php' );
wpcom_vip_load_plugin( 'cmb2-conditionals/cmb2-conditionals.php' );
wpcom_vip_load_plugin( 'cmb2-field-ajax-search/cmb2-field-ajax-search.php' );
wpcom_vip_load_plugin( 'cmb2-field-post-search-ajax/cmb-field-post-search-ajax.php' );
wpcom_vip_load_plugin( 'cmb2-select-plus/cmb2-select-plus.php' );
wpcom_vip_load_plugin( 'cmb2-user-select/cmb2-user-select.php' );
wpcom_vip_load_plugin( 'cmb2-field-type-font-awesome/iconselect.php' );
wpcom_vip_load_plugin( 'co-authors-plus/co-authors-plus.php' );
wpcom_vip_load_plugin( 'documentcloud/documentcloud.php' );
if ( 'production' === VIP_GO_ENV ) {
	wpcom_vip_load_plugin( 'dorzki-notifications-to-slack/slack-notifications.php' );
}
wpcom_vip_load_plugin( 'duplicate-post/duplicate-post.php' );
/*
we need to keep this disabled until there is a new adapter for VIP's enterprise search.
see: https://github.com/alleyinteractive/es-admin/issues/26
if ( ( defined( 'VIP_ENABLE_VIP_SEARCH' ) && true === VIP_ENABLE_VIP_SEARCH ) ) {
	wpcom_vip_load_plugin( 'es-admin/es-admin.php' );
}*/
wpcom_vip_load_plugin( 'exclude-terms-in-admin/exclude-terms-in-admin.php' );
wpcom_vip_load_plugin( 'font-awesome/index.php' );
wpcom_vip_load_plugin( 'form-processor-mailchimp/form-processor-mailchimp.php' );
wpcom_vip_load_plugin( 'gravityforms/gravityforms.php' );
//wpcom_vip_load_plugin( 'hcaptcha-for-forms-and-more/hcaptcha.php' );
wpcom_vip_load_plugin( 'highlight-search-terms/hlst.php' );
wpcom_vip_load_plugin( 'insert-headers-and-footers/ihaf.php' );
if ( 'production' === VIP_GO_ENV || 'preprod' === VIP_GO_ENV ) {
	wpcom_vip_load_plugin( 'jquery-updater/jquery-updater.php' );
}
wpcom_vip_load_plugin( 'lazy-load-for-comments/lazy-load-for-comments.php' );
wpcom_vip_load_plugin( 'lh-multipart-email/lh-multipart-email.php' );
wpcom_vip_load_plugin( 'liveblog/liveblog.php' );
wpcom_vip_load_plugin( 'minnpost-event-addon/minnpost-event-addon.php' );
wpcom_vip_load_plugin( 'minnpost-form-processor-mailchimp/minnpost-form-processor-mailchimp.php' );
wpcom_vip_load_plugin( 'minnpost-membership/minnpost-membership.php' );
wpcom_vip_load_plugin( 'minnpost-roles-and-capabilities/minnpost-roles-and-capabilities.php' );
wpcom_vip_load_plugin( 'minnpost-spills-widget/minnpost-spills-widget.php' );
wpcom_vip_load_plugin( 'minnpost-wordpress-salesforce-plugin/minnpost-salesforce.php' );
wpcom_vip_load_plugin( 'most-commented/most-commented.php' );
wpcom_vip_load_plugin( 'msm-sitemap/msm-sitemap.php' );
wpcom_vip_load_plugin( 'multiple-roles/multiple-roles.php' );
wpcom_vip_load_plugin( 'nav-menu-roles/nav-menu-roles.php' );
wpcom_vip_load_plugin( 'no-category-base-wpml/no-category-base-wpml.php' );
wpcom_vip_load_plugin( 'object-sync-for-salesforce/object-sync-for-salesforce.php' );
wpcom_vip_load_plugin( 'public-post-preview/public-post-preview.php' );
wpcom_vip_load_plugin( 'pwa/pwa.php' );
wpcom_vip_load_plugin( 'raw-html-pro/raw_html.php' );
wpcom_vip_load_plugin( 'recently-registered/recently-registered.php' );
wpcom_vip_load_plugin( 'remove-widget-titles/remove-widget-titles.php' );
wpcom_vip_load_plugin( 'republication-tracker-tool/republication-tracker-tool.php' );
wpcom_vip_load_plugin( 'reset-metabox-order/reset-metabox-order.php' );
wpcom_vip_load_plugin( 'schema-and-structured-data-for-wp/structured-data-for-wp.php' );
wpcom_vip_load_plugin( 'simple-comment-editing/index.php' );
wpcom_vip_load_plugin( 'staff-user-post-list/staff-user-list.php' );
wpcom_vip_load_plugin( 'stop-spammer-registrations-plugin/stop-spammer-registrations-new.php' );
wpcom_vip_load_plugin( 'the-events-calendar/the-events-calendar.php' );
wpcom_vip_load_plugin( 'tribe-ext-speaker-linked-post-type/tribe-ext-speaker-linked-post-type.php' );
wpcom_vip_load_plugin( 'user-account-management/user-account-management.php' );
wpcom_vip_load_plugin( 'view-admin-as/view-admin-as.php' );
wpcom_vip_load_plugin( 'widget-options/plugin.php' );
wpcom_vip_load_plugin( 'widget-output-filters/widget-output-filters.php' );
wpcom_vip_load_plugin( 'wpcom-legacy-redirector/wpcom-legacy-redirector.php' );
wpcom_vip_load_plugin( 'wp-analytics-tracking-generator/wp-analytics-tracking-generator.php' );
wpcom_vip_load_plugin( 'wp-category-permalink/wp-category-permalink.php' );
wpcom_vip_load_plugin( 'wp-message-inserter-plugin/wp-message-inserter-plugin.php' );
wpcom_vip_load_plugin( 'wp-post-expires/wp-post-expires.php' );
wpcom_vip_load_plugin( 'wp-post-image-watermarks/wp-post-image-watermarks.php' );
wpcom_vip_load_plugin( 'www-post-thumb/www-post-thumb.php' );
wpcom_vip_load_plugin( 'zoninator/zoninator.php' );

// turn off parsely because 1) we're not paying for it, and 2) it has ethical question marks at best
add_filter( 'wpvip_parsely_load_mu', '__return_false' );

if ( ! defined( 'VIP_ENABLE_VIP_SEARCH_QUERY_INTEGRATION' ) || true !== VIP_ENABLE_VIP_SEARCH_QUERY_INTEGRATION ) {
	// for non elastic search environments:

	// enable jetpack search and jetpack related posts.
	add_filter( 'jetpack_active_modules', 'x_enable_jetpack_search_module', 9999 );
	function x_enable_jetpack_search_module( $modules ) {
		if ( ! in_array( 'search', $modules, true ) ) {
			$modules[] = 'search';
		}
		if ( ! in_array( 'related-posts', $modules, true ) ) {
			$modules[] = 'related-posts';
		}
		return $modules;
	}

	// enable es-wp-query adapter. this only works for Jetpack Search.
	add_action(
		'after_setup_theme',
		function () {
			if ( function_exists( 'es_wp_query_load_adapter' ) ) {
				es_wp_query_load_adapter( 'jetpack-search' );
			}
		},
		5
	);

	// enable es-admin adapter. this only works for Jetpack Search.
	add_filter(
		'es_admin_adapter',
		function () {
			return '\ES_Admin\Adapters\Jetpack_Search';
		}
	);

} elseif ( defined( 'VIP_ENABLE_VIP_SEARCH_QUERY_INTEGRATION' ) && true === VIP_ENABLE_VIP_SEARCH_QUERY_INTEGRATION ) {
	// for elasticsearch only:

	// make sure to remove search and related post modules.
	add_filter(
		'jetpack_active_modules',
		function( $modules ) {
			foreach ( $modules as $i => $m ) {
				if ( 'search' === $m ) {
					unset( $modules[ $i ] );
				}
				if ( 'related-posts' === $m ) {
					unset( $modules[ $i ] );
				}
			}
			return $modules;
		}
	);

}
