<?php
/**
 *  WP-SpamShield Security
 *  File Version 1.9.16
 */

/* Make sure file remains secure if called directly */
if( !defined( 'ABSPATH' ) || !defined( 'WPSS_VERSION' ) ) {
	if( !headers_sent() ) { @header( $_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden', TRUE, 403 ); @header( 'X-Robots-Tag: noindex', TRUE ); }
	die( 'ERROR: Direct access to this file is not allowed.' );
}
/* Prevents unintentional error display if WP_DEBUG not enabled. */
if( TRUE !== WPSS_DEBUG && TRUE !== WP_DEBUG ) { @ini_set( 'display_errors', 0 ); @error_reporting( 0 ); }



class WPSS_Security extends WP_SpamShield {

	/**
	 *  WP-SpamShield Security Class
	 */

	function __construct() {
		/**
		 *  Do nothing...for now
		 */
	}

	/**
	 *  Security Init
	 *  Run early security protocols
	 *  @dependencies	...
	 *  @used by		WP_SpamShield->__construct()
	 *  @hook			action|plugins_loaded|WPSS_F0
	 *  @since			1.9.9.9.9
	 */
	static public function security_init() {
		/**
		 *	Make some security enhancements at runtime via ini directives and WP hooks
		 *	- Retrieval of remote files/URLs should NOT be done using fopen()/file_get_contents()
		 *		- Attempt to disable at runtime to prevent poorly coded plugins from introducing security risks
		 *	- Ensure that cron requests verify SSL/TLS certs
		 *	- More secure session cookie handling
		 */
		global $is_apache,$is_IIS;
		if( parent::is_php_ver( '5.6' ) && $is_apache ) {	/* Apache and PHP 5.6+ only, to prevent issues with older setups */
			$ini_path		= ( rs_wpss_is_function_enabled( 'php_ini_loaded_file' ) ) ? (string) @php_ini_loaded_file() : '';
			$ini_file		= ( !empty( $ini_path ) && rs_wpss_is_function_enabled( 'parse_ini_file' ) ) ? (array) @parse_ini_file( $ini_path ) : array();
			$ini_set		= ( rs_wpss_is_function_enabled( 'ini_set' ) );
			$ini_vals		= array( 'allow_url_fopen' => 0, 'allow_url_include' => 0, 'session.cookie_httponly' => 1, );
			$ini_vals_ssl	= array( 'session.cookie_secure' => 1, );
			$wp_vals		= array( 'https_ssl_verify' => 'true', 'http_request_reject_unsafe_urls' => 'true', );
			$wp_vals_ssl	= array( 'https_local_ssl_verify' => 'true', );
			$wp_cb			= array( 'true', 'false', 'zero', 'null', 'empty_array', 'empty_string' );
			foreach( $ini_vals as $k => $v ) {
				if( $ini_set && WPSS_Utils::is_ini_value_changeable( $k ) && !isset( $ini_file[$k] ) ) { @ini_set( $k, $v ); }
			}
			foreach( $wp_vals as $f => $v ) {
				$cb = ( is_string( $v ) && WPSS_PHP::in_array( $v, $wp_cb ) ) ? '__return_'.$v : $v;
				add_filter( $f, $cb, 100 );
			}
			if( parent::is_https() ) {
				foreach( $ini_vals_ssl as $k => $v ) {
					if( $ini_set && WPSS_Utils::is_ini_value_changeable( $k ) && !isset( $ini_file[$k] ) ) { @ini_set( $k, $v ); }
				}
				foreach( $wp_vals_ssl as $f => $v ) {
					$cb = ( is_string( $v ) && WPSS_PHP::in_array( $v, $wp_cb ) ) ? '__return_'.$v : $v;
					add_filter( $f, $cb, 100 );
				}
			}
		}

		/**
		 *	Hook 'wpss_security_init'
		 *	@since		1.9.9.9.9
		 */
		do_action( 'wpss_security_init' );

		if( is_admin() ) {

			if( parent::is_admin_page() ) {
				/**
				 *	Add directives that should run early on WP-SpamShield admin page
				 */

			}

			/**
			 *	Add directives that should run early in admin
			 */

			/* TO DO: add_action( 'plugins_loaded', array( __CLASS__, 'plugins_loaded_50' ), 50 ); */
		}

	}

	/**
	 *  Check if POST submission is security threat: hack attempt or vulnerability probe
	 *  @dependencies	...
	 *  @used by		...
	 *  @since			...
	 */
	public function check_post_sec() {

		$site_url	= WPSS_SITE_URL;
		$site_dom	= WPSS_SITE_DOMAIN;
		$admin_url	= WPSS_ADMIN_URL.'/';
		$cont_url	= WPSS_CONTENT_DIR_URL.'/';
		$plug_url	= WPSS_PLUGINS_DIR_URL.'/';
		$post_count	= count( $_POST );
		$user_agent = rs_wpss_get_user_agent();
		$req_url	= WPSS_Func::lower( WPSS_THIS_URL );
		$req_ajax	= rs_wpss_is_ajax_request();
		$req_404	= parent::is_404( TRUE ); /* Not all WP sites return proper 404 status. The fact this security check even got activated means it was a 404. */
		$req_hal	= rs_wpss_get_http_accept( TRUE, TRUE, TRUE );
		$req_ha		= rs_wpss_get_http_accept( TRUE, TRUE );

		/* IP / PROXY INFO - BEGIN */
		$GLOBALS['wpss_ip_proxy_info'] = rs_wpss_ip_proxy_info(); extract( $GLOBALS['wpss_ip_proxy_info'] );
		/* IP / PROXY INFO - END */

		/* Short Signatures - Regex */

		$rgx_sig_arr = array( '-e*5l?*B-@yZ_-,8_-lSZ98BC[', '+25-Z9dCZ,87C-7CBlSZ=-C[', 'q-e*5lq?*B-@yZ_-,8_-l', );

		foreach( $_POST as $k => $v ) {
			if( !is_string( $v ) && !is_array( $v ) && !is_object( $v ) ) { continue; }
			if( !is_string( $v ) ) { $v = @WPSS_PHP::json_encode( $v ); }
			$v = WPSS_Func::lower( (string) $v );
			foreach( $rgx_sig_arr as $i => $s ) { /* Switch to single preg_match as this expands, replace nested foreach() */
				$sd = rs_wpss_rbkmd( $s, 'de' );
				if( FALSE !== strpos( $v, $sd ) ) { $_SERVER['WPSS_SEC_THREAT'] = TRUE; return TRUE; }
			}
		}

		/* Full Signatures - Just the beginning of what's to come... - TO DO */

		$signatures = array(
			/* SIGNATURES - BEGIN */

			array(
				'description' 		=> 'Revslider & Showbiz Pro - AJAX Vulnerability', 
				'post_i_min'		=> 2, 
				'post_i_max'		=> 2, 
				'target_urls'		=> array( '/wp-admin/admin-ajax.php', ),
				'ajax_request'		=> FALSE, 
				'404'				=> '*', 
				'session_cookie'	=> FALSE, 
				'hal_signature'		=> array( '', ), 
				'ha_signature'		=> array( '', '*/*', ), 
				'key_val_pairs'		=> array( 
					array( 
						'action'		=> 'revslider_ajax_action', 
						'client_action'	=> 'update_plugin',
						), 
					array( 
						'action'		=> 'showbiz_ajax_action', 
						'client_action'	=> 'update_plugin', 
						), 
					),
				),

			array(
				'description' 		=> 'WP Marketplace <= 2.4.0 & WP Download Manager <=2.7.4 - Remote Code Execution', 
				'post_i_min'		=> 5, 
				'post_i_max'		=> 5, 
				'target_urls'		=> array(), 
				'ajax_request'		=> FALSE, 
				'404'				=> '*', 
				'session_cookie'	=> FALSE, 
				'hal_signature'		=> array( '', ), 
				'ha_signature'		=> array( '', '*/*', ), 
				'key_val_pairs'		=> array( 
					array( 
						'action'		=> 'wpmp_pp_ajax_call', 
						'user_login'	=> '*', 
						'execute'		=> 'wp_insert_user', 
						'role'			=> 'administrator', 
						'user_pass'		=> '*', 
						), 
					array( 
						'action'		=> 'wpdm_ajax_call', 
						'user_login'	=> '*', 
						'execute'		=> 'wp_insert_user', 
						'role'			=> 'administrator', 
						'user_pass'		=> '*', 
						), 
					),
				),

			array(
				'description' 		=> 'WP Symposium <= 14.11 - Shell Upload Vulnerability', 
				'post_i_min'		=> 2, 
				'post_i_max'		=> 3, 
				'target_urls'		=> array( '/wp-content/plugins/wp-symposium/server/php/index.php', ), 
				'ajax_request'		=> FALSE, 
				'404'				=> '*', 
				'session_cookie'	=> FALSE, 
				'hal_signature'		=> array( '', ), 
				'ha_signature'		=> array( '', '*/*', ), 
				'key_val_pairs'		=> array( 
					array( 
						'uploader_url'	=> $plug_url.'/wp-symposium/server/php/', 
						'uploader_uid'	=> '1', 
						), 
					),
				),

			array(
				'description' 		=> 'Ultimate Product Catalogue <= 3.11 - Multiple Vulnerabilities', 
				'post_i_min'		=> 3, 
				'post_i_max'		=> 3, 
				'target_urls'		=> array( '/wp-content/plugins/ultimate-product-catalogue/product-sheets/wp-links-ompt.php', '/wp-content/plugins/ultimate-product-catalogue/product-sheets/wp-includes.php', '/wp-content/plugins/ultimate-product-catalogue/product-sheets/wp-styles.php', ), 
				'ajax_request'		=> FALSE, 
				'404'				=> '*', 
				'session_cookie'	=> FALSE, 
				'hal_signature'		=> array( '', ), 
				'ha_signature'		=> array( '', '*/*', ), 
				'key_val_pairs'		=> array( 
					array( 
						'p2'			=> '2929', 
						'abc28'			=> 'print $_REQUEST[\'p1\'].$_REQUEST[\'p2\']', 
						'p1'			=> '4242', 
						), 
					array( 
						'p2'			=> '2929', 
						'af5f492a1'		=> 'print $_REQUEST[\'p1\'].$_REQUEST[\'p2\']', 
						'p1'			=> '4242', 
						), 
					array( 
						'p2'			=> '2929', 
						'e41e'			=> 'print $_REQUEST[\'p1\'].$_REQUEST[\'p2\']', 
						'p1'			=> '4242', 
						), 
					),
				),

			array(
				'description' 		=> 'Ultimate Product Catalogue <= 3.11 - Multiple Vulnerabilities', 
				'post_i_min'		=> 1, 
				'post_i_max'		=> 1, 
				'target_urls'		=> array( '/wp-content/plugins/ultimate-product-catalogue/product-sheets/wp-setup.php', '/wp-content/plugins/ultimate-product-catalogue/product-sheets/wp-includes.php', ), 
				'ajax_request'		=> FALSE, 
				'404'				=> '*', 
				'session_cookie'	=> FALSE, 
				'hal_signature'		=> array( '', ), 
				'ha_signature'		=> array( '', '*/*', ), 
				'key_val_pairs'		=> array( 
					array( 
						'e51e'			=> 'die(pi());', 
						), 
					array( 
						'af5f492a1'		=> 'die(pi());', 
						), 
					),
				),

			array(
				'description' 		=> 'Simple Ads Manager <= 2.5.94 - Arbitrary File Upload', 
				'post_i_min'		=> 2, 
				'post_i_max'		=> 2, 
				'target_urls'		=> array( '/wp-content/plugins/simple-ads-manager/sam-ajax-admin.php', ), 
				'ajax_request'		=> FALSE, 
				'404'				=> '*', 
				'session_cookie'	=> FALSE, 
				'hal_signature'		=> array( '', ), 
				'ha_signature'		=> array( '', '*/*', ), 
				'key_val_pairs'		=> array( 
					array( 
						'action'		=> 'upload_ad_image', 
						'path'			=> '*', 
						), 
					),
				),

			array(
				'description' 		=> 'Work The Flow File Upload <= 2.5.2 - Shell Upload', 
				'post_i_min'		=> 1, 
				'post_i_max'		=> 1, 
				'target_urls'		=> array( '/wp-content/plugins/work-the-flow-file-upload/public/assets/jquery-file-upload-9.5.0/server/php/index.php', '/assets/plugins/jquery-file-upload/server/php/index.php', ), 
				'ajax_request'		=> FALSE, 
				'404'				=> '*', 
				'session_cookie'	=> FALSE, 
				'hal_signature'		=> array( '', ), 
				'ha_signature'		=> array( '', '*/*', ), 
				'key_val_pairs'		=> array( 
					array( 
						'action'		=> 'upload', 
						), 
					),
				),


			/* SIGNATURES - END */
			);

		/* Run Checks Against Signatures */

		foreach ( $signatures as $i => $sig ) {
			if( !empty( $sig['post_i_min'] ) && ( $post_count < $sig['post_i_min'] || $post_count > $sig['post_i_max'] ) ) { continue; }
			if( !empty( $sig['target_urls'] ) ) { 
				$urls_rgx = WPSS_Filters::get_rgx_ptrn( $sig['target_urls'], '', 'red_str' );
				if( !parent::preg_match( $urls_rgx, $req_url ) ) { continue; }
			}
			if( $sig['ajax_request'] !== '*' && $sig['ajax_request'] !== $req_ajax ) { continue; }
			if( $sig['404'] !== '*' && $sig['404'] !== $req_404 ) { continue; }
			$hal_max = count( $sig['hal_signature'] ) - 1; $m = 0; /* Matches */
			foreach( $sig['hal_signature'] as $i => $hal_sig ) {
				if( $hal_sig == $req_hal ) { $m++; }
				if( $i == $hal_max && $m === 0 ) { continue 2; }
			}
			$ha_max = count( $sig['ha_signature'] ) - 1; $m = 0; /* Matches */
			foreach( $sig['ha_signature'] as $i => $ha_sig ) {
				if( $ha_sig == $req_ha ) { $m++; }
				if( $i == $ha_max && $m === 0 ) { continue 2; }
			}
			foreach( $sig['key_val_pairs'] as $i => $kvp ) {
				$kvp_max = count( $kvp ); $m = 0; /* Matches */
				foreach( $kvp as $k => $v ) {
					if( ( !empty( $_POST[$k] ) && $_POST[$k] === $v ) || ( $v === '*' && isset( $_POST[$k] ) ) ) { $m++; }
					if( $m === $kvp_max ) { $_SERVER['WPSS_SEC_THREAT'] = TRUE; return TRUE; }
				}
			}
		}

		/* Integrate with Server Firewalls */

		$firewall_threat_keys = array( 'WPSS_SEC_THREAT', 'BHCS_SEC_THREAT', 'X_KNOWN_SEC_THREAT', 'X_SITE_ATTACKER', 'X_KNOWN_ATTACKER', );
		foreach( $firewall_threat_keys as $i => $k ) {
			if( !empty( $_SERVER[$k] ) ) { $_SERVER['WPSS_SEC_THREAT'] = TRUE; return TRUE; }
		}

		/* No more tests */

		return FALSE;
	}

	/**
	 *  Ban users by IP address or check if they have been banned
	 *  @param			string		$method		'set'|'chk'
	 *  @dependencies	...
	 *  @used by		...
	 *  @since			1.9.4
	 */
	static public function ip_ban( $method = 'set' ) {
		if( FALSE === WPSS_IP_BAN_ENABLE || TRUE === WPSS_IP_BAN_CLEAR ) { self::clear_ip_ban(); return FALSE; }
		$wpss_ip_ban_disable = parent::get_option( 'ip_ban_disable' );
		if( !empty( $wpss_ip_ban_disable ) ) { self::clear_ip_ban(); return FALSE; }
		$ip = parent::get_ip_addr();
		if( $ip === WPSS_SERVER_ADDR ) { return FALSE; } /* Skip website IP address */
		if( rs_wpss_is_local_request() ) { return FALSE; } /* Skip anything on same C-Block as website */
		/* if( self:no_ban_list() ) { return FALSE; } // SE Bots, SM Bots, etc */
		if( rs_wpss_is_admin_ip( $ip ) || rs_wpss_whitelist_check( NULL, $ip ) ) { return FALSE; }

		/* TO DO: Add logic for reverse proxies */

		$ip_ban_status	= FALSE;
		$wpss_ip_ban	= get_option( 'spamshield_ip_ban' );
		if( empty( $wpss_ip_ban ) ) { $wpss_ip_ban = array(); }
		/* Check */
		if( self::is_valid_ip( $ip ) && WPSS_PHP::in_array( $ip, $wpss_ip_ban ) ) { $ip_ban_status = TRUE; }
		/* Set */
		if( !empty( $ip_ban_status ) || $method === 'set' ) {
			if( count( $wpss_ip_ban ) >= 100 ) { $wpss_ip_ban = array(); }
			if( self::is_valid_ip( $ip ) && !WPSS_PHP::in_array( $ip, $wpss_ip_ban ) ) { $wpss_ip_ban[] = $ip; }
			$wpss_ip_ban = rs_wpss_sort_unique( $wpss_ip_ban );
			foreach( $wpss_ip_ban as $i => $b ) {
				if( rs_wpss_is_admin_ip( $b ) || rs_wpss_whitelist_check( NULL, $b ) ) { unset( $wpss_ip_ban[$i] ); }
			}
			update_option( 'spamshield_ip_ban', $wpss_ip_ban );
			self::ip_ban_htaccess();
			$ip_ban_status = TRUE;
		}
		if( rs_wpss_is_session_active() ) {
			$_SERVER['WPSS_IP_BAN'] = $_SESSION['WPSS_IP_BAN_'.WPSS_HASH] = $ip_ban_status;
			if( !empty( $_SERVER['WPSS_IP_BAN'] ) || !empty( $_SESSION['WPSS_IP_BAN_'.WPSS_HASH] ) ) {
				$_SERVER['WPSS_SEC_THREAT'] = $_SESSION['WPSS_SEC_THREAT_'.WPSS_HASH] = TRUE;
			}
		}
		return $ip_ban_status;
	}

	/**
	 *  Write the updated list of banned IP's to .htaccess.
	 *  @dependencies	...
	 *  @used by		...
	 *  @since			1.9.4
	 */
	static private function ip_ban_htaccess() {
		$hta_bak_dir		= WPSS_CONTENT_DIR_PATH.WPSS_DS.'backup';
		$hta_wpss_bak_dir	= $hta_bak_dir.WPSS_DS.'wp-spamshield';
		$hta_file			= ABSPATH.WPSS_DS.'.htaccess';
		$hta_bak_file		= $hta_wpss_bak_dir.WPSS_DS.'original.htaccess';
		$wpss_index_file	= WPSS_PLUGIN_PATH.WPSS_DS.'index.php';
		$bak_dir_hta_file	= WPSS_PLUGIN_PATH.WPSS_DS.'lib'.WPSS_DS.'sec'.WPSS_DS.'.htaccess';
		$ip 				= parent::get_ip_addr();
		$user_agent 		= rs_wpss_get_user_agent();
		$wpss_ip_ban		= get_option( 'spamshield_ip_ban' ); if( empty( $wpss_ip_ban ) ) { return FALSE; }
		$wpss_ip_ban		= rs_wpss_sort_unique( $wpss_ip_ban );
		$banned_ip_count	= count( $wpss_ip_ban );
		$ip_ban_rgx			= '^(' . str_replace( array( '.', ':', ), array( '\.', '\:', ), implode( '|', $wpss_ip_ban ) ) . ')$';

		$wpss_hta_data = WPSS_EOL.WPSS_EOL.'# BEGIN WP-SpamShield'.WPSS_EOL.WPSS_EOL;
		$wpss_hta_data .= '<IfModule mod_setenvif.c>'.WPSS_EOL."\t".'SetEnvIf Remote_Addr '.$ip_ban_rgx.' WPSS_SEC_THREAT'.WPSS_EOL.'</IfModule>';
		$wpss_hta_data .= WPSS_EOL.WPSS_EOL.'# END WP-SpamShield'.WPSS_EOL.WPSS_EOL;
		$wpss_hta_data_wp = '# BEGIN WordPress';

		@clearstatcache();
		if( @file_exists( $hta_file ) ) {
			if( ! @file_exists( $hta_wpss_bak_dir ) ) {
				@wp_mkdir_p( $hta_wpss_bak_dir );
				WPSS_PHP::chmod( $hta_wpss_bak_dir,	750 );
				WPSS_PHP::chmod( $hta_bak_dir,		750 );
				@copy ( $bak_dir_hta_file,	$hta_wpss_bak_dir.WPSS_DS.'.htaccess'	);
				@copy ( $wpss_index_file,	$hta_wpss_bak_dir.WPSS_DS.'index.php'	);
				@copy ( $bak_dir_hta_file,	$hta_bak_dir.WPSS_DS.'.htaccess'		);
				@copy ( $wpss_index_file,	$hta_bak_dir.WPSS_DS.'index.php'		);
			}
			if( ! @file_exists( $hta_bak_file ) ) {
				@copy ( $hta_file, $hta_bak_file );
			}
			$hta_contents = @file_get_contents( $hta_file );
			if( strpos( $hta_contents, '# BEGIN WP-SpamShield' ) !== FALSE && strpos( $hta_contents, '# END WP-SpamShield' ) !== FALSE ) {
				$hta_contents_mod = @preg_replace( "~#\ BEGIN\ WP-SpamShield[\w\W]+#\ END\ WP-SpamShield~i", trim( $wpss_hta_data, WPSS_EOL ), $hta_contents );
				if( $hta_contents_mod !== $hta_contents ) {
					@file_put_contents( $hta_file, $hta_contents_mod, LOCK_EX );
				}
			} elseif( strpos( $hta_contents, '# BEGIN WordPress' ) !== FALSE ) {
				$hta_contents_mod = @preg_replace( "~#\ BEGIN\ WordPress~i", $wpss_hta_data.$wpss_hta_data_wp, $hta_contents );
				@file_put_contents( $hta_file, $hta_contents_mod, LOCK_EX );
			} else {
				@file_put_contents( $hta_file, WPSS_EOL.WPSS_EOL.$wpss_hta_data.WPSS_EOL.WPSS_EOL, FILE_APPEND | LOCK_EX );
			}
			@parent::append_log_data( NULL, NULL, WPSS_EOL.'IP address banned and added to .htaccess block list. IP: '.$ip );
			@parent::append_log_data( NULL, NULL, WPSS_EOL.'User-Agent: '.$user_agent );
			@parent::append_log_data( NULL, NULL, WPSS_EOL.'$_SERVER Data: '.print_r($_SERVER,1) );
		}
	}

	/**
	 *  Clear IP ban from database and .htaccess.
	 *  @dependencies	...
	 *  @used by		...
	 *  @since			1.9.4
	 */
	static public function clear_ip_ban() {
		update_option( 'spamshield_ip_ban', array() );
		unset( $_SESSION['WPSS_IP_BAN_'.WPSS_HASH], $_SERVER['WPSS_IP_BAN'], $_SESSION['WPSS_SEC_THREAT_'.WPSS_HASH], $_SERVER['WPSS_SEC_THREAT'] );
		self::clear_ip_ban_htaccess();
	}

	/**
	 *  Clear banned IP info from .htaccess.
	 *  @dependencies	...
	 *  @used by		...
	 *  @since			1.9.4
	 */
	static private function clear_ip_ban_htaccess() {
		$hta_bak_dir		= WPSS_CONTENT_DIR_PATH.WPSS_DS.'backup';
		$hta_wpss_bak_dir	= $hta_bak_dir.WPSS_DS.'wp-spamshield';
		$hta_file			= ABSPATH.WPSS_DS.'.htaccess';
		$hta_bak_file		= $hta_wpss_bak_dir.WPSS_DS.'original.htaccess';
		$wpss_index_file	= WPSS_PLUGIN_PATH.WPSS_DS.'index.php';
		$bak_dir_hta_file	= WPSS_PLUGIN_PATH.WPSS_DS.'lib'.WPSS_DS.'sec'.WPSS_DS.'.htaccess';

		$wpss_hta_data = '# BEGIN WP-SpamShield'.WPSS_EOL.WPSS_EOL.'# END WP-SpamShield';

		if( @file_exists( $hta_file ) ) {
			if( ! @file_exists( $hta_wpss_bak_dir ) ) {
				@wp_mkdir_p( $hta_wpss_bak_dir );
				@copy ( $bak_dir_hta_file, $hta_wpss_bak_dir.WPSS_DS.'.htaccess' );
				@copy ( $wpss_index_file, $hta_wpss_bak_dir.WPSS_DS.'index.php' );
				@copy ( $bak_dir_hta_file, $hta_bak_dir.WPSS_DS.'.htaccess' );
				@copy ( $wpss_index_file, $hta_bak_dir.WPSS_DS.'index.php' );
			}
			if( ! @file_exists( $hta_bak_file ) ) {
				@copy ( $hta_file, $hta_bak_file );
			}
			$hta_contents = @file_get_contents( $hta_file );
			if( strpos( $hta_contents, '# BEGIN WP-SpamShield' ) !== FALSE && strpos( $hta_contents, '# END WP-SpamShield' ) !== FALSE ) {
				$hta_contents_mod = @preg_replace( "~#\ BEGIN\ WP-SpamShield[\w\W]+#\ END\ WP-SpamShield~i", $wpss_hta_data, $hta_contents );
				if( $hta_contents_mod !== $hta_contents ) {
					@file_put_contents( $hta_file, $hta_contents_mod, LOCK_EX );
					@parent::append_log_data( NULL, NULL, WPSS_EOL.'Banned IP addresses removed from .htaccess.' );
				}
			}
		}
	}

	/**
	 *  Admin Security Checks
	 *  Check for specific plugin security issues and apply fix or workaround
	 *  @dependencies	rs_wpss_is_admin_sproc(), rs_wpss_is_user_logged_in()
	 *  @used by		WP_SpamShield->__construct()
	 *  @hook			action|admin_init|-1000
	 *  @since			1.9.5.8
	 */
	static public function check_admin_sec() {
		if( rs_wpss_is_admin_sproc( TRUE ) || !rs_wpss_is_user_logged_in() ) { return; }
		if( isset( $_GET['allow_tracking'] ) ) { unset( $_GET['allow_tracking'] ); }

		/* Add next here... */

	}

	static public function get_raw_post_data() {
		global $HTTP_RAW_POST_DATA;
		if( 'POST' === WPSS_REQUEST_METHOD ) {
			$HTTP_RAW_POST_DATA	= ( !isset( $HTTP_RAW_POST_DATA ) ) ? @file_get_contents( 'php://input' ) : $HTTP_RAW_POST_DATA;
			$HTTP_RAW_POST_DATA	= $_SERVER['X_RAW_POST_DATA'] = trim( $HTTP_RAW_POST_DATA );
			return $HTTP_RAW_POST_DATA;
		}
		return NULL;
	}

	/**
	 *  SECURITY - Disable the XML-RPC 'system.multicall' method by defualt
	 *  Protect against XML-RPC brute force amplification attacks without breaking functionality
	 *  Only allow for certain specific conditions
	 *  @dependencies	...
	 *  @used by		WP_SpamShield->__construct()
	 *  @hook			filter|xmlrpc_methods|100
	 *  @since			1.9.7.8
	 */
	static public function disable_xmlrpc_multicall( $methods ) {
		$spamshield_options = parent::get_option();

		/* WPSS Whitelist Check - IP Only */
		if( rs_wpss_whitelist_check() ) { return $methods; }

		/* BYPASS - HOOK */
		$mfsc_bypass = apply_filters( 'wpss_misc_form_spam_check_bypass', FALSE );
		if( !empty( $mfsc_bypass ) ) { return $methods; }

		/**
		 *	Disable system.multicall method by default, to prevent brute force amplification attacks
		 *	Automattic Sites/IPs are whitelisted (eg. akismet.com, automattic.com, jetpack.com, vaultpress.com, woocommerce.com, etc.)
		 *	192.0.64.0-192.0.127.255 (CIDR:192.0.64.0/18)
		 */
		$ip = parent::get_ip_addr();
		if( !parent::is_valid_ip( $ip ) || !parent::preg_match( "~^192\.0\.(6[4-9]|[7-9][0-9]|1[01][0-9]|12[0-7])\.~", $ip ) || WPSS_Filters::skiddie_ua_check() || self::is_brute_force_amp_attack() ) {
			unset( $methods['system.multicall'] );
		}
		if( !empty( $_SERVER['WPSS_SEC_THREAT'] ) || !empty( $_SERVER['WPSS_TOR_EXIT_NODE'] ) || !empty( $_SERVER['WPSS_BRUTE_FORCE_ATTACK'] ) ) {
			$methods = array();
			if( defined( 'JETPACK__VERSION' ) ) {
				add_filter( 'jetpack_xmlrpc_methods',					'__return_empty_array', WPSS_L0 );
				add_filter( 'jetpack_xmlrpc_unauthenticated_methods',	'__return_empty_array', WPSS_L0 );
			}
		}
		return $methods;
	}

	/**
	 *  Detects/blocks brute force attacks
	 *  This is a secondary backstop to other defense measures
	 *  @dependencies	...
	 *  @since			1.9.9.9.9
	 */
	static public function is_brute_force_attack() {
		global $HTTP_RAW_POST_DATA,$wpss_is_brute_force_attack;
		if( isset( $wpss_is_brute_force_attack ) && is_bool( $wpss_is_brute_force_attack ) ) {
			if( !empty( $wpss_is_brute_force_attack ) ) {
				$_SERVER['WPSS_SEC_THREAT'] = $_SERVER['WPSS_BRUTE_FORCE_ATTACK'] = TRUE;
			}
			return $wpss_is_brute_force_attack;
		}
		if( 'POST' !== WPSS_REQUEST_METHOD ) {
			$wpss_is_brute_force_attack = $wpss_is_brute_force_amp_attack = FALSE; return FALSE;
		}
		if( !isset( $HTTP_RAW_POST_DATA ) ) { $HTTP_RAW_POST_DATA = self::get_raw_post_data(); }
		$HRPD		= stripslashes( $HTTP_RAW_POST_DATA );

		$domain		= rs_wpss_get_email_domain( WPSS_SITE_DOMAIN );
		$domstr		= strtok( $domain, '.' ); strtok( '', '' );
		$usr_arr	= array_filter( array( "adm[i1]n", "www+(\.*(c[o0]m|net|[o0]rg))?", rs_wpss_preg_quote( $domain ), $domstr, ) ); /* array_filter( ) --> Remove empty elements */
		$pwd_arr	= array( "wel+c[o0]me", "hel+[o0]+", "test([i1]ng)?", "dem[o0]+", "letme[i1]n", "changeme", "secret", "g[o0]+gle", "myn[o0]+b", "pa[s5]+(w[o0]rd)?", "(qw|az)erty", "(111+|555+|666+|777+|123(456(7890?)?|321)?|(987)?6543210?|abc(def)?|xyz|1q[2a]|qwe|asd|zxc|qaz)+[a-z0-9]*", );
		$pwd_arr	= array_merge( $usr_arr, $pwd_arr );
		$pwd_suf	= array( "111+", "555+", "666+", "777+", "123", "321", "456", "7890?", "20[0-2][0-9]", );
		$usr_rgx	= "(". implode( "|", $usr_arr ) .")+[0-9]?";
		$pwd_rgx	= "(". implode( "|", $pwd_arr ) .")+(". implode( "|", $pwd_suf ) .")*[a-z0-9]*";
		$xml_rgx	= "<struct>[^<>]*<member>[^<>]*<name>methodName</name>[^<>]*<value>[^<>]*<string>(wp|blogger)\.(get(Profile|UsersBlogs)|[a-z]+)</string>[^<>]*</value>[^<>]*</member>[^<>]*<member>[^<>]*<name>params</name>[^<>]*<value>[^<>]*<array>[^<>]*<data>[^<>]*(<value>[^<>]*<string>[0-9]+</string>[^<>]*</value>[^<>]*)?(<value>[^<>]*<string>".$usr_rgx."[^<>]*</string>[^<>]*</value>[^<>]*<value>[^<>]*<string>[^<>]+</string>[^<>]*</value>|<value>[^<>]*<string>[^<>]+</string>[^<>]*</value>[^<>]*<value>[^<>]*<string>".$pwd_rgx."[^<>]*</string>[^<>]*</value>)";
		$req_rgx	= "^(log\=".$usr_rgx."&pwd\=[^\=&]+&wp\-submit\=|log\=[^\=&]+&pwd\=".$pwd_rgx."&wp\-submit\=)"; /* POST requests */
		$bfa_rgx	= ( rs_wpss_is_xmlrpc() ) ? $xml_rgx : $req_rgx;
		$wpss_is_brute_force_attack	= ( parent::preg_match( "~". $bfa_rgx . "~isU", $HRPD ) );
		if( TRUE === $wpss_is_brute_force_attack ) {
			$_SERVER['WPSS_SEC_THREAT'] = $_SERVER['WPSS_BRUTE_FORCE_ATTACK'] = TRUE;
			rs_wpss_ubl_cache( 'set' );
			if( TRUE === WPSS_IP_BAN_ENABLE ) { self::ip_ban(); }
			add_filter( 'authenticate', '__return_null', 19 );
		}
		return $wpss_is_brute_force_attack;
	}

	/**
	 *  Detects/prevents brute force amplification attacks
	 *  This is a secondary backstop to other defense measures
	 *  @dependencies	...
	 *  @since			1.9.9.8.8
	 */
	static public function is_brute_force_amp_attack() {
		global $HTTP_RAW_POST_DATA,$wpss_is_brute_force_attack,$wpss_is_brute_force_amp_attack;
		if( isset( $wpss_is_brute_force_amp_attack ) && is_bool( $wpss_is_brute_force_amp_attack ) ) {
			if( !empty( $wpss_is_brute_force_amp_attack ) ) {
				$wpss_is_brute_force_attack	= $_SERVER['WPSS_SEC_THREAT'] = $_SERVER['WPSS_BRUTE_FORCE_ATTACK'] = $_SERVER['WPSS_BRUTE_FORCE_AMP_ATTACK'] = TRUE;
			}
			return $wpss_is_brute_force_amp_attack;
		}
		if( !isset( $HTTP_RAW_POST_DATA ) ) { $HTTP_RAW_POST_DATA = self::get_raw_post_data(); }
		$HRPD = stripslashes( $HTTP_RAW_POST_DATA );
		$wpss_is_brute_force_amp_attack	= ( self::is_brute_force_attack() && rs_wpss_is_xmlrpc() && FALSE !== stripos( $HRPD, '<methodName>system.multicall</methodName>' ) );
		if( TRUE === $wpss_is_brute_force_amp_attack ) {
			$_SERVER['WPSS_SEC_THREAT'] = $_SERVER['WPSS_BRUTE_FORCE_ATTACK'] = $_SERVER['WPSS_BRUTE_FORCE_AMP_ATTACK'] = TRUE;
			rs_wpss_ubl_cache( 'set' );
			if( TRUE === WPSS_IP_BAN_ENABLE ) { self::ip_ban(); }
			add_filter( 'authenticate', '__return_null', 19 );
		}
		return $wpss_is_brute_force_amp_attack;
	}

	/**
	 *  SECURITY - Checks all incoming POST requests early for malicious behavior
	 *  Misc Form Spam Check - Layer 2
	 *  @dependencies	rs_wpss_is_login_page(), rs_wpss_is_doing_ajax(), WPSS_Security::early_admin_intercept(), rs_wpss_is_local_request(), WPSS_Security::get_raw_post_data(), rs_wpss_whitelist_check(), WPSS_Func::lower(), rs_wpss_get_query_string(), rs_wpss_is_login_page(), WPSS_Compatibility::misc_form_bypass(), ...
	 *  @used by		WP_SpamShield->__construct()
	 *  @hook			action|init|-990
	 *  @since			1.9.7.8
	 */
	static public function early_post_intercept() {
		if( !empty( $_SERVER['REQUEST_METHOD'] ) && !WPSS_PHP::in_array( WPSS_REQUEST_METHOD, array( 'POST', 'GET', 'HEAD' ) ) ) { return; }
		if( rs_wpss_is_admin_sproc() || rs_wpss_is_doing_cron() ) { return; }
		if( ( is_admin() && rs_wpss_is_user_logged_in() && !rs_wpss_is_login_page() ) || rs_wpss_is_doing_ajax() ) { self::early_admin_intercept(); }
		if( rs_wpss_is_local_request() ) { return; }
		if( 'POST' !== WPSS_REQUEST_METHOD ) { self::early_get_intercept(); return; }
		global $HTTP_RAW_POST_DATA;
		$_SERVER['X_RAW_POST_DATA'] = ( isset( $_SERVER['X_RAW_POST_DATA'] ) ) ? $_SERVER['X_RAW_POST_DATA'] : self::get_raw_post_data();

		if( empty( $_POST ) &&  empty( $HTTP_RAW_POST_DATA ) ) { return; }
		if( empty( $_POST ) && !empty( $HTTP_RAW_POST_DATA ) ) { $_POST = array( 'HTTP_RAW_POST_DATA' => $HTTP_RAW_POST_DATA ); }

		$spamshield_options = parent::get_option();
		if( !empty( $spamshield_options['disable_misc_form_shield'] ) ) { return; }

		/* WPSS Whitelist Check - IP Only */
		if( rs_wpss_whitelist_check() ) { return; }

		$url		= WPSS_THIS_URL;
		$url_lc		= WPSS_Func::lower( $url );
		$req_uri	= $_SERVER['REQUEST_URI'];
		$req_uri_lc	= WPSS_Func::lower( $req_uri );
		$query_str	= rs_wpss_get_query_string( $url );

		/* BYPASS - GENERAL */
		if( isset( $_POST[WPSS_REF2XJS] ) || isset( $_POST[WPSS_JSONST] ) || isset( $_POST['wpss_contact_message'] ) || isset( $_POST['signup_username'] ) || isset( $_POST['signup_email'] ) || isset( $_POST['ws_plugin__s2member_registration'] ) || isset( $_POST['_wpcf7_version'] ) || isset( $_POST['gform_submit'] ) || isset( $_POST['gform_unique_id'] ) ) { return; }
		if( is_admin() && rs_wpss_is_user_logged_in() ) { return; }
		if( rs_wpss_is_login_page() ) { return; }
		if( rs_wpss_is_installing() ) { return; }
		$post_count = count( $_POST );
		$ecom_urls = unserialize( WPSS_ECOM_URLS );
		foreach( $ecom_urls as $k => $u ) { if( strpos( $req_uri, $u ) !== FALSE ) { return; } }
		$admin_url = WPSS_ADMIN_URL.'/';
		if( $post_count >= 5 && isset( $_POST['log'], $_POST['pwd'], $_POST['wp-submit'], $_POST['testcookie'], $_POST['redirect_to'] ) && $_POST['redirect_to'] === $admin_url ) { return; }
		if( $post_count >= 5 && isset( $_POST['log'], $_POST['pwd'], $_POST['login'], $_POST['testcookie'], $_POST['redirect_to'] ) ) { return; }
		if( $post_count >= 5 && isset( $_POST['username'], $_POST['password'], $_POST['login'], $_POST['_wpnonce'], $_POST['_wp_http_referer'] ) && rs_wpss_is_wc_login_page() ) { return; }

		if( WPSS_Compatibility::misc_form_bypass() ) { return; }

		/* BYPASS - HOOK */
		$mfsc_bypass = apply_filters( 'wpss_misc_form_spam_check_bypass', FALSE );
		if( !empty( $mfsc_bypass ) ) { return; }

		do_action( 'wpss_early_post_intercept' );

		$epc_filter_status		= $wpss_error_code = $log_pref = '';
		$epc_jsck_error			= $epc_badrobot_error = FALSE;
		$form_type				= 'misc form';
		$pref					= 'EPC-';
		$errors_3p				= array();
		$error_txt 				= rs_wpss_error_txt();
		$server_name			= WPSS_SERVER_NAME;
		$server_email_domain	= rs_wpss_get_email_domain( $server_name );
		$epc_serial_post 		= @WPSS_PHP::json_encode( $_POST );
		$form_auth_dat 			= array( 'comment_author' => '', 'comment_author_email' => '', 'comment_author_url' => '' );

		$blocked	= FALSE;
		$c 			= array(
			'name'		=> '',
			'value'		=> '1',
			'expire'	=> time() + YEAR_IN_SECONDS,
			'path'		=> '/',
			'domain'	=> rs_wpss_get_cookie_domain(),
			'secure'	=> FALSE,
			'httponly'	=> FALSE,
		);

		if( rs_wpss_is_xmlrpc() ) {
			rs_wpss_maybe_start_session();
			$c['name'] = 'P_XMLRPC';
			if( !empty( $HTTP_RAW_POST_DATA ) && is_string( $HTTP_RAW_POST_DATA ) && FALSE !== strpos( stripslashes( $HTTP_RAW_POST_DATA ), '<methodName>pingback.ping</methodName>' ) ) {
				if( !defined( 'WPSS_XMLRPC_PINGBACK' ) ) { define( 'WPSS_XMLRPC_PINGBACK', TRUE ); }
				/* TO DO - Replace with: // parent::define( array( 'XMLRPC_PINGBACK' => TRUE ), TRUE ); */
				return;
			}
		}

		if( rs_wpss_is_doing_nf_rest() ) {
			rs_wpss_maybe_start_session();
			$c['name'] = 'P_REST';
		}

		if( rs_wpss_is_doing_ajax() ) {
			if( ( empty( $_POST ) && empty( $query_str ) ) || !empty( $_SERVER['WPSS_SEC_THREAT'] ) ) {
				$wpss_error_code .= ' '.$pref.'FAR1020';
				$err_cod = 'fake_ajax_request_error';
				$err_msg = __( 'That action is currently not allowed.' );
				$errors_3p[$err_cod] = $err_msg;
			}
		}

		if( rs_wpss_is_xmlrpc() || rs_wpss_is_doing_nf_rest() ) {
			/* BAD ROBOT BLACKLIST */
			$bad_robot_filter_data = WPSS_Filters::bad_robot_blacklist_chk( $form_type, $epc_filter_status );
			$epc_filter_status = $bad_robot_filter_data['status'];
			$bad_robot_blacklisted = $bad_robot_filter_data['blacklisted'];
			if( !empty( $bad_robot_blacklisted ) ) {
				$wpss_error_code .= $bad_robot_filter_data['error_code'];
				$err_cod = 'badrobot_error';
				$err_msg = __( 'That action is currently not allowed.' );
				$errors_3p[$err_cod] = $err_msg;
				$_SERVER['WPSS_SEC_THREAT'] = TRUE;
			}
		} elseif( !rs_wpss_is_doing_rest() ) {
			if( WPSS_Filters::skiddie_ua_check() ) {
				$wpss_error_code .= ' '.$pref.'UA1004';
				$err_cod = 'badrobot_skiddie_error';
				$err_msg = __( 'That action is currently not allowed.' );
				$errors_3p[$err_cod] = $err_msg;
				$_SERVER['WPSS_SEC_THREAT'] = TRUE;
			}
		}

		if( rs_wpss_ubl_cache() ) {
			if( TRUE === WPSS_IP_BAN_ENABLE && rs_wpss_is_xmlrpc() ) { self::ip_ban(); }
			$wpss_error_code .= ' '.$pref.'0-BL';
			$err_cod = 'blacklisted_user_error';
			$err_msg = __( 'That action is currently not allowed.' );
			$errors_3p[$err_cod] = $err_msg;
		}

		if( !empty( $c['name'] ) ) { /* Setting cookie to honeypot bad actors */
			@setcookie( $c['name'], $c['value'], $c['expire'], $c['path'], $c['domain'], $c['secure'], $c['httponly'] );
		}

		/* Done with Tests */
		$wpss_error_code = trim( $wpss_error_code );

		if( !empty( $wpss_error_code ) ) {
			$wpss_error_code = str_replace( 'MSC-', 'EPC-', $wpss_error_code );
			if( rs_wpss_is_xmlrpc() ) {
				/* Disable Authenticated XMLRPC Functions */
				add_filter( 'xmlrpc_enabled',		'__return_false', 100 );
			}
			if( rs_wpss_is_doing_rest() ) {
				/* Disable REST WP-API version 1.x */
				add_filter( 'json_enabled',			'__return_false', 100 );
				add_filter( 'json_jsonp_enabled',	'__return_false', 100 );
				/* Disable REST WP-API version 2.x */
				if( !parent::is_wp_ver( '4.7' ) ) {
					add_filter( 'rest_enabled',		'__return_false', 100 );
				}
				add_filter( 'rest_jsonp_enabled',	'__return_false', 100 );
				add_filter( 'rest_authentication_errors', array( __CLASS__, 'disable_rest' ), 100 );
			}
			rs_wpss_update_accept_status( $form_auth_dat, 'r', 'Line: '.__LINE__, $wpss_error_code );
			if( !empty( $spamshield_options['comment_logging'] ) ) {
				rs_wpss_log_data( $form_auth_dat, $wpss_error_code, $form_type, $epc_serial_post );
			}
		} else {
			rs_wpss_update_accept_status( $form_auth_dat, 'a', 'Line: '.__LINE__ );
		}

		/**
		 *	Hook / Action
		 */
		do_action( 'wpss_early_post_intercept_end' );

		/* Now output error message */
		if( !empty( $wpss_error_code ) ) {
			$error_msg = '';
			foreach( $errors_3p as $c => $m ) {
				$error_msg .= '<strong>'.$error_txt.':</strong> '.$m.'<br /><br />'.WPSS_EOL;
			}
			parent::wp_die( $error_msg, TRUE );
		}

	}

	/**
	 *  SECURITY - Checks all incoming GET requests early for malicious behavior
	 *  Only if WPSS_IP_BAN_ENABLE === TRUE (Only enabled by default on Beta tester sites - has to be manually enabled for all others)
	 *  @dependencies	rs_wpss_is_admin_sproc(), rs_wpss_is_session_active(), rs_wpss_is_local_request(), rs_wpss_is_doing_ajax(), rs_wpss_is_ajax_request(), rs_wpss_is_doing_cron(), rs_wpss_is_xmlrpc(), rs_wpss_is_doing_nf_rest(), rs_wpss_is_installing(), rs_wpss_is_cli(), rs_wpss_invalid_browser_footprint(), WPSS_Security::ip_ban()
	 *  @used by		WPSS_Security::early_post_intercept()
	 *  @fires at		'init' / priority -990
	 *  @since			1.9.8.1
	 */
	static public function early_get_intercept() {
		if( TRUE !== WPSS_IP_BAN_ENABLE || rs_wpss_is_admin_sproc() || !rs_wpss_is_session_active() || rs_wpss_is_user_logged_in() ) { return FALSE; }
		if( rs_wpss_is_local_request() || rs_wpss_is_doing_ajax() || rs_wpss_is_ajax_request() || rs_wpss_is_doing_cron() || rs_wpss_is_xmlrpc() || rs_wpss_is_doing_nf_rest() || rs_wpss_is_installing() || rs_wpss_is_cli() ) { return FALSE; }
		$wpss_404_limit = ( rs_wpss_invalid_browser_footprint() ) ? 3 : 7;	/* Excessive number of 404s is a sign of probing */
		if( ( !empty( $_SESSION['wpss_404_hits_'.WPSS_HASH] ) && $_SESSION['wpss_404_hits_'.WPSS_HASH] >= $wpss_404_limit ) ) { self::ip_ban(); return TRUE; }
		return FALSE;
	}

	/**
	 *  SECURITY - Checks all incoming admin POST requests early for malicious behavior
	 *  @dependencies	rs_wpss_is_admin_sproc(), rs_wpss_is_doing_cron(), 
	 *  @used by		WPSS_Security::early_post_intercept()
	 *  @fires at		'init' / priority -990
	 *  @since			1.9.8.1
	 */
	static public function early_admin_intercept() {
		global $HTTP_RAW_POST_DATA, $pagenow;
		if( 'POST' !== WPSS_REQUEST_METHOD ) { return; }
		if( empty( $_POST ) && empty( $HTTP_RAW_POST_DATA ) ) { return; }
		if( 'widgets.php' === $pagenow || 'customize.php' === $pagenow ) { return; }
		if( rs_wpss_is_admin_sproc() || rs_wpss_is_doing_cron() || rs_wpss_is_installing() || rs_wpss_is_cli() || parent::is_customize_preview() ) { return; }
		if( empty( $_POST ) && !empty( $HTTP_RAW_POST_DATA ) ) { $_POST = array( 'HTTP_RAW_POST_DATA' => $HTTP_RAW_POST_DATA ); }
		if( !empty( $_POST ) && is_array( $_POST ) ) {
			foreach( $_POST as $k => $v ) {
				if( 0 === strpos( $k, 'PO_' ) && is_array( $v ) ) {
					foreach( $v as $ak => $av ) {
						if( 0 === strpos( $av, WPSS_PLUGIN_NAME ) ) { unset( $v[$ak] ); }
					}
					$_POST[$k] = array_values($v);
				}
			}
		}
		return FALSE;
	}

	/**
	 *  SECURITY - Checks all incoming requests for malicious/vulnerable request methods
	 *  @dependencies	rs_wpss_is_admin_sproc()
	 *  @used by		WP_SpamShield->__construct()
	 *  @hook			action|init|-1000
	 *  @since			1.9.8.2
	 */
	static public function check_request_method() {
		if( rs_wpss_is_admin_sproc() || rs_wpss_is_doing_cron() || rs_wpss_is_cli() ) { return FALSE; }

		/* BYPASS - HOOK */
		$rmc_bypass = apply_filters( 'wpss_request_method_check_bypass', FALSE );
		if( !empty( $rmc_bypass ) ) { return FALSE; }

		$wpss_error_code = ''; $pref = 'RMC-'; $errors_3p = array(); $error_txt = rs_wpss_error_txt();

		if( rs_wpss_is_local_request() ) { return FALSE; }

		if( !empty( $_SERVER['REQUEST_METHOD'] ) && !WPSS_PHP::in_array( WPSS_REQUEST_METHOD, array( 'GET', 'HEAD', 'OPTIONS', 'POST', 'PUT', 'PATCH', 'DELETE' ) ) ) {
			if( TRUE === WPSS_IP_BAN_ENABLE ) { self::ip_ban(); }
			$wpss_error_code .= ' '.$pref.'E405-MNA';
			$err_cod = 'method_not_allowed_error';
			$err_msg = __( 'Method Not Allowed' );
			$errors_3p[$err_cod] = $err_msg;
		}

		/* Done with Tests */
		$wpss_error_code = trim( $wpss_error_code );

		/* Now output error message */
		if( !empty( $wpss_error_code ) ) {
			$error_msg = '';
			foreach( $errors_3p as $c => $m ) {
				$error_msg .= '<strong>'.$error_txt.':</strong> '.$m.'<br /><br />'.WPSS_EOL;
			}
			$_SERVER['WPSS_SEC_THREAT'] = TRUE;
			parent::wp_die( $error_msg, TRUE, '405' );
		}
	}

	/**
	 *  Automatically keep plugin up to date: ensure latest anti-spam and security updates
	 *	Uses WP_Automatic_Updater class ( class-wp-automatic.php )
	 *  @dependencies	rs_wpss_is_admin_sproc()
	 *  @used by		...
	 *  @hook			...
	 *  @since			1.9.7.8
	 *  @modified		1.9.9.8.6	Added option to disable, Added advanced option
	 */
	static public function auto_update( $update, $item ) {
		if( TRUE === WPSS_AUTOUP_DISABLE || rs_wpss_is_admin_sproc() ) { return $update; }
		$auto_update_plugin = parent::get_option( 'auto_update_plugin' );
		if( empty( $auto_update_plugin ) ) { return $update; }
		/* Array of plugin slugs to always auto-update */
		$plugins = array( 'wp-spamshield', );
		if ( WPSS_PHP::in_array( $item->slug, $plugins ) ) {
			return true;	/* Always update plugins in this array */
		} else {
			return $update;	/* Else, use the normal API response to decide whether to update or not */
		}
	}

	/**
	 *  Disable REST in WordPres 4.7.0+
	 *  @dependencies	rs_wpss_error_txt()
	 *  @used by		...
	 *  @since			1.9.9.8.2
	 */
	static public function disable_rest( $errors = NULL ) {
		if( empty( $errors ) || !is_object( $errors ) ) { $errors = new WP_Error; }
		$err_txt = rs_wpss_error_txt();
		$err_cod = 'rest_disabled';
		$err_msg = __( 'REST API is currently disabled.', 'wp-spamshield' ); /* TO DO: Translate */
		$errors->add( $err_cod, '<strong>' . $err_txt . ':</strong> ' . $err_msg );
		return $errors;
	}

	/**
	 *  Add security checks to Password Reset Requests
	 *  @dependencies	...
	 *  @used by		...
	 *  @since			1.9.9.9.9
	 */
	static public function password_reset( $errors = NULL ) {
		if( empty( $errors ) || !is_object( $errors ) ) { $errors = new WP_Error; }

		$filter_status			= $wpss_error_code = $log_pref = '';
		$jsck_error				= $badrobot_error = FALSE;
		$form_type				= 'early post check';
		$pref					= 'PWR-';
		$serial_post 			= @WPSS_PHP::json_encode( $_POST );
		$form_auth_dat 			= array( 'comment_author' => '', 'comment_author_email' => '', 'comment_author_url' => '' );

		/* BAD ROBOT BLACKLIST */
		$bad_robot_filter_data = WPSS_Filters::bad_robot_blacklist_chk( $form_type, $filter_status );
		$filter_status = $bad_robot_filter_data['status'];
		$bad_robot_blacklisted = $bad_robot_filter_data['blacklisted'];
		if( !empty( $bad_robot_blacklisted ) ) {
			$wpss_error_code .= $bad_robot_filter_data['error_code'];
			$_SERVER['WPSS_SEC_THREAT'] = TRUE;
		}

		/* Done with Tests */
		$wpss_error_code = trim( $wpss_error_code );

		if( !empty( $wpss_error_code ) ) {
			$err_cod = 'invalidcombo';
			$err_msg = __( '<strong>ERROR</strong>: Invalid username or email.' );
			$errors->add( $err_cod, $err_msg );
			$wpss_error_code = str_replace( 'MSC-', 'PWR-', $wpss_error_code );
			rs_wpss_update_accept_status( $form_auth_dat, 'r', 'Line: '.__LINE__, $wpss_error_code );
			if( !empty( $spamshield_options['comment_logging'] ) ) {
				rs_wpss_log_data( $form_auth_dat, $wpss_error_code, $form_type, $serial_post );
			}
		} else {
			rs_wpss_update_accept_status( $form_auth_dat, 'a', 'Line: '.__LINE__ );
		}

		return $errors;
	}

	/**
	 *  Mail Init
	 *	Fires at wp_mail() init before anything is processed
	 *	Secondary mitigation for CVE-2017-8295: https://cve.mitre.org/cgi-bin/cvename.cgi?name=CVE-2017-8295
	 *  @dependencies	...
	 *  @used by		...
	 *  @since			1.9.9.9.9
	 */
	static public function mail_init( $atts = array() ) {
		if( empty( $_SERVER['SERVER_NAME'] ) || empty( $_SERVER['HTTP_HOST'] ) || WPSS_SITE_DOMAIN !== $_SERVER['SERVER_NAME'] || WPSS_SITE_DOMAIN !== $_SERVER['HTTP_HOST'] || $_SERVER['SERVER_NAME'] !== $_SERVER['HTTP_HOST'] ) {
			$temp_headers = ( !empty( $atts['headers'] ) ) ? WPSS_Func::lower( (string) ( ( is_array( $atts['headers'] ) ) ? implode( "\r\n", $atts['headers'] ) : $atts['headers'] ) ) : '';
			if( FALSE === strpos( $temp_headers, 'from:' ) ) {
				$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = WPSS_SITE_DOMAIN;
			}
		}
		return $atts;
	}

	/**
	 *	Prevent WordPress User-Agent from leaking site info on outgoing requests to other sites/servers.
	 *	The default WP UA includes the WP version and site URL.
	 *  @dependencies	...
	 *  @used by		...
	 *  @since			1.9.12
	 */
	static public function privacy_ua( $wpss_ua = FALSE, $extras = array() ) {
		$extras		= ( empty( $extras ) || !is_array( $extras ) ) ? array() : array_filter( $extras );
		if( TRUE === $wpss_ua ) { $extras[] = 'WP-SpamShield/'.WPSS_VERSION; }
		$addend		= ( !empty( $extras ) ) ? '; ' . implode( '; ', $extras ) : '';
		$privacy_ua	= 'WordPress/'.WPSS_WP_VERSION.' ( https://wordpress.org/'.$addend.' )';
		return $privacy_ua;
	}

}

