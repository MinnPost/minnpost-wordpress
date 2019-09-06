<?php
/**
 * Publish to Apple News: \Apple_Actions\Index\Export class
 *
 * @package Apple_News
 * @subpackage Apple_Actions\Index
 */

namespace Apple_Actions\Index;

require_once plugin_dir_path( __FILE__ ) . '../class-action.php';
require_once plugin_dir_path( __FILE__ ) . '../class-action-exception.php';
require_once plugin_dir_path( __FILE__ ) . '../../../includes/apple-exporter/autoload.php';

use Apple_Actions\Action as Action;
use Apple_Exporter\Exporter as Exporter;
use Apple_Exporter\Exporter_Content as Exporter_Content;
use Apple_Exporter\Exporter_Content_Settings as Exporter_Content_Settings;
use Apple_Exporter\Third_Party\Jetpack_Tiled_Gallery as Jetpack_Tiled_Gallery;
use \Admin_Apple_Sections;

/**
 * A class to handle an export request from the admin.
 *
 * @package Apple_News
 * @subpackage Apple_Actions\Index
 */
class Export extends Action {

	/**
	 * A variable to keep track of whether we are in the middle of an export.
	 *
	 * @var bool
	 * @access private
	 */
	private static $exporting = false;

	/**
	 * ID of the post being exported.
	 *
	 * @var int
	 * @access private
	 */
	private $id;

	/**
	 * Constructor.
	 *
	 * @param \Apple_Exporter\Settings $settings Settings in use during the current run.
	 * @param int|null                 $id       Optional. The ID of the content to export.
	 * @param array|null               $sections Optional. Sections to map for this post.
	 * @access public
	 */
	public function __construct( $settings, $id = null, $sections = null ) {
		parent::__construct( $settings );
		$this->set_theme( $sections );
		$this->id = $id;

		// Assign instance of an active Jetpack tiled gallery installation.
		$jetpack_tiled_gallery = Jetpack_Tiled_Gallery::instance();
	}

	/**
	 * A function to determine whether an export is currently in progress.
	 *
	 * @access public
	 * @return bool
	 */
	public static function is_exporting() {
		return self::$exporting;
	}

	/**
	 * Perform the export and return the results.
	 *
	 * @return string The JSON data
	 * @access public
	 */
	public function perform() {
		self::$exporting = true;
		$exporter        = $this->fetch_exporter();
		$json            = $exporter->export();
		self::$exporting = false;

		return $json;
	}

	/**
	 * Fetches an instance of Exporter.
	 *
	 * @return Exporter
	 * @access public
	 */
	public function fetch_exporter() {

		global $post;

		do_action( 'apple_news_do_fetch_exporter', $this->id );

		/**
		 * Fetch WP_Post object, and all required post information to fill up the
		 * Exporter_Content instance.
		 */
		$post = get_post( $this->id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.OverrideProhibited

		// Only include excerpt if exists.
		$excerpt = has_excerpt( $post ) ? wp_strip_all_tags( $post->post_excerpt ) : '';

		// Get the post thumbnail.
		$post_thumb = wp_get_attachment_url( get_post_thumbnail_id( $this->id ) ) ?: null;

		// Build the byline.
		$byline = $this->format_byline( $post );

		// Get the content.
		$content = $this->get_content( $post );

		// Filter each of our items before passing into the exporter class.
		$title      = apply_filters( 'apple_news_exporter_title', $post->post_title, $post->ID );
		$excerpt    = apply_filters( 'apple_news_exporter_excerpt', $excerpt, $post->ID );
		$post_thumb = apply_filters( 'apple_news_exporter_post_thumb', $post_thumb, $post->ID );
		$byline     = apply_filters( 'apple_news_exporter_byline', $byline, $post->ID );
		$content    = apply_filters( 'apple_news_exporter_content', $content, $post->ID );

		// Now pass all the variables into the Exporter_Content array.
		$base_content = new Exporter_Content(
			$post->ID,
			$title,
			$content,
			$excerpt,
			$post_thumb,
			$byline,
			$this->fetch_content_settings()
		);

		return new Exporter( $base_content, null, $this->settings );
	}

	/**
	 * Formats the byline
	 *
	 * @since 1.2.0
	 *
	 * @param \WP_Post $post   The post to use.
	 * @param string   $author Optional. Overrides author information. Defaults to author of the post.
	 * @param string   $date   Optional. Overrides the date. Defaults to the date of the post.
	 * @access public
	 * @return string
	 */
	public function format_byline( $post, $author = '', $date = '' ) {

		// Get information about the currently used theme.
		$theme = \Apple_Exporter\Theme::get_used();

		// Get the author.
		if ( empty( $author ) ) {

			// Try to get the author information from Co-Authors Plus.
			if ( function_exists( 'coauthors' ) ) {
				$author = coauthors( null, null, null, null, false );
			} else {
				$author = ucfirst( get_the_author_meta( 'display_name', $post->post_author ) );
			}
		}

		// Get the date.
		if ( empty( $date ) && ! empty( $post->post_date ) ) {
			$date = $post->post_date;
		}

		// Set the default date format.
		$date_format = 'M j, Y | g:i A';

		// Check for a custom byline format.
		$byline_format = $theme->get_value( 'byline_format' );
		if ( ! empty( $byline_format ) ) {
			/**
			 * Find and replace the author format placeholder name with a temporary placeholder.
			 * This is because some bylines could contain hashtags!
			 */
			$temp_byline_placeholder = 'AUTHOR' . time();
			$byline                  = str_replace( '#author#', $temp_byline_placeholder, $byline_format );

			// Attempt to parse the date format from the remaining string.
			$matches = array();
			preg_match( '/#(.*?)#/', $byline, $matches );
			if ( ! empty( $matches[1] ) && ! empty( $date ) ) {
				// Set the date using the custom format.
				$byline = str_replace( $matches[0], date( $matches[1], strtotime( $date ) ), $byline );
			}

			// Replace the temporary placeholder with the actual byline.
			$byline = str_replace( $temp_byline_placeholder, $author, $byline );

		} else {
			// Use the default format.
			$byline = sprintf(
				'by %1$s | %2$s',
				$author,
				date( $date_format, strtotime( $date ) )
			);
		}

		return $byline;
	}

	/**
	 * Gets the content
	 *
	 * @since 1.4.0
	 *
	 * @param \WP_Post $post The post object to extract content from.
	 * @access private
	 * @return string
	 */
	private function get_content( $post ) {
		/**
		 * The post_content is not raw HTML, as WordPress editor cleans up
		 * paragraphs and new lines, so we need to transform the content to
		 * HTML. We use 'the_content' filter for that.
		 */
		$content = apply_filters( 'apple_news_exporter_content_pre', $post->post_content, $post->ID );
		$content = apply_filters( 'the_content', $content );
		$content = $this->remove_tags( $content );
		$content = $this->remove_entities( $content );
		return $content;
	}

	/**
	 * Remove tags incompatible with Apple News format.
	 *
	 * @since 1.4.0
	 *
	 * @param string $html The HTML to be filtered.
	 * @access private
	 * @return string
	 */
	private function remove_tags( $html ) {
		$html = preg_replace( '/<style[^>]*>.*?<\/style>/i', '', $html );
		return $html;
	}

	/**
	 * Filter the content for markdown format.
	 *
	 * @param string $content The content to be filtered.
	 * @access private
	 * @return string
	 */
	private function remove_entities( $content ) {
		if ( 'yes' === $this->get_setting( 'html_support' ) ) {
			return $content;
		}

		// Correct ampersand output.
		return str_replace( '&amp;', '&', $content );
	}

	/**
	 * Loads settings for the Exporter_Content from the WordPress post metadata.
	 *
	 * @since 0.4.0
	 * @return Settings
	 * @access private
	 */
	private function fetch_content_settings() {
		$settings = new Exporter_Content_Settings();
		foreach ( get_post_meta( $this->id ) as $name => $value ) {
			if ( 0 === strpos( $name, 'apple_news_' ) ) {
				$name  = str_replace( 'apple_news_', '', $name );
				$value = $value[0];
				$settings->set( $name, $value );
			}
		}
		return apply_filters( 'apple_news_content_settings', $settings );
	}

	/**
	 * Sets the active theme for this session if explicitly set or mapped.
	 *
	 * @since 1.2.3
	 *
	 * @param array $sections Explicit sections mapped for this post.
	 *
	 * @access private
	 */
	private function set_theme( $sections ) {

		// This can only work if there is explicitly one section.
		if ( ! is_array( $sections ) || 1 !== count( $sections ) ) {
			return;
		}

		// Check if there is a custom theme mapping.
		$theme_name = Admin_Apple_Sections::get_theme_for_section( basename( $sections[0] ) );
		if ( empty( $theme_name ) ) {
			return;
		}

		// Try to get theme settings.
		$theme = new \Apple_Exporter\Theme();
		$theme->set_name( $theme_name );
		if ( ! $theme->load() ) {

			// Fall back to the active theme.
			$theme->set_name( \Apple_Exporter\Theme::get_active_theme_name() );
			$theme->load();
		}

		// Set theme as active for this session.
		$theme->use_this();
	}
}
