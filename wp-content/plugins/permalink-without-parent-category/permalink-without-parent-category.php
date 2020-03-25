<?php
/*
Plugin Name: Permalink Without Parent Category
Description: This allows posts that use %category% in the permalink pattern to only show the child category, not the parent.
Version: 0.0.1
Author: Jonathan Stegall
Author URI: https://code.minnpost.com
License: GPL2+
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: permalink-without-parent-category
*/

// Start up the plugin
class Permalink_Without_Parent_Category {

	/**
	* @var string
	*/
	private $version;

	/**
	 * This is our constructor
	 *
	 * @return void
	 */
	public function __construct() {

		$this->version = '0.0.1';
		$this->post_filter();

	}

	public function post_filter() {
		add_filter( 'post_link', array( $this, 'remove_parent_cats_from_link' ), 10, 3 );
	}

	public function remove_parent_cats_from_link( $permalink, $post = '', $leavename = '' ) {
		if ( '' !== $post && is_main_query() ) {
			$category_id = minnpost_get_permalink_category_id( $post->ID );
			if ( '' === $category_id ) {
				$cats = get_the_category( $post->ID );
				if ( ! empty( $cats ) ) {
					$cats        = wp_list_sort( $cats, 'term_id' );
					$category_id = $cats[0]->term_id;
				}
			}
			if ( 0 !== $category_id ) {
				$category = get_category( $category_id );
				if ( 0 !== $category->parent ) {
					$parent  = $category->parent;
					$parents = get_category_parents( $parent, false, '/', true ); // string
				}
			}
			if ( isset( $parents ) ) {
				$permalink = str_replace( $parents, '', $permalink );
				error_log( 'run' );
			}
			return $permalink;
		}
	}
}
// Instantiate our class
$permalink_without_parent_category = new Permalink_Without_Parent_Category();
