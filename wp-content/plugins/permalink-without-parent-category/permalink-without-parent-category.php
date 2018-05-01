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
		if ( $post !== '' ) {
			$cats = get_the_category( $post->ID );
			if ( $cats ) {
				// Make sure we use the same start cat as the permalink generator
				//usort( $cats, '_usort_terms_by_ID' ); // order by ID - this is deprecated in 4.7
				$cats = wp_list_sort( $cats, 'ID' ); // new method in 4.7
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

/// end class
}
// Instantiate our class
$Permalink_Without_Parent_Category = new Permalink_Without_Parent_Category();