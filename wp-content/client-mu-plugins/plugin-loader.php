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
wpcom_vip_load_plugin( 'arcads-dfp-acm-provider/arcads-dfp-acm-provider.php' );
wpcom_vip_load_plugin( 'better-image-credits/better-image-credits.php' );
wpcom_vip_load_plugin( 'category-pagination-fix/category-pagefix.php' );
if ( 'production' === VIP_GO_ENV ) {
	wpcom_vip_load_plugin( 'chartbeat/chartbeat.php' );
}
wpcom_vip_load_plugin( 'cmb2/init.php' );
wpcom_vip_load_plugin( 'cmb2-attached-posts/cmb2-attached-posts-field.php' );
wpcom_vip_load_plugin( 'cmb2-conditionals/cmb2-conditionals.php' );
wpcom_vip_load_plugin( 'cmb2-field-ajax-search/cmb2-field-ajax-search.php' );
wpcom_vip_load_plugin( 'cmb2-field-post-search-ajax/cmb-field-post-search-ajax.php' );
wpcom_vip_load_plugin( 'cmb2-select-plus/cmb2-select-plus.php' );
wpcom_vip_load_plugin( 'cmb2-user-select/cmb2-user-select.php' );
wpcom_vip_load_plugin( 'co-authors-extend/co-authors-extend.php' );
wpcom_vip_load_plugin( 'co-authors-plus/co-authors-plus.php' );
wpcom_vip_load_plugin( 'cr3ativ-sponsor/cr3ativ-sponsor.php' );
wpcom_vip_load_plugin( 'documentcloud/documentcloud.php' );
wpcom_vip_load_plugin( 'duplicate-post/duplicate-post.php' );
if ( 'local' !== VIP_GO_ENV ) {
	wpcom_vip_load_plugin( 'es-admin/es-admin.php' );
	wpcom_vip_load_plugin( 'es-wp-query/es-wp-query.php' );
}
wpcom_vip_load_plugin( 'exclude-terms-in-admin/exclude-terms-in-admin.php' );
wpcom_vip_load_plugin( 'form-processor-mailchimp/form-processor-mailchimp.php' );
wpcom_vip_load_plugin( 'gravityforms/gravityforms.php' );
wpcom_vip_load_plugin( 'insert-headers-and-footers/ihaf.php' );
wpcom_vip_load_plugin( 'jquery-updater/jquery-updater.php' );
wpcom_vip_load_plugin( 'lh-multipart-email/lh-multipart-email.php' );
wpcom_vip_load_plugin( 'liveblog/liveblog.php' );
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
wpcom_vip_load_plugin( 'permalink-without-parent-category/permalink-without-parent-category.php' );
wpcom_vip_load_plugin( 'popup-maker/popup-maker.php' );
wpcom_vip_load_plugin( 'public-post-preview/public-post-preview.php' );
wpcom_vip_load_plugin( 'pwa/pwa.php' );
wpcom_vip_load_plugin( 'raw-html-pro/raw_html.php' );
wpcom_vip_load_plugin( 'recent-posts-widget-extended/rpwe.php' );
wpcom_vip_load_plugin( 'recently-registered/recently-registered.php' );
wpcom_vip_load_plugin( 'remove-widget-titles/remove-widget-titles.php' );
wpcom_vip_load_plugin( 'simple-image-sizes/simple_image_sizes.php' );
wpcom_vip_load_plugin( 'staff-user-post-list/staff-user-list.php' );
wpcom_vip_load_plugin( 'stop-spammer-registrations-plugin/stop-spammer-registrations-new.php' );
wpcom_vip_load_plugin( 'term-management-tools/term-management-tools.php' );
wpcom_vip_load_plugin( 'the-events-calendar/the-events-calendar.php' );
wpcom_vip_load_plugin( 'user-account-management/user-account-management.php' );
wpcom_vip_load_plugin( 'view-admin-as/view-admin-as.php' );
wpcom_vip_load_plugin( 'term-management-tools/term-management-tools.php' );
wpcom_vip_load_plugin( 'widget-options/plugin.php' );
wpcom_vip_load_plugin( 'widget-output-filters/widget-output-filters.php' );
wpcom_vip_load_plugin( 'widget-settings-importexport/widget-data.php' );
wpcom_vip_load_plugin( 'wpcom-legacy-redirector/wpcom-legacy-redirector.php' );
wpcom_vip_load_plugin( 'wp-analytics-tracking-generator/wp-analytics-tracking-generator.php' );
wpcom_vip_load_plugin( 'wp-category-permalink/wp-category-permalink.php' );
wpcom_vip_load_plugin( 'wp-message-inserter-plugin/wp-message-inserter-plugin.php' );
wpcom_vip_load_plugin( 'wp-post-expires/wp-post-expires.php' );
wpcom_vip_load_plugin( 'wp-post-image-watermarks/wp-post-image-watermarks.php' );
wpcom_vip_load_plugin( 'www-post-thumb/www-post-thumb.php' );
wpcom_vip_load_plugin( 'zoninator/zoninator.php' );

// enable jetpack search
if ( 'local' !== VIP_GO_ENV ) {
	add_filter( 'jetpack_active_modules', 'x_enable_jetpack_search_module', 9999 );
	function x_enable_jetpack_search_module( $modules ) {
		if ( ! in_array( 'search', $modules, true ) ) {
			$modules[] = 'search';
		}
		return $modules;
	}
}

// es-wp-query adapter
if ( 'local' !== VIP_GO_ENV ) {
	add_action(
		'after_setup_theme',
		function() {
			if ( function_exists( 'es_wp_query_load_adapter' ) ) {
				es_wp_query_load_adapter( 'jetpack-search' );
			}
		},
		5
	);
}

// es-admin adapter
if ( 'local' !== VIP_GO_ENV ) {
	add_filter(
		'es_admin_adapter',
		function() {
			return '\ES_Admin\Adapters\Jetpack_Search';
		}
	);
}
