<?php
/**
 * Class: Save_To_Site_Button
 * A browser bookmark tool for Saved Links
 *
 * The Save To Site Button contains special javascript adapted from Press This!
 *
 * @since 0.3
 * @see https://wordpress.org/plugins/press-this-reloaded/
 */

/**
 * This file used to be loaded directly from the Save To Site Button bookmarklet.
 *
 * If WordPress is not loaded, then direct them to the right edit screen
 * and pass along the previous query vars.
 *
 * @since 0.3
 */
if ( ! defined( 'ABSPATH' ) ) {

	// Load WordPress
	define( 'WP_USE_THEMES', false );
	require_once '../../../wp-admin/admin.php';

	// Generate URL redirect
	$URL    = parse_url( $_SERVER['REQUEST_URI'] );
	$newURL = admin_url( 'post-new.php?' . $URL['query'] );

	// Header redirect
	header( 'Location: ' . $newURL );
}

class Save_To_Site_Button {

	private static $title;
	private static $description;
	private static $url;
	private static $source;
	private static $imgUrl;
	const plugin_domain = 'link-roundups';

	/**
	 * Initialize the class.
	 *
	 * @since 0.3
	 */
	public static function init() {
		if ( isset( $_GET['u'] ) ) {
			add_action( 'load-post-new.php', array( __CLASS__, 'load' ) );
			add_action( 'load-post.php', array( __CLASS__, 'load' ) );
		}
	}

	/**
	 * Returns the link for the bookmarklet button.
	 *
	 * If get_shortcut_link() returns an empty string (WP 4.9+) and
	 * if the https://wordpress.org/plugins/press-this/ plugin is installed,
	 * this plugin will use the plugin's bookmarklet code instead.
	 *
	 * @since 0.3
	 *
	 * @uses get_shortcut_link
	 * @uses https://github.com/WordPress/press-this/blob/690c99c8cd8feba4fe4fbfff89a955a325e35505/press-this-plugin.php#L118-L173
	 * @return String. Javascript bookmarklet code.
	 */
	public static function shortcut_link() {

		// This is the default 'Press This!' button link.
		$shortcut_link = '';

		if (
			version_compare( get_bloginfo( 'version' ), '4.9', '<' )
			&& function_exists( 'get_shortcut_link' )
		) {
			$shortcut_link = htmlspecialchars( get_shortcut_link() );
		}

		// since 4.9, get_shortcut_link has returned the empty string.
		if ( empty( $shortcut_link ) && function_exists( 'press_this_get_shortcut_link' ) ) {
			$shortcut_link = htmlspecialchars( press_this_get_shortcut_link() );
		}

		$post_type = 'rounduplink';

		// We alter it for our post type.
		$shortcut_link = str_replace( 'press-this.php', 'post-new.php', $shortcut_link );
		$shortcut_link = str_replace( 'width=720', 'width=840', $shortcut_link );
		$shortcut_link = str_replace( 'post-new.php', 'post-new.php?post_type=' . $post_type, $shortcut_link );
		$shortcut_link = str_replace( '?u=', '&u=', $shortcut_link );
		$shortcut_link = str_replace( '?v=', '&v=', $shortcut_link );

		return $shortcut_link;
	}

	/**
	 * Sets up default values for new Saved Link.
	 *
	 * These values are all static properties; I think that works because this instance of this class is only valid within this page load.
	 *
	 * @since 0.3
	 */
	public static function load() {
		$meta   = isset( $_POST['_meta'] ) ? $_POST['_meta'] : array();
		$links  = isset( $_POST['_links'] ) ? $_POST['_links'] : null;
		$images = isset( $_POST['_images'] ) ? $_POST['_images'] : null;
		$embeds = isset( $_POST['_embeds'] ) ? $_POST['_embeds'] : null;

		self::$url = isset( $_GET['u'] ) ? esc_url( $_GET['u'] ) : '';
		self::$url = wp_kses( urldecode( self::$url ), null );

		// Default title
		self::$title = '';
		if ( ! empty( $meta['og:title'] ) ) {
			self::$title = $meta['og:title'];
		} else {
			self::$title = isset( $_POST['t'] ) ? trim( strip_tags( html_entity_decode( stripslashes( $_POST['t'] ), ENT_QUOTES ) ) ) : '';
		}

		$selection = '';
		if ( ! empty( $_POST['s'] ) ) {
			$selection = str_replace( '&apos;', "'", stripslashes( $_POST['s'] ) );
			$selection = '<blockquote>' . trim( htmlspecialchars( html_entity_decode( $selection, ENT_QUOTES ) ) ) . '</blockquote>';
		}

		// Default description
		self::$description = '';
		if ( ! empty( $selection ) ) {
			self::$description = $selection;
		} elseif ( ! empty( $meta['og:description'] ) ) {
			self::$description = $meta['og:description'];
		}

		// Default source
		self::$source = '';
		if ( ! empty( $meta['og:site_name'] ) ) {
			self::$source = $meta['og:site_name'];
		} elseif ( self::$url ) {
			$url          = parse_url( self::$url );
			self::$source = $url['host'];
		}

		self::$imgUrl = '';
		if ( ! empty( $meta['og:image'] ) ) {
			self::$imgUrl = $meta['og:image'];
		}

		/**
		 * Default Link Roundups Values for Custom Meta
		 *
		 * Register default title, link URL, link description and link source.
		 *
		 * @since x.x.x
		 *
		 * @param type  $var Description.
		 * @param array $args {
		 *     Short description about this hash.
		 *
		 *     @type type $var Description.
		 *     @type type $var Description.
		 * }
		 * @param type  $var Description.
		 */
		add_filter( 'default_title', array( __CLASS__, 'default_title' ) );
		add_filter( 'default_link_url', array( __CLASS__, 'default_link' ) );
		add_filter( 'default_link_description', array( __CLASS__, 'default_description' ) );
		add_filter( 'default_link_source', array( __CLASS__, 'default_source' ) );
		// attempt to hide the Toolbar in the backend, probably fails in newer WordPress versions.
		add_filter( 'show_admin_bar', '__return_false' );
	}

	/**
	 * Returns the default title value for this link.
	 *
	 * @since 0.3
	 *
	 * @return String. Default title.
	 */
	public static function default_title( $title = null ) {
		return self::$title;
	}

	/**
	 * Returns the default description value for this link.
	 *
	 * @since 0.3
	 *
	 * @return String. Default description.
	 */
	public static function default_description( $description = null ) {
		return self::$description;
	}

	/**
	 * Returns the default link value for this link.
	 *
	 * @since 0.3
	 *
	 * @return String. Default link.
	 */
	public static function default_link( $link = null ) {
		return self::$url;
	}

	/**
	 * Returns the default source value for this link.
	 *
	 * @since 0.3
	 *
	 * @return String. Default source.
	 */
	public static function default_source( $source = null ) {
		return self::$source;
	}

	/**
	 * Returns the default image src value for this link.
	 *
	 * @since 0.3
	 *
	 * @return String. Default image src.
	 */
	public static function default_imgUrl( $imgUrl = null ) {
		return self::$imgUrl;
	}

}

Save_To_Site_Button::init();
