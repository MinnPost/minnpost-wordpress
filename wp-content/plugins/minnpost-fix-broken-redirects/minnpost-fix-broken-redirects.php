<?php
/*
Plugin Name: MinnPost Fix Broken Redirects
Description: Create redirects for 404s that have special characters
Version: 0.0.3
Author: Jonathan Stegall
Author URI: https://code.minnpost.com
Text Domain: minnpost-roles-and-capabilities
License: GPL2+
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

class Minnpost_Fix_Broken_Redirects {

	/**
	 * @var string
	 * The plugin version
	*/
	private $version;

	/**
	 * @var object
	 * Static property to hold an instance of the class; this seems to make it reusable
	 *
	 */
	static $instance = null;

	/**
	 * Load the static $instance property that holds the instance of the class.
	 * This instance makes the class reusable by other plugins
	*
	 * @return object
	*
	*/
	static public function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new Minnpost_Fix_Broken_Redirects();
		}
		return self::$instance;
	}

	/**
	 * This is our constructor
	 *
	 * @return void
	 */
	public function __construct() {

		$this->version = '0.0.1';
		$this->slug    = 'minnpost-fix-broken-redirects';

		$this->add_actions();

	}

	private function add_actions() {
		add_action( 'template_redirect', array( $this, 'special_character_url_redirect' ) );
		add_action( 'admin_init', array( $this, 'admin_special_character_url_redirect' ) );
	}

	/**
	 * Store broken pre-Drupal urls with special characters, which 404, to be redirected
	 * @return array $vars
	 */
	public function special_character_url_redirect() {

		if ( ! is_user_logged_in() ) {
			return;
		}

		if ( ! user_can( get_current_user_id(), 'manage_options' ) ) {
			return;
		}

		// check if is a 404 error
		if ( is_404() ) {
			$option    = 'minnpost_urls_to_redirect';
			$full      = $_SERVER['REQUEST_URI'];
			$requested = basename( parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ) );

			if ( false !== strpos( $requested, '.' ) ) {
				return;
			}

			$fixed = $this->remove_non_ascii_characters( $requested );
			if ( $requested !== $fixed ) {
				$replaced = str_replace( $requested, $fixed, $full );
				//wp_safe_redirect( site_url( $replaced ) );
				//exit();
				if ( class_exists( 'WPCOM_Legacy_Redirector' ) ) {
					$urls     = get_option( $option, array() );
					$this_url = array(
						'from'       => $full,
						'to'         => site_url( $replaced ),
						'redirected' => false,
						'updated'    => false,
					);
					$urls[]   = $this_url;
					update_option( $option, $urls, false );
				}
			}
		}
	}

	/**
	 * Try to make a usable URL
	 * @param string $requested
	 * @return string $fixed
	 */
	private function remove_non_ascii_characters( $requested ) {
		if ( preg_match( '/a-zA-Z0-9-_./i', urldecode( $requested ) ) ) {
			return;
		};
		$url_decoded = urldecode( $requested );
		$fixed       = remove_accents( $url_decoded );
		$fixed       = preg_replace( '/[^a-zA-Z0-9-_.]/', '', $fixed );
		$fixed       = sanitize_title_with_dashes( $fixed );
		return $fixed;
	}

	/**
	 * When admin loads, parse any special character redirects and try to create them
	 *
	 */
	public function admin_special_character_url_redirect() {

		if ( ! is_user_logged_in() ) {
			return;
		}

		if ( ! user_can( get_current_user_id(), 'manage_options' ) ) {
			return;
		}

		if ( class_exists( 'WPCOM_Legacy_Redirector' ) ) {
			$option = 'minnpost_urls_to_redirect';
			$urls   = get_option( $option, array() );
			if ( ! empty( $urls ) ) {
				foreach ( $urls as $key => $url ) {
					if ( ! isset( $url['updated'] ) || '' === $url['updated'] || ! isset( $url['redirected'] ) || '' === $url['redirected'] ) {
						//error_log( 'not set value' );
						unset( $urls[ $key ] );
						continue;
					}
					$redirect_inserted = false;
					$post_updated      = 0;
					$post_name         = urldecode( basename( parse_url( $url['from'], PHP_URL_PATH ) ) );
					//error_log( 'check for postname ' . $post_name );
					if ( true !== $url['updated'] ) {
						//error_log( 'it has not been updated' );
						global $wpdb;
						$posts = $wpdb->get_results( 'SELECT ID FROM ' . $wpdb->prefix . 'posts' . ' WHERE post_name = "' . sanitize_title( $post_name ) . '" OR post_name = "' . $post_name . '"', ARRAY_A );
						if ( empty( $posts ) ) {
							//error_log( 'no posts match' );
							continue;
						}
						//error_log( 'posts match' );
						$post    = $posts[0];
						$post_id = $post['ID'];
						$post_updated = wp_update_post(
							array(
								'ID'        => $post_id,
								'post_name' => basename( parse_url( $url['to'], PHP_URL_PATH ) ),
							)
						);
						if ( 0 !== $post_updated ) {
							$url['updated'] = true;
						}
					}
					if ( true !== $url['redirected'] ) {
						$fixed       = basename( parse_url( $url['to'], PHP_URL_PATH ) );
						$redirect_to = str_replace( $post_name, $fixed, $url['to'] );
						//error_log( 'redirected to is ' . $redirect_to );
						$redirect_inserted = WPCOM_Legacy_Redirector::insert_legacy_redirect( $url['from'], $redirect_to );
						if ( true === filter_var( $redirect_inserted, FILTER_VALIDATE_BOOLEAN ) ) {
							$url['redirected'] = true;
						}
					}
					$urls[ $key ] = $url;
					if ( true === filter_var( $redirect_inserted, FILTER_VALIDATE_BOOLEAN ) && 0 !== $post_updated ) {
						unset( $urls[ $key ] );
					}
				}
				//error_log( 'update the value to remove the one we just did' );
				update_option( $option, $urls, false );
			} else {
				return;
			}
		}
	}

}

// start doing stuff. for the view-admin-as plugin, at least, we have to use muplugins_loaded
add_action( 'plugins_loaded', array( 'Minnpost_Fix_Broken_Redirects', 'get_instance' ) );
