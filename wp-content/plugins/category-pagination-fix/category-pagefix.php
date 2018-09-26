<?php
/*
Plugin Name: Category pagination fix
Plugin URI: https://github.com/larsnystrom/category-pagination-fix
Description: Fixes 404 page error in pagination of category page while using custom permalink. Now added support for custom post types by using snippets from jdantzer plugin
Version: 3.2.3
Author: rahnas; version increased by Jonathan Stegall to prevent false updates and fix code standards
Author URI: http://www.htmlremix.com
Copyright 2009  Creative common  (email: mail@htmlremix.com)
This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
You are allowed to use, change and redistibute without any legal issues. I am not responsible for any damage caused by this program. Use at your own risk
Tested with WordPress 3. Works with wp-pagenavi
*/
/**
 * This plugin will fix the problem where next/previous of page number buttons are broken on list
 * of posts in a category when the custom permalink string is:
 * /%category%/%postname%/
 * The problem is that with a url like this:
 * /categoryname/page/2
 * the 'page' looks like a post name, not the keyword "page"
 */
if ( ! function_exists( 'remove_page_from_query_string' ) ) :
	add_filter( 'request', 'remove_page_from_query_string' );
	function remove_page_from_query_string( $query_string ) {
		if ( isset( $query_string['name'] ) && 'page' === $query_string['name'] && isset( $query_string['page'] ) ) {
			unset( $query_string['name'] );
			// 'page' in the query_string might look like '/2', so explode it out
			$page_part             = explode( '/', $query_string['page'] );
			$query_string['paged'] = end( $page_part );
		}
		return $query_string;
	}
endif;

// following are code adapted from Custom Post Type Category Pagination Fix by jdantzer
if ( ! function_exists( 'fix_category_pagination' ) ) :
	add_filter( 'request', 'fix_category_pagination' );
	function fix_category_pagination( $qs ) {
		if ( isset( $qs['category_name'] ) && isset( $qs['paged'] ) ) {
			$qs['post_type'] = get_post_types(array(
				'public'   => true,
				'_builtin' => false,
			));
			array_push( $qs['post_type'], 'post' );
		}
		return $qs;
	}
endif;
