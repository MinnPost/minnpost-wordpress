<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      0.0.1
 *
 * @package    Coauthors_Extend
 * @subpackage Coauthors_Extend/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Coauthors_Extend
 * @subpackage Coauthors_Extend/public
 * @author     Jonathan Stegall <jonathan@jonathanstegall.com>
 */
class Coauthors_Extend_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    0.0.1
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}


	/*public function twentysixteen_entry_meta() {
		if ( 'post' === get_post_type() ) {

			if ( function_exists( 'coauthors_posts_links' ) ) {
		        $author = coauthors_posts_links( null, null, null, null, false );
		    } else {
		        $author = '<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '" title="' . esc_attr( get_the_author_meta( 'display_name' ) ) . '">' . get_the_author_meta( 'display_name' ) . '</a></span>';
		    }

			$author_avatar_size = apply_filters( 'twentysixteen_author_avatar_size', 49 );
			printf( '<span class="byline">' . $author . '</span>',
				get_avatar( get_the_author_meta( 'user_email' ), $author_avatar_size ),
				_x( 'Author', 'Used before post author name.', 'twentysixteen' ),
				esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
				get_the_author()
			);
		}    

		if ( in_array( get_post_type(), array( 'post', 'attachment' ) ) ) {
			twentysixteen_entry_date();
		}

		$format = get_post_format();
		if ( current_theme_supports( 'post-formats', $format ) ) {
			printf( '<span class="entry-format">%1$s<a href="%2$s">%3$s</a></span>',
				sprintf( '<span class="screen-reader-text">%s </span>', _x( 'Format', 'Used before post format.', 'twentysixteen' ) ),
				esc_url( get_post_format_link( $format ) ),
				get_post_format_string( $format )
			);
		}

		if ( 'post' === get_post_type() ) {
			twentysixteen_entry_taxonomies();
		}

		if ( ! is_singular() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
			echo '<span class="comments-link">';
			comments_popup_link( sprintf( __( 'Leave a comment<span class="screen-reader-text"> on %s</span>', 'twentysixteen' ), get_the_title() ) );
			echo '</span>';
		}
	}*/

	/*public function remove_parent_cats_from_link( $permalink, $post, $leavename ) {
		$cats = get_the_category( $post->ID );
		if ( $cats ) {
			// Make sure we use the same start cat as the permalink generator
			usort( $cats, '_usort_terms_by_ID' ); // order by ID
			$category = $cats[0]->slug;
			if ( $parent = $cats[0]->parent ) {
				// If there are parent categories, collect them and replace them in the link
				$parentcats = get_category_parents( $parent, false, '/', true );
				// str_replace() is not the best solution if you can have duplicates:
				// myexamplesite.com/luxemburg/luxemburg/ will be stripped down to myexamplesite.com/
				// But if you don't expect that, it should work
				$permalink = str_replace( $parentcats, '', $permalink );
			}
		}
		return $permalink;
	}*/

}
