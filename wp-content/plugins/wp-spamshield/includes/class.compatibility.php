<?php
/**
 *  WP-SpamShield Compatibility
 *  File Version 1.9.16
 */

/* Make sure file remains secure if called directly */
if( !defined( 'ABSPATH' ) || !defined( 'WPSS_VERSION' ) ) {
	if( !headers_sent() ) { @header( $_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden', TRUE, 403 ); @header( 'X-Robots-Tag: noindex', TRUE ); }
	die( 'ERROR: Direct access to this file is not allowed.' );
}
/* Prevents unintentional error display if WP_DEBUG not enabled. */
if( TRUE !== WPSS_DEBUG && TRUE !== WP_DEBUG ) { @ini_set( 'display_errors', 0 ); @error_reporting( 0 ); }


final class WPSS_Compatibility extends WP_SpamShield {

	/**
	 *	WP-SpamShield Compatibility Class
	 *	Plugin detection
	 *	Compatibility deconfliction for some of the plugins listed in the Known Issues and Plugin Conflicts ( http://www.redsandmarketing.com/plugins/wp-spamshield/known-conflicts/ )
	 *	Where possible, apply compatibility fixes or workarounds
	 */

	function __construct() {
		/**
		 *	Do nothing...for now
		 */
	}

	/**
	 *	Drop in replacement for WordPress native `is_plugin_active()` function
	 *	Detect if plugin is active, even if not in Admin
	 *	Use this because WordPress' native `is_plugin_active()` function only works in Admin area ( `is_admin()` === TRUE )
	 *	ex. $plug_bn = 'folder/filename.php'; // Plugin Basename
	 *	$plug_bn can be just a slug if basename is formatted like so: {slug}/{slug}.php		@since 1.9.14
	 *	@dependencies	...
	 *	@since			...
	 */
	static public function is_plugin_active( $plug_bn, $check_network = TRUE ) {
		if( empty( $plug_bn ) ){ return FALSE; }
		$plug_bn = ( FALSE === WPSS_PHP::strpos( $plug_bn, array( '.php', '/', ) ) ) ? $plug_bn.'/'.$plug_bn.'.php' : $plug_bn;
		global $wpss_conf_active_plugins,$wpss_active_plugins,$wpss_active_network_plugins;
		/* Quick Check */
		if( !empty( $wpss_conf_active_plugins[$plug_bn] ) ) { return TRUE; }
		if( TRUE === $check_network && is_multisite() ) { if( !empty( $wpss_conf_active_network_plugins[$plug_bn] ) ) { return TRUE; } }
		$wpss_conf_active_plugins = array();
		$wpss_conf_active_network_plugins = array();
		/* Check known plugin constants and classes */
		$plug_cncl = array(
			/* Compatibility Fixes */
			'autoptimize/autoptimize.php' => array( 'cn' => 'AUTOPTIMIZE_WP_CONTENT_NAME', 'cl' => 'autoptimizeConfig' ), 'gwolle-gb/gwolle-gb.php' => array( 'cn' => 'GWOLLE_GB_VER', 'cl' => '' ), 'jetpack/jetpack.php' => array( 'cn' => 'JETPACK__VERSION', 'cl' => 'Jetpack' ), 'plugin-organizer/plugin-organizer.php' => array( 'cn' => '', 'cl' => 'PluginOrganizer' ), 'si-contact-form/si-contact-form.php' => array( 'cn' => 'FSCF_VERSION', 'cl' => 'FSCF_Util' ), 'wp-slimstat/wp-slimstat.php' => array( 'cn' => '', 'cl' => 'wp_slimstat' ), 'wordpress-seo/wp-seo.php' => array( 'cn' => 'WPSEO_VERSION', 'cl' => '' ), 'wp-spamfree/wp-spamfree.php' => array( 'cn' => '', 'cl' => 'wpSpamFree' ),
			/* 3rd Party Forms, Membership & Registration */
			'bbpress/bbpress.php' => array( 'cn' => '', 'cl' => 'bbPress' ), 'buddypress/bp-loader.php' => array( 'cn' => 'BP_PLUGIN_DIR', 'cl' => 'BuddyPress' ), 'contact-form-7/wp-contact-form-7.php' => array( 'cn' => 'WPCF7_VERSION', 'cl' => '' ), 'gravityforms/gravityforms.php' => array( 'cn' => 'GF_MIN_WP_VERSION', 'cl' => 'GFForms' ), 'mailchimp-for-wp/mailchimp-for-wp.php' => array( 'cn' => 'MC4WP_LITE_VERSION', 'cl' => 'MC4WP_Lite' ), 'ninja-forms/ninja-forms.php' => array( 'cn' => 'NF_PLUGIN_VERSION', 'cl' => 'Ninja_Forms' ),
			/* Cache Plugins */
			'w3-total-cache/w3-total-cache.php' => array( 'cn' => 'W3TC_VERSION', 'cl' => '' ), 'wp-fastest-cache/wpFastestCache.php' => array( 'cn' => 'WPFC_WP_PLUGIN_DIR', 'cl' => 'WpFastestCache' ), 'wp-fastest-cache-premium/wpFastestCachePremium.php' => array( 'cn' => '', 'cl' => '' ), 'wp-rocket/wp-rocket.php' => array( 'cn' => 'WP_ROCKET_VERSION', 'cl' => '' ),
			/* Ecommerce Plugins */
			'affiliates/affiliates.php' => array( 'cn' => 'AFFILIATES_CORE_VERSION', 'cl' => '' ), 'caldera-forms/caldera-core.php' => array( 'cn' => 'CFCORE_VER', 'cl' => '' ), 'download-manager/download-manager.php' => array( 'cn' => 'WPDM_Version', 'cl' => '' ), 'easy-digital-downloads/easy-digital-downloads.php' => array( 'cn' => 'EDD_VERSION', 'cl' => 'Easy_Digital_Downloads' ), 'ecommerce-product-catalog/ecommerce-product-catalog.php' => array( 'cn' => 'AL_BASE_PATH', 'cl' => 'eCommerce_Product_Catalog' ), 'ecwid-shopping-cart/ecwid-shopping-cart.php' => array( 'cn' => 'ECWID_PLUGIN_DIR', 'cl' => '' ), 'edd-invoices/edd-invoices.php' => array( 'cn' => '', 'cl' => '' ), 'edd-recurring/edd-recurring.php' => array( 'cn' => '', 'cl' => '' ), 'edd-software-licensing/edd-software-licenses.php' => array( 'cn' => '', 'cl' => '' ), 'eshop/eshop.php' => array( 'cn' => 'ESHOP_VERSION', 'cl' => '' ), 'events-made-easy/events-manager.php' => array( 'cn' => 'EME_DB_VERSION', 'cl' => '' ), 'events-manager/events-manager.php' => array( 'cn' => '', 'cl' => '' ), 'formidable-paypal/formidable-paypal.php' => array( 'cn' => '', 'cl' => '' ), 'give/give.php' => array( 'cn' => 'GIVE_VERSION', 'cl' => 'Give' ), 'gravity-forms-stripe/gravity-forms-stripe.php' => array( 'cn' => 'GFP_STRIPE_FILE', 'cl' => '' ), 'gravityformsauthorizenet/authorizenet.php' => array( 'cn' => 'GF_AUTHORIZENET_VERSION', 'cl' => 'GF_AuthorizeNet_Bootstrap' ), 'gravityformspayfast/payfast.php' => array( 'cn' => 'GF_PAYFAST_VERSION', 'cl' => 'GF_PayFast_Bootstrap' ), 'gravityformsstripe/stripe.php' => array( 'cn' => 'GF_STRIPE_VERSION', 'cl' => 'GF_Stripe_Bootstrap' ), 'gravityformspaypal/paypal.php' => array( 'cn' => 'GF_PAYPAL_VERSION', 'cl' => 'GF_PayPal_Bootstrap' ), 'ithemes-exchange/init.php' => array( 'cn' => '', 'cl' => 'IT_Exchange' ), 'jigoshop/jigoshop.php' => array( 'cn' => 'JIGOSHOP_VERSION', 'cl' => '' ), 'memberpress/memberpress.php' => array( 'cn' => 'MEPR_VERSION', 'cl' => '' ), 'mollie-payments-for-woocommerce/mollie-payments-for-woocommerce.php' => array( 'cn' => 'M4W_PLUGIN_DIR', 'cl' => 'Mollie_WC_Autoload' ), 'paid-memberships-pro/paid-memberships-pro.php' => array( 'cn' => 'PMPRO_VERSION', 'cl' => '' ), 'paytium/paytium.php' => array( 'cn' => 'PT_VERSION', 'cl' => '' ), 'paytium-edd/paytium-edd.php' => array( 'cn' => '', 'cl' => '' ), 's2member/s2member.php' => array( 'cn' => 'WS_PLUGIN__S2MEMBER_VERSION', 'cl' => '' ), 'shopp/Shopp.php' => array( 'cn' => '', 'cl' => 'ShoppLoader' ), 'simple-membership/simple-wp-membership.php' => array( 'cn' => 'SIMPLE_WP_MEMBERSHIP_VER', 'cl' => '' ), 'stripe/stripe-checkout.php' => array( 'cn' => 'SIMPAY_VERSION', 'cl' => '' ), 'ultimate-product-catalogue/UPCP_Main.php' => array( 'cn' => 'UPCP_CD_PLUGIN_PATH', 'cl' => '' ), 'usc-e-shop/usc-e-shop.php' => array( 'cn' => 'USCES_VERSION', 'cl' => '' ), 'users-ultra/xoousers.php' => array( 'cn' => 'xoousers_url', 'cl' => '' ), 'wc-vendors/class-wc-vendors.php' => array( 'cn' => 'wcv_plugin_dir', 'cl' => 'WC_Vendors' ), 'woocommerce-paypal-pro-payment-gateway/woo-paypal-pro.php' => array( 'cn' => 'WC_PP_PRO_ADDON_VERSION', 'cl' => 'WC_Paypal_Pro_Gateway_Addon' ), 'woocommerce/woocommerce.php' => array( 'cn' => 'WOOCOMMERCE_VERSION', 'cl' => 'WooCommerce' ), 'wordpress-ecommerce/marketpress.php' => array( 'cn' => 'MP_LITE', 'cl' => 'MarketPress' ), 'wordpress-simple-paypal-shopping-cart/wp_shopping_cart.php' => array( 'cn' => 'WP_CART_VERSION', 'cl' => '' ), 'wp-e-commerce/wp-shopping-cart.php' => array( 'cn' => 'WPSC_VERSION', 'cl' => 'WP_eCommerce' ), 'wp-easycart/wpeasycart.php' => array( 'cn' => 'EC_CURRENT_VERSION', 'cl' => '' ), 'wp-shop-original/wp-shop.php' => array( 'cn' => 'WPSHOP_DIR', 'cl' => '' ), 'wp-ultra-simple-paypal-shopping-cart/wp_ultra_simple_shopping_cart.php' => array( 'cn' => 'WUSPSC_VERSION', 'cl' => '' ), 'wppizza/wppizza.php' => array( 'cn' => 'WPPIZZA_VERSION', 'cl' => '' ), 'yith-woocommerce-stripe/init.php' => array( 'cn' => 'YITH_WCSTRIPE_VERSION', 'cl' => '' ),
			/* Security Plugins */
			'wordfence/wordfence.php' => array( 'cn' => 'WORDFENCE_VERSION', 'cl' => 'wordfence' ), 
			/* Page Builder Plugins */
			'beaver-builder-lite-version/fl-builder.php' => array( 'cn' => 'FL_BUILDER_VERSION', 'cl' => 'FLBuilder' ), 'bb-plugin/fl-builder.php' => array( 'cn' => 'FL_BUILDER_VERSION', 'cl' => 'FLBuilder' ),
			/* Conflicting/Unsupported/Insecure Plugins - Alert to Deactivate and Uninstall */
			'commentluv/commentluv.php' => array( 'cn' => '', 'cl' => 'commentluv' ), 
			/* All others */
		);
		if( ( !empty( $plug_cncl[$plug_bn]['cn'] ) && defined( $plug_cncl[$plug_bn]['cn'] ) ) || ( !empty( $plug_cncl[$plug_bn]['cl'] ) && class_exists( $plug_cncl[$plug_bn]['cl'] ) ) ) { $wpss_conf_active_plugins[$plug_bn] = TRUE; return TRUE; }
		/* No match yet, so now do standard check */
		if( empty( $wpss_active_plugins ) ) { $wpss_active_plugins = rs_wpss_get_active_plugins(); }
		if( WPSS_PHP::in_array( $plug_bn, $wpss_active_plugins ) ) { $wpss_conf_active_plugins[$plug_bn] = TRUE; return TRUE; }
		if( TRUE === $check_network && is_multisite() ) {
			if( empty( $wpss_active_network_plugins ) ) { $wpss_active_network_plugins = rs_wpss_get_active_network_plugins(); }
			if( WPSS_PHP::in_array( $plug_bn, $wpss_active_network_plugins ) ) { $wpss_conf_active_network_plugins[$plug_bn] = TRUE; return TRUE; }
		}
		return FALSE;
	}

	/**
	 *	Add plugins to PHP Compatibility Checker Plugin whitelist to prevent false positives
	 *	All our plugins are fully PHP 7+ compatible
	 *	@dependencies	...
	 *	@since			1.9.9.8.2
	 */
	static public function php_compat( $ignored = array() ) {
		if( !is_array( $ignored ) ) { return $ignored; }
		$rsmg_plugins = array( 'rs-feedburner', 'rs-head-cleaner', 'rs-head-cleaner-lite', 'rs-nofollow-blogroll', 'rs-system-diagnostic', 'scrapebreaker', 'wp-spamshield' );
		foreach( $rsmg_plugins as $i => $p ) {
			$plugin = '*/'.$p.'/*';
			if( !WPSS_PHP::in_array( $plugin, $ignored ) ) {
				$ignored[] = $plugin;
			}
		}
		return $ignored;
	}

	static public function upgrade_conflict_check() {
		/**
		 *	When it is detected that the plugin has been upgraded, fire, and run these checks
		 */

		/* Plugin Organizer Plugin ( https://wordpress.org/plugins/plugin-organizer/ ) */
		if( self::is_plugin_active( 'plugin-organizer' ) ) {
			self::deconflict_po_01();
		}
	}

	/**
	 *	Check if supported 3rd party plugins are active that require exceptions
	 *	@hook			action|plugins_loaded|-100
	 *	@dependencies	...
	 *	@since			...
	 */
	static public function supported() {
		/**
		 *	Turn on Soft Compat Mode for the following plugins: ( TO DO: array() / foreach() )
		 *	Gravity Forms		- http://www.gravityforms.com/
		 *	W3 Total Cache		- https://wordpress.org/plugins/w3-total-cache/ - https://www.w3-edge.com/products/w3-total-cache/
		 */
		if( self::is_plugin_active( 'gravityforms' ) || self::is_plugin_active( 'w3-total-cache' ) ) {
			if( !defined( 'WPSS_SOFT_COMPAT_MODE' ) ) { define( 'WPSS_SOFT_COMPAT_MODE', TRUE ); }
		}

		/**
		 *	Gwolle Guestbook	- https://wordpress.org/plugins/gwolle-gb/
		 */
		if( self::is_plugin_active( 'gwolle-gb' ) ) {
			$spamshield_options = parent::get_option();
			if( empty( $spamshield_options['disable_misc_form_shield'] ) ) {
				self::deconflict_gwgb_01();
				add_filter( 'gwolle_gb_button', array( __CLASS__, 'deconflict_gwgb_02' ), -100, 1 );
				if( 'POST' === WPSS_REQUEST_METHOD && !empty( $_POST ) ) {
					if( is_admin() ) {
						if( !empty( $_GET['page'] ) && 'gwolle-gb/settings.php' === $_GET['page'] ) {
							$pref = ''; $keys_unset	= array( 'antispam-answer', 'antispam-question', 'form_ajax', 'form_antispam_enabled', 'form_recaptcha_enabled', 'honeypot', 'gwolle_gb_nonce', );
							foreach( $keys_unset as $i => $k ) { unset( $_POST[$pref.$k] ); }
							add_action( 'shutdown', array( __CLASS__, 'deconflict_gwgb_02' ), -100 );
						}
					} elseif( !rs_wpss_is_user_logged_in() ) {
						add_filter( 'gwolle_gb_write_add_after', array( __CLASS__, 'deconflict_gwgb_03' ), 10, 1 );
						if( !empty( $_POST['gwolle_gb_function'] ) && 'add_entry' === $_POST['gwolle_gb_function'] ) {
							$pref = 'gwolle_gb_'; $keys_unset = array( 'antispam_answer', 'captcha_code', 'captcha_prefix', );
							foreach( $keys_unset as $i => $k ) { unset( $_POST[$pref.$k] ); }
						}
					}
				}
			}
		}

		/* Add next... */

	}

	/**
	 *	Check if unsupported 3rd party plugins are active, then deconflict
	 *	@hook			action|plugins_loaded|-90
	 *	@since			1.9.15
	 */
	static public function unsupported() {

		/**
		 *	Simple Comment Editing	- https://wordpress.org/plugins/simple-comment-editing/
		 */
		if( self::is_plugin_active( 'simple-comment-editing/index.php' ) ) {
			remove_action( 'plugins_loaded', 'sce_instantiate', 10 );
			add_filter( 'sce_can_edit', '__return_false', WPSS_L0 );
			add_filter( 'sce_allow_delete', '__return_false', WPSS_L0 );
			add_filter( 'sce_load_scripts', '__return_false', WPSS_L0 );
		}

		/* Add next... */

	}

	/**
	 *	Check if plugins with known conflicts/issues are active, then deconflict using workarounds
	 *	@hook			action|plugins_loaded|100
	 *	@dependencies	...
	 *	@since			...
	 */
	static public function conflict_check() {
		/* New User Approve Plugin ( https://wordpress.org/plugins/new-user-approve/ ) */
		if( class_exists( 'pw_new_user_approve' ) ) {
			add_action( 'register_post', array( __CLASS__, 'deconflict_nua_01' ), -10 );
			add_action( 'registration_errors', array( __CLASS__, 'deconflict_nua_02' ), -10 );
		}

		/* Affiliates Plugin ( https://wordpress.org/plugins/affiliates/ ) */
		if( defined( 'AFFILIATES_CORE_VERSION' ) || class_exists( 'Affiliates_Registration' ) ) {
			if( class_exists( 'Affiliates_Registration' ) && method_exists( 'Affiliates_Registration', 'update_affiliate_user' ) ) {
				add_filter( 'user_registration_email', 'rs_wpss_sanitize_new_user_email' );
			}
			if( class_exists( 'Affiliates_Registration' ) && method_exists( 'Affiliates_Registration', 'render_form' ) ) {
				add_filter( 'affiliates_registration_after_fields', 'rs_wpss_register_form_append' );
			}
		}

		/* Easy Digital Downloads ( https://wordpress.org/plugins/easy-digital-downloads/ ) */
		if( self::is_edd_active() ) {
			add_action( 'edd_process_verified_download', array( __CLASS__, 'deconflict_edd_01' ), 999 );
		}

		/* Add next... */

	}

	static public function deconflict_edd_01() {
		remove_filter( 'wpss_filter_404', 'rs_wpss_mod_status_header', 100 );
	}

	static public function deconflict_nua_01() {
		if( class_exists( 'pw_new_user_approve' ) && method_exists( 'pw_new_user_approve', 'create_new_user' ) && has_filter( 'register_post', array( pw_new_user_approve::instance(), 'create_new_user' ) ) ) {
			remove_action( 'register_post', array( pw_new_user_approve::instance(), 'create_new_user' ), 10 );
			add_action( 'registration_errors', array( __CLASS__, 'deconflict_nua_01_01' ), 9998, 3 );
		}
	}

	static public function deconflict_nua_01_01( $errors, $user_login, $user_email ) {
		if( !empty( $errors ) && is_object( $errors ) && $errors->get_error_code() ) { return $errors; }
		if( class_exists( 'pw_new_user_approve' ) && method_exists( 'pw_new_user_approve', 'create_new_user' ) ) {
			if( empty( $errors ) || !is_object( $errors ) ) { $errors = new WP_Error; }
			pw_new_user_approve::instance()->create_new_user( $user_login, $user_email, $errors );
		}
		return $errors;
	}

	static public function deconflict_nua_02() {
		if( class_exists( 'pw_new_user_approve' ) && method_exists( 'pw_new_user_approve', 'show_user_pending_message' ) && has_filter( 'registration_errors', array( pw_new_user_approve::instance(), 'show_user_pending_message' ) ) ) {
			remove_filter( 'registration_errors', array( pw_new_user_approve::instance(), 'show_user_pending_message' ), 10 );
			if( function_exists( 'login_header' ) && function_exists( 'login_footer' ) ) {
				add_filter( 'registration_errors', array( __CLASS__, 'deconflict_nua_02_01' ), 9999 );
			}
		}
	}

	static public function deconflict_nua_02_01( $errors ) {
		if( !empty( $errors ) && is_object( $errors ) && $errors->get_error_code() ) { return $errors; }
		if( class_exists( 'pw_new_user_approve' ) && method_exists( 'pw_new_user_approve', 'show_user_pending_message' ) ) {
			if( empty( $errors ) || !is_object( $errors ) ) { $errors = new WP_Error; }
			pw_new_user_approve::instance()->show_user_pending_message( $errors );
		}
		return $errors;
	}

	static public function deconflict_po_01() {
		/* Make sure WP-SpamShield does not get disabled or hindered */
		$all_options = wp_load_alloptions();
		foreach( $all_options  as $option => $value ) {
			if( 0 === strpos( $option, 'PO_disabled_' ) && is_array( $value ) ) {
				foreach( $value  as $k => $v ) {
					if( 0 === strpos( $v, WPSS_PLUGIN_NAME ) ) { unset( $value[$k] ); }
				}
				$value = array_values( $value ); update_option( $option, $value );
			}
		}
		update_option( 'PO_plugin_order', array() );
	}

	static public function deconflict_gwgb_01() {
		$pref = 'gwolle_gb-';
		$mod_options = array( 'akismet-active' => 'false', 'antispam-answer' => '', 'antispam-question' => '', 'form_ajax' => 'false', 'honeypot' => 'false', 'moderate-entries' => 'true', 'nonce' => 'false', 'form' => array(), );
		foreach( $mod_options as $k => $v ) {
			if( 'form' === $k ) {
				$c = array( __CLASS__, 'deconflict_gwgb_04' );
			} else {
				$c = ( '' === $v || NULL === $v ) ? '__return_empty_string' : '__return_'.$v;
				add_filter( 'pre_option_'.$pref.$k, $c, 100, 1 );
			}
			add_filter( 'option_'.$pref.$k, $c, 100, 1 );
			add_filter( 'pre_update_option_'.$pref.$k, $c, 100, 1 );
		}
	}

	static public function deconflict_gwgb_02( $var = NULL ) {
		$pref = 'gwolle_gb-';
		$form = get_option( $pref.'form', array() );
		$form = self::deconflict_gwgb_04( $form );
		$mod_options = array( 'akismet-active' => 'false', 'antispam-answer' => '', 'antispam-question' => '', 'form_ajax' => 'false', 'honeypot' => 'false', 'moderate-entries' => 'true', 'nonce' => 'false', 'form' => serialize( $form ), );
		foreach( $mod_options as $k => $v ) { update_option( $pref.$k, $v ); }
		if( !empty( $var ) ) { return $var; }
	}

	static public function deconflict_gwgb_03( $form_append = NULL ) {
		if( rs_wpss_is_user_admin() || rs_wpss_is_admin_sproc() || self::is_builder_active() ) { return $form_append; }
		$spamshield_options = parent::get_option();
		if( !empty( $spamshield_options['disable_misc_form_shield'] ) ) { return $form_append; }
		$wpss_string = parent::insert_footer_js( TRUE );
		$form_append = "\n".$wpss_string."\n"."\n";
		return $form_append;
	}

	static public function deconflict_gwgb_04( $form = array() ) {
		$mod_form = array( 'form_name_enabled' => 'true', 'form_name_mandatory' => 'true', 'form_email_enabled' => 'true', 'form_email_mandatory' => 'true', 'form_message_enabled' => 'true', 'form_message_mandatory' => 'true', 'form_antispam_enabled' => 'false', 'form_recaptcha_enabled' => 'false', );
		$form = ( !empty( $form ) && is_string( $form ) ) ? maybe_unserialize( $form ) : $form;
		$form = ( !empty( $form ) && is_array( $form ) ) ? $form : array();
		if( !empty( $form ) && is_array( $form ) ) {
			foreach( $mod_form as $k => $v ) {
				if( !isset( $form[$k] ) || $form[$k] !== $v ) { $form[$k] = $v; }
			}
		}
		return $form;
	}

	/**
	 *	Comment Form Compatibility
	 *	@dependencies	...
	 *	@since			...
	 */
	static public function comment_form() {
		if( rs_wpss_is_admin_sproc() ) { return; }

		/* Vantage Theme by Appthemes ( https://www.appthemes.com/themes/vantage/ ) */
		global $wpss_theme_vantage;
		if( !empty( $wpss_theme_vantage ) || ( defined( 'APP_FRAMEWORK_DIR_NAME' ) && defined( 'VA_VERSION' ) ) ) {
			$wpss_theme_vantage = TRUE;
			return TRUE;
		} else {
			$theme				= wp_get_theme();
			if( !empty( $theme ) && is_object( $theme ) ) {
				$theme_name		= $theme->get( 'Name' );
				$theme_author	= $theme->get( 'Author' );
				if( 'Vantage' === $theme_name && 'AppThemes' === $theme_author ) {
					$wpss_theme_vantage = TRUE;
					return TRUE;
				}
			}
		}

		/* Add next... */

		return FALSE;
	}

	/**
	 *	Footer JS Compatibility
	 *	@dependencies	...
	 *	@since			...
	 */
	static public function footer_js() {
		if( rs_wpss_is_admin_sproc() ) { return; }
		$js = '';

		/* Vantage Theme by Appthemes ( https://www.appthemes.com/themes/vantage/ ) */
		global $wpss_theme_vantage;
		$v_js = ', #add-review-form';
		if( !empty( $wpss_theme_vantage ) || ( defined( 'APP_FRAMEWORK_DIR_NAME' ) && defined( 'VA_VERSION' ) ) ) {
			$wpss_theme_vantage = TRUE;
			$js .= $v_js;
		} else {
			$theme				= wp_get_theme();
			if( !empty( $theme ) && is_object( $theme ) ) {
				$theme_name		= $theme->get( 'Name' );
				$theme_author	= $theme->get( 'Author' );
				if( 'Vantage' === $theme_name && 'AppThemes' === $theme_author ) {
					$wpss_theme_vantage = TRUE;
					$js .= $v_js;
				}
			}
		}

		/* Add next... */

		return $js;
	}

	/**
	 *	Miscellaneous Form Spam Check Bypass
	 *	Check if Anti-Spam for Miscellaneous Forms should be bypassed
	 *	@dependencies	...
	 *	@since			...
	 */
	static public function misc_form_bypass() {

		/* Setup necessary variables */
		$url		= WPSS_THIS_URL;
		$url_lc		= WPSS_Func::lower( $url );
		$req_uri	= $_SERVER['REQUEST_URI'];
		$req_uri_lc	= WPSS_Func::lower( $req_uri );
		$post_count = count( $_POST );
		$ip			= parent::get_ip_addr();
		$user_agent = rs_wpss_get_user_agent();
		$referer	= rs_wpss_get_referrer();

		/* IP / PROXY INFO - BEGIN */
		$GLOBALS['wpss_ip_proxy_info'] = rs_wpss_ip_proxy_info(); extract( $GLOBALS['wpss_ip_proxy_info'] );
		/* IP / PROXY INFO - END */

		/* GEOLOCATION */
		if( $post_count == 6 && isset( $_POST['updatemylocation'], $_POST['log'], $_POST['lat'], $_POST['country'], $_POST['zip'], $_POST['myaddress'] ) ) { return TRUE; }

		/* WP Remote */
		if( defined( 'WPRP_PLUGIN_SLUG' ) && !empty( $_POST['wpr_verify_key'] ) && parent::preg_match( "~\ WP\-Remote$~", $user_agent ) && parent::preg_match( "~\.amazonaws\.com$~", $rev_dns ) ) { return TRUE; }

		/* Ecommerce Plugins */
		if( self::is_ecom_enabled() && !self::is_woocom_enabled() && $fcrdns === '[Verified]' ) {
			/* PayPal, Stripe, Authorize.net, Mollie, Worldpay, etc */
			if(
				( $user_agent === 'PayPal IPN ( https://www.paypal.com/ipn )' && parent::preg_match( "~(^|\.)paypal\.com$~", $rev_dns ) ) ||
				$rev_dns === 'api.stripe.com' ||
				parent::preg_match( "~(^|\.)(2checkout\.com|authorize\.net|bitpay\.com|mollie\.com|payfast\.co\.za|paylane\.com|simplifycommerce\.com|stripe\.com|wepayapi\.com|worldpay\.com)$~", $rev_dns )
			) { return TRUE; }
		}

		/* WooCommerce Payment Gateways / Endpoints / AJAX / REST */
		if( self::is_woocom_enabled() ) {
			if( ( $user_agent === 'PayPal IPN ( https://www.paypal.com/ipn )' && parent::preg_match( "~^(ipn|ipnpb|notify|reports)(\.sandbox)?\.paypal\.com$~", $rev_dns ) ) || strpos( $req_uri, 'WC_Gateway_Paypal' ) !== FALSE ) { return TRUE; }
			/* Plugin: 'woocommerce-gateway-payfast/gateway-payfast.php' */
			if( parent::preg_match( "~(^|\.)payfast\.co\.za$~", $rev_dns ) || ( strpos( $req_uri, 'wc-api' ) !== FALSE && strpos( $req_uri, 'WC_Gateway_PayFast' ) !== FALSE ) ) { return TRUE; }
			/* $wc_gateways = array( 'WC_Gateway_BACS', 'WC_Gateway_Cheque', 'WC_Gateway_COD', 'WC_Gateway_Paypal', 'WC_Addons_Gateway_Simplify_Commerce', 'WC_Gateway_Simplify_Commerce' ); */
			if( parent::preg_match( "~((\?|\&)wc\-api\=WC_(Addons_)?Gateway_|/wc\-api/.*WC_(Addons_)?Gateway_)~", $req_uri ) ) { return TRUE; }
			/* See: woocommerce/includes/wc-conditional-functions.php */
			$wc_funcs = array( 'is_wc_endpoint_url', 'is_shop', 'is_product_taxonomy', 'is_product', 'is_cart', 'is_checkout', );
			foreach( $wc_funcs as $i => $f ) {
				if( function_exists( $f ) && (bool) @$f() ) { return TRUE; }
			}
			if( rs_wpss_is_wc_ajax_request() || self::is_wc_doing_rest() ) { return TRUE; }
			if( rs_wpss_is_doing_nojax_rest() && rs_wpss_is_json_request() && 0 === strpos( $user_agent, $wc_rest_api_ua ) ) { return TRUE; }
		}

		/* Easy Digital Downloads Payment Gateways */
		if( self::is_edd_active() ) {
			if( ( $user_agent === 'PayPal IPN ( https://www.paypal.com/ipn )' && parent::preg_match( "~^(ipn|ipnpb|notify|reports)(\.sandbox)?\.paypal\.com$~", $rev_dns ) ) || ( !empty( $_GET['edd-listener'] ) && $_GET['edd-listener'] === 'IPN' )  || ( strpos( $req_uri, 'edd-listener' ) !== FALSE && strpos( $req_uri, 'IPN' ) !== FALSE ) ) { return TRUE; }
			if( ( !empty( $_GET['edd-listener'] ) && $_GET['edd-listener'] === 'amazon' ) || ( strpos( $req_uri, 'edd-listener' ) !== FALSE && strpos( $req_uri, 'amazon' ) !== FALSE ) ) { return TRUE; }
			if( !empty( $_GET['edd-listener'] ) || strpos( $req_uri, 'edd-listener' ) !== FALSE ) { return TRUE; }
			if( !empty( $_GET['edd_action'] ) || !empty( $_POST['edd_action'] ) ) { return TRUE; }
			if( !empty( $GLOBALS['wp_query']->query_vars['edd-api'] ) ) { return TRUE; }
		}

		/* Gravity Forms PayPal Payments Standard Add-On ( http://www.gravityforms.com/add-ons/paypal/ ) */
		if( ( defined( 'GF_MIN_WP_VERSION' ) && defined( 'GF_PAYPAL_VERSION' ) ) || ( class_exists( 'GFForms' ) && class_exists( 'GF_PayPal_Bootstrap' ) ) || self::is_plugin_active( 'gravityformspaypal/paypal.php' ) ) {
			if( ( FALSE !== strpos( $url, 'gf_paypal_ipn' ) || ( !empty( $_GET['page'] ) && 'gf_paypal_ipn' === $_GET['page'] ) ) && isset( $_POST['ipn_track_id'], $_POST['payer_id'], $_POST['receiver_id'], $_POST['txn_id'], $_POST['txn_type'], $_POST['verify_sign'] ) ) { return TRUE; }
			if( ( FALSE !== strpos( $url, 'gf_paypal_return' ) || !empty( $_GET['gf_paypal_return'] ) ) && isset( $_POST['payer_id'], $_POST['receiver_id'], $_POST['txn_id'], $_POST['txn_type'], $_POST['verify_sign'] ) ) { return TRUE; }
		}

		/* PayPal IPN */
		if(
			isset( $_POST['ipn_track_id'], $_POST['payer_id'], $_POST['payment_type'], $_POST['payment_status'], $_POST['receiver_id'], $_POST['txn_id'], $_POST['txn_type'], $_POST['verify_sign'] )
			&& FALSE !== strpos( $req_uri_lc, 'paypal' )
			&& $user_agent === 'PayPal IPN ( https://www.paypal.com/ipn )'
			&& parent::preg_match( "~^(ipn|ipnpb|notify|reports)(\.sandbox)?\.paypal\.com$~", $rev_dns )
			&& $fcrdns === '[Verified]'
		) { return TRUE; }

		/* Clef - TO DO: Remove after July 7, 2017 - https://jetpack.com/for/clef/ */
		if( defined( 'CLEF_VERSION' ) ) {
			if( parent::preg_match( "~^Clef/[0-9](\.[0-9]+)+\ \(https\://getclef\.com\)$~", $user_agent ) && parent::preg_match( "~((^|\.)clef\.io|\.amazonaws\.com)$~", $rev_dns ) ) { return TRUE; }
		}

		/* OA Social Login */
		if( defined( 'OA_SOCIAL_LOGIN_VERSION' ) ) {
			$ref_dom_rev = strrev( rs_wpss_get_domain( $referer ) ); $oa_dom_rev = strrev( 'api.oneall.com' );
			if( $post_count >= 4 && isset( $_GET['oa_social_login_source'], $_POST['oa_action'], $_POST['oa_social_login_token'], $_POST['connection_token'], $_POST['identity_vault_key'] ) && $_POST['oa_action'] === 'social_login' && strpos( $ref_dom_rev, $oa_dom_rev ) === 0 ) { return TRUE; }
		}

		/* IFTTT */
		if( rs_wpss_is_xmlrpc() && $user_agent === 'IFTTT production' && parent::preg_match( "~\.amazonaws\.com$~", $rev_dns ) && $fcrdns === '[Verified]' ) { return TRUE; }

		/* Amazon SNS - http://docs.aws.amazon.com/sns/latest/dg/json-formats.html */
		if( $user_agent === 'Amazon Simple Notification Service Agent' && isset( $_SERVER['HTTP_X_AMZ_SNS_MESSAGE_TYPE'], $_SERVER['HTTP_X_AMZ_SNS_MESSAGE_ID'], $_SERVER['HTTP_X_AMZ_SNS_TOPIC_ARN'], $_SERVER['CONTENT_TYPE'], $_POST['HTTP_RAW_POST_DATA'] ) ) {
			$_POST['HTTP_RAWDS_POST_DATA'] = trim( stripslashes( $_POST['HTTP_RAW_POST_DATA'] ) );
			if(
				   parent::preg_match( "~text/plain~i", $_SERVER['CONTENT_TYPE'] )
				&& parent::preg_match( "~^(SubscriptionConfirmation|Notification|UnsubscribeConfirmation)$~", $_SERVER['HTTP_X_AMZ_SNS_MESSAGE_TYPE'] )
				&& parent::preg_match( "~\"Type\"\ \:\ \"(SubscriptionConfirmation|Notification|UnsubscribeConfirmation)\",~", $_POST['HTTP_RAWDS_POST_DATA'] )
				&& parent::preg_match( "~^[a-z0-9\-]+$~", $_SERVER['HTTP_X_AMZ_SNS_MESSAGE_ID'] )
				&& parent::preg_match( "~\"MessageId\"\ \:\ \"".preg_quote($_SERVER['HTTP_X_AMZ_SNS_MESSAGE_ID'])."\",~", $_POST['HTTP_RAWDS_POST_DATA'] )
				&& parent::preg_match( "~^arn\:aws\:sns\:([a-z0-9\-]+)\:[a-z0-9]+\:.+$~", $_SERVER['HTTP_X_AMZ_SNS_TOPIC_ARN'], $amz_sns_topic_arn_matches )
				&& parent::preg_match( "~\"TopicArn\"\ \:\ \"".preg_quote($_SERVER['HTTP_X_AMZ_SNS_TOPIC_ARN'])."\",~", $_POST['HTTP_RAWDS_POST_DATA'] )
				&& parent::preg_match( "~\"SigningCertURL\"\ \:\ \"https\://sns\." . preg_quote( $amz_sns_topic_arn_matches[1] ) . "\.amazonaws\.com/SimpleNotificationService\-[a-z0-9]+\.pem\"~", $_POST['HTTP_RAWDS_POST_DATA'] )
				&& $fcrdns === '[Verified]'
			) { unset( $_POST['HTTP_RAWDS_POST_DATA'] ); return TRUE; }
			unset( $_POST['HTTP_RAWDS_POST_DATA'] );
		}

		/**
		 *	Slim Stat Analytics - https://wordpress.org/plugins/wp-slimstat/
		 *	@since	1.9.9.9.5
		 */
		if( rs_wpss_is_doing_ajax() && 'POST' === WPSS_REQUEST_METHOD && !empty( $_POST['action'] ) && 'slimtrack' === $_POST['action'] && self::is_plugin_active( 'wp-slimstat' ) ) {
			return TRUE;
		}

		/* Nothing was triggered */
		return FALSE;
	}

	/**
	 *	Detect if site is ecommerce (store), or using an ecommerce plugin
	 *	@dependencies	...
	 *	@since			...
	 */
	static public function is_ecom_enabled() {
		global $wpss_ecom_enabled,$wpss_woocom_enabled;
		if( !empty( $wpss_ecom_enabled ) || !empty( $wpss_woocom_enabled ) ) { $wpss_ecom_enabled = TRUE; return TRUE; }
		/**
		 *	Users can manually set to TRUE in wp-config.php (For example, if user has a custom or unknown ecommerce package)
		 *	Plugin Developers can use WP-SpamShield filter hook 'wpss_misc_form_spam_check_bypass'
		 */
		if( defined( 'WPSS_CUSTOM_ECOM' ) && WPSS_CUSTOM_ECOM ) { $wpss_ecom_enabled = TRUE; return TRUE; }
		/**
		 *	Detect popular e-commerce plugins
		 */
		$ecom_plug_constants = array(
			'AFFILIATES_CORE_VERSION', 'AL_BASE_PATH', 'CFCORE_VER', 'EC_CURRENT_VERSION', 'ECWID_PLUGIN_DIR', 'EDD_VERSION', 'EME_DB_VERSION', 'ESHOP_VERSION', 'GF_AUTHORIZENET_VERSION', 'GF_PAYFAST_VERSION', 'GF_PAYPAL_VERSION', 'GF_STRIPE_VERSION', 'GFP_STRIPE_FILE', 'GIVE_VERSION', 'JIGOSHOP_VERSION', 'MEPR_VERSION', 'MP_LITE', 'PMPRO_VERSION', 'SIMPAY_VERSION', 'SIMPLE_WP_MEMBERSHIP_VER', 'UPCP_CD_PLUGIN_PATH', 'USCES_VERSION', 'WC_PP_PRO_ADDON_VERSION', 'wcv_plugin_dir', 'WOOCOMMERCE_VERSION', 'WC_VERSION', 'WP_CART_VERSION', 'WPDM_Version', 'WPPIZZA_VERSION', 'WPSC_VERSION', 'WPSHOP_DIR', 'WS_PLUGIN__S2MEMBER_VERSION', 'WUSPSC_VERSION', 'xoousers_url', 'uultraxoousers_pro_url', 'YITH_WCSTRIPE_VERSION',
		);
		foreach( $ecom_plug_constants as $k => $p ) { if( defined( $p ) ) { $wpss_ecom_enabled = TRUE; return TRUE; } }
		$ecom_plug_classes = array(
			'Easy_Digital_Downloads', 'eCommerce_Product_Catalog', 'GF_AuthorizeNet_Bootstrap', 'GF_PayFast_Bootstrap', 'GF_PayPal_Bootstrap', 'GF_Stripe_Bootstrap', 'Give', 'IT_Exchange', 'MarketPress', 'ShoppLoader', 'WC_Paypal_Pro_Gateway_Addon', 'WC_Vendors', 'WooCommerce', 'WP_eCommerce',
		);
		foreach( $ecom_plug_classes as $k => $p ) { if( class_exists( $p ) ) { $wpss_ecom_enabled = TRUE; return TRUE; } }
		$ecom_plugs = array(
			'affiliates', 'caldera-forms', 'download-manager', 'easy-digital-downloads', 'ecommerce-product-catalog', 'ecwid-shopping-cart', 'edd-invoices', 'edd-recurring', 'edd-software-licensing/edd-software-licenses.php', 'eshop', 'events-made-easy/events-manager.php', 'events-manager', 'formidable-paypal', 'give', 'gravity-forms-stripe', 'gravityformsauthorizenet/authorizenet.php', 'gravityformspayfast/payfast.php', 'gravityformsstripe/stripe.php', 'gravityformspaypal/paypal.php', 'ithemes-exchange/init.php', 'jigoshop', 'memberpress', 'mollie-payments-for-woocommerce', 'paid-memberships-pro', 'paytium', 'paytium-edd', 's2member', 'shopp/Shopp.php', 'simple-membership/simple-wp-membership.php', 'stripe/stripe-checkout.php', 'ultimate-product-catalogue/UPCP_Main.php', 'usc-e-shop', 'users-ultra/xoousers.php', 'wc-vendors/class-wc-vendors.php', 'woocommerce-paypal-pro-payment-gateway/woo-paypal-pro.php', 'woocommerce', 'wordpress-ecommerce/marketpress.php', 'wordpress-simple-paypal-shopping-cart/wp_shopping_cart.php', 'wp-e-commerce/wp-shopping-cart.php', 'wp-easycart/wpeasycart.php', 'wp-shop-original/wp-shop.php', 'wp-ultra-simple-paypal-shopping-cart/wp_ultra_simple_shopping_cart.php', 'wppizza', 'yith-woocommerce-stripe/init.php', 
		);
		foreach( $ecom_plugs as $k => $p ) { if( self::is_plugin_active( $p ) ) { $wpss_ecom_enabled = TRUE; return TRUE; } }
		/**
		 *	$ecom_plug_str = array();
		 *	$ecom_plug_rgx = array();
		 */
		return FALSE;
	}

	/**
	 *	Detect WooCommerce plugin
	 *	@dependencies	...
	 *	@since			...
	 */
	static public function is_woocom_enabled() {
		global $wpss_ecom_enabled,$wpss_woocom_enabled;
		if( !empty( $wpss_woocom_enabled ) ) { $wpss_ecom_enabled = TRUE; return TRUE; }
		$wc_plug_constants = array( 'WC_VERSION', 'WOOCOMMERCE_VERSION' );
		foreach( $wc_plug_constants as $k => $p ) { if( defined( $p ) ) { $wpss_ecom_enabled = $wpss_woocom_enabled = TRUE; return TRUE; } }
		$ecom_plugs = array( 'woocommerce' );
		foreach( $ecom_plugs as $k => $p ) { if( self::is_plugin_active( $p ) ) { $wpss_ecom_enabled = $wpss_woocom_enabled = TRUE; return TRUE; } }
		return FALSE;
	}

	/**
	 *	Detect if conflicting page builder plugins are active
	 *	@dependencies	...
	 *	@since			...
	 */
	static public function is_builder_active() {
		global $wpss_builder_active; if( !empty( $wpss_builder_active ) ) { return TRUE; }
		$builder_plugs = array( 'beaver-builder-lite-version/fl-builder.php', 'bb-plugin/fl-builder.php' );
		foreach( $builder_plugs as $k => $p ) {
			if( self::is_plugin_active( $p ) ) {
				if( method_exists( 'FLBuilderModel', 'is_builder_active' ) && FLBuilderModel::is_builder_active() ) { $wpss_builder_active = TRUE; return TRUE; }
			}
		}
		return FALSE;
	}

	/**
	 *	Detect JetPack Plugin
	 *	@dependencies	WPSS_Compatibility::is_plugin_active()
	 *	@since			1.9.14
	 */
	static public function is_jp_active() {
		return ( self::is_plugin_active( 'jetpack' ) );
	}

	/**
	 *	Detect Contact Form 7 Plugin
	 *	@dependencies	WPSS_Compatibility::is_plugin_active()
	 *	@since			1.9.14
	 */
	static public function is_cf7_active() {
		return ( self::is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) );
	}

	/**
	 *	Detect Gravity Forms Plugin
	 *	@dependencies	WPSS_Compatibility::is_plugin_active()
	 *	@since			1.9.14
	 */
	static public function is_gf_active() {
		return ( self::is_plugin_active( 'gravityforms' ) );
	}

	/**
	 *	Detect Easy Digital Downloads Plugin
	 *	@dependencies	WPSS_Compatibility::is_plugin_active()
	 *	@since			1.9.15
	 */
	static public function is_edd_active() {
		return ( self::is_plugin_active( 'easy-digital-downloads' ) );
	}

	/**
	 *	Check if EDD is processing an API request
	 *	@dependencies	WPSS_Compatibility::is_plugin_active()
	 *	@since			1.9.15
	 */
	static public function is_edd_doing_api() {
		return ( self::is_edd_active() && defined( 'EDD_DOING_API' ) && EDD_DOING_API );
	}

	/**
	 *	Check for Surrogates
	 *	- Server Caching, Reverse Poxies, WAFS: Varnish, Cloudflare (Rocket Loader), Sucuri WAF, Incapsula, etc.
	 *	- Specific web hosts that use aggressive caching (eg. Varnish or other): WP Engine, Dreamhost, SiteGround, Bluehost, GoDaddy, Lightning Base, Pagely, ...
	 *	@dependencies	WPSS_Utils::get_ip_dns_params(), WPSS_Utils::get_web_host(), WP_SpamShield::update_option(), WP_SpamShield::is_varnish_active()(), WP_SpamShield::get_option(), ...
	 *	@since			1.9.9.5
	 */
	static public function is_surrogate() {
		global $wpss_surrogate;
		if( isset( $wpss_surrogate ) && is_bool( $wpss_surrogate ) ) { return $wpss_surrogate; }
		$wpss_surrogate	= FALSE;
		$web_host		= WPSS_Utils::get_web_host( WPSS_Utils::get_ip_dns_params() );
		if( ( !empty( $web_host ) && ( $web_host === 'WP Engine' || $web_host === 'Dreamhost' || $web_host === 'SiteGround' || $web_host === 'Bluehost' || $web_host === 'GoDaddy' || $web_host === 'Lightning Base' || $web_host === 'Pagely' ) ) || self::is_varnish_active() || self::is_lscache_active() ) {
			$wpss_surrogate	= TRUE;
			parent::update_option( array( 'surrogate' => $wpss_surrogate ) ); return TRUE;
		}
		if( empty( $wpss_surrogate ) ) {
			$wpss_surrogate = parent::get_option( 'surrogate' );
		}
		if( empty( $wpss_surrogate ) ) {
			$wpss_surrogate = FALSE;
		}
		parent::update_option( array( 'surrogate' => $wpss_surrogate, 'web_host' => $web_host ) );
		return $wpss_surrogate;
	}

	/**
	 *	Varnish detection
	 *	@dependencies	WPSS_PHP::extension_loaded(), WP_SpamShield::update_option(), WP_SpamShield::is_plugin_active(),
	 *	@since			1.9.9.5
	 */
	static public function is_varnish_active() {
		global $wpss_varnish_active,$wpss_surrogate,$_WPSS_ENV;
		if( isset( $wpss_varnish_active ) && is_bool( $wpss_varnish_active ) ) { return $wpss_varnish_active; }
		$varnish_srv_var		= array( 'HTTP_X_VARNISH', );
		$varnish_env_var		= array( 'HTTP_X_VARNISH', );
		$varnish_php_const		= array( 'VARNISH_COMPAT_2', 'VARNISH_COMPAT_3', 'VARNISH_CONFIG_COMPAT', 'VARNISH_CONFIG_HOST', 'VARNISH_CONFIG_IDENT', 'VARNISH_CONFIG_PORT', 'VARNISH_CONFIG_SECRET', 'VARNISH_CONFIG_TIMEOUT', 'VARNISH_STATUS_AUTH', 'VARNISH_STATUS_CANT', 'VARNISH_STATUS_CLOSE', 'VARNISH_STATUS_COMMS', 'VARNISH_STATUS_OK', 'VARNISH_STATUS_PARAM', 'VARNISH_STATUS_SYNTAX', 'VARNISH_STATUS_TOOFEW', 'VARNISH_STATUS_TOOMANY', 'VARNISH_STATUS_UNIMPL', 'VARNISH_STATUS_UNKNOWN', );
		$varnish_plug_const		= array( 'DHDO', 'DHDO_PLUGIN_DIR', 'DREAMSPEED_VERSION', 'VHP_VARNISH_IP', );
		$varnish_constants		= array_merge( $varnish_php_const, $varnish_plug_const );
		$varnish_plugs			= array( 'dreamobjects', 'dreamspeed-cdn', 'varnish-http-purge', );
		$wpss_varnish_active	= WPSS_PHP::extension_loaded( 'varnish' );
		if( empty( $wpss_varnish_active ) ) {
			foreach( $varnish_srv_var as $i => $v ) {
				if( !empty( $_SERVER[$v] ) ) { $wpss_varnish_active = TRUE; break; }
			}
		}
		if( empty( $wpss_varnish_active ) ) {
			foreach( $varnish_env_var as $i => $v ) {
				if( !empty( $_WPSS_ENV[$v] ) ) { $wpss_varnish_active = TRUE; break; }
			}
		}
		if( empty( $wpss_varnish_active ) ) {
			foreach( $varnish_constants as $i => $c ) {
				if( defined( $c ) ) { $wpss_varnish_active = TRUE; break; }
			}
		}
		if( empty( $wpss_varnish_active ) ) {
			foreach( $varnish_plugs as $i => $p ) {
				if( self::is_plugin_active( $p ) ) { $wpss_varnish_active = TRUE; break; }
			}
		}
		if( !empty( $wpss_varnish_active ) ) {
			$wpss_surrogate	= $wpss_varnish_active = TRUE;
			parent::update_option( array( 'surrogate' => $wpss_surrogate ) );
			return TRUE;
		}
		$wpss_varnish_active = FALSE;
		return FALSE;
	}

	/**
	 *	Litespeed detection
	 *	@dependencies	WPSS_PHP::extension_loaded(),
	 *	@since			1.9.9.9.7
	 */
	static public function is_litespeed() {
		global $is_litespeed;
		if( isset( $is_litespeed ) && is_bool( $is_litespeed ) ) { return $is_litespeed; }
		$is_litespeed	= ( FALSE !== strpos( $_SERVER['SERVER_SOFTWARE'], 'LiteSpeed' ) || 'litespeed' === PHP_SAPI || WPSS_PHP::extension_loaded( 'litespeed' ) );
		return $is_litespeed;
	}

	/**
	 *	LSCache detection (Litespeed Cache)
	 *	@dependencies	WPSS_Compatibility::is_litespeed(),
	 *	@since			1.9.9.9.7
	 */
	static public function is_lscache_active() {
		global $wpss_lscache_active;
		if( isset( $wpss_lscache_active ) && is_bool( $wpss_lscache_active ) ) { return $wpss_lscache_active; }
		$wpss_lscache_active = ( self::is_litespeed() && ( !empty( $_SERVER['X-LSCACHE'] ) || !empty( $_SERVER['HTTP_X_LSCACHE'] ) ) );
		return $wpss_lscache_active;
	}

	/**
	 *	Check if Contact Form 7 is doing REST
	 *	@dependencies	WPSS_Func::lower(), WPSS_Compatibility::is_cf7_active(), rs_wpss_is_doing_rest(), ...
	 *	@since			1.9.12 as rs_wpss_casetrans()
	 *  @moved			1.9.14 to WPSS_Compatibility class
	 */
	static public function is_cf7_doing_rest() {
		$req_uri = WPSS_Func::lower( $_SERVER['REQUEST_URI'] );
		return ( FALSE !== strpos( $req_uri, '/contact-form-7/v' ) && self::is_cf7_active() && rs_wpss_is_doing_rest() && defined( 'WPCF7_VERSION' ) && version_compare( WPCF7_VERSION, '4.8', '>=' ) );
	}

	/**
	 *	Check if JetPack is doing REST
	 *	To check if JP is doing settings, use `WPSS_Compatibility::is_jp_doing_rest( TRUE )`
	 *	@dependencies	WPSS_Func::lower(), WPSS_Compatibility::is_jp_active(), rs_wpss_is_doing_rest(), ...
	 *	@since			1.9.14
	 */
	static public function is_jp_doing_rest( $chk_set = FALSE ) {
		$req_uri	= WPSS_Func::lower( $_SERVER['REQUEST_URI'] );
		$set_pass	= ( FALSE === $chk_set || parent::preg_match( "~jetpack/v[\d\.]+/settings~i", $req_uri ) );
		return ( FALSE !== strpos( $req_uri, '/jetpack/v' ) && TRUE === $set_pass && self::is_jp_active() && rs_wpss_is_doing_rest() );
	}

	/**
	 *	Check if WooCommerce is doing REST
	 *	@dependencies	WPSS_Func::lower(), WPSS_Compatibility::is_woocom_enabled(), rs_wpss_is_doing_rest(), ...
	 *	@since			1.9.16
	 */
	static public function is_wc_doing_rest() {
		$req_uri = WPSS_Func::lower( $_SERVER['REQUEST_URI'] );
		return ( FALSE !== strpos( $req_uri, '/wc/v' ) && self::is_woocom_enabled() && rs_wpss_is_doing_rest() );
	}

	/**
	 *	Check if WooCommerce API Request
	 *	@dependencies	WPSS_Func::lower(), WPSS_Compatibility::is_woocom_enabled(), rs_wpss_is_doing_rest(), ...
	 *	@since			1.9.16
	 */
	static public function is_wc_api_request() {
		return ( self::is_wc_api_ua() && self::is_woocom_enabled() && rs_wpss_is_doing_nojax_rest() && rs_wpss_is_json_request() );
	}

	/**
	 *	Check if WooCommerce API User Agent
	 *	@dependencies	rs_wpss_get_user_agent()
	 *	@since			1.9.16
	 */
	static public function is_wc_api_ua() {
		$user_agent	= rs_wpss_get_user_agent();
		$wc_api_ua	= 'WooCommerce API Client';
		return ( 0 === strpos( $user_agent, $wc_api_ua ) );
	}

	/* Add next... */

}

