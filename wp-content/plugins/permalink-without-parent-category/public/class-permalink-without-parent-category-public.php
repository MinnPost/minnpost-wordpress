<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      0.0.1
 *
 * @package    Permalink_Without_Parent_Category
 * @subpackage Permalink_Without_Parent_Category/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Permalink_Without_Parent_Category
 * @subpackage Permalink_Without_Parent_Category/public
 * @author     Jonathan Stegall <jonathan@jonathanstegall.com>
 */
class Permalink_Without_Parent_Category_Public {

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


	public function remove_parent_cats_from_link( $permalink, $post, $leavename ) {
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
	}

}
