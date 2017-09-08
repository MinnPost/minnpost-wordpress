<?php
/*
Plugin Name: Migrate Widgets
Plugin URI: https://wordpress.org/plugins/migrate-widgets
Description:
Version: 0.0.1
Author: Jonathan Stegall
Author URI: http://code.minnpost.com
License: GPL2+
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: migrate-widgets
*/

add_action( 'widgets_init', 'mp_sidebar_item_widgets' );
function mp_sidebar_item_widgets() {

	// We don't want to undo user changes, so we look for changes first.
	$active_widgets = get_option( 'sidebars_widgets' );
	$migrated_widgets = array();
	global $wpdb;
	$sidebar_item_widgets = $wpdb->get_results( 'SELECT `title`, `content`, `show_on`, `migrated` FROM wp_sidebars WHERE migrated != "1"' );

	$sidebars = array (
		'sidebar-2' => 'sidebar-2', // do the middle sidebar first
		'sidebar-1' => 'sidebar-1', // this is the right sidebar
		'wp_inactive_widgets' => 'wp_inactive_widgets', // we do want to be able to migrate some widgets and keep them inactive
	);

	foreach ( $sidebars as $key => $value ) {
	
		if ( ! empty ( $active_widgets[ $sidebars[ $key ] ] ) ) {
			$counter = count( $active_widgets[ $sidebars[ $key ] ] ) + 1;
		} else {
			$counter = 0;
		}

		$original_root = '<img src="https://www.minnpost.com/sites/default/files/images/thumbnails/';

		if ( 'sidebar-2' === $key ) {
			// image urls for middle sidebar
			$image_root = '<img src="https://www.minnpost.com/sites/default/files/imagecache/sidebar_middle/images/thumbnails/';
		} else if ( 'sidebar-1' === $key ) {
			// image urls for right sidebar
			$image_root = '<img src="https://www.minnpost.com/sites/default/files/imagecache/sidebar_right/images/thumbnails/';
		}

		foreach ( $sidebar_item_widgets as $widget ) {

			$widget_content = str_replace( $original_root, $image_root, $widget->content );

			$search_key = array_search( $widget->title, array_column( $migrated_widgets, 'title' ) );

			if ( false !== $search_key && ( 'wp_inactive_widgets' === $key || false === strpos( $widget->show_on, '%') ) ) {
				// it's not already in the array, unless the show_on field has a % in it for whatever follows after it
				continue;
			}

			// add this widget to this sidebar
			$active_widgets[ $sidebars[$key] ][$counter] = 'custom_html-' . $counter;

			// and write into it:
			$migrated_widgets[ $counter ] = array(
				'title' => $widget->title,
				'content' => $widget_content,
				'wc_cache' => 'yes',
			);
			$migrating = true;

			$data = mp_sidebar_set_conditions_data( $widget->show_on );

			if ( ( isset( $data['conditions']['rules_major'] ) && in_array( 'category', $data['conditions']['rules_major'] ) ) || ( isset( $data['conditions']['rules_major'] ) && in_array( 'taxonomy', $data['conditions']['rules_major'] ) ) ) {

				if ( false !== strpos( $widget->show_on, '/%') ) {
					if ( 'sidebar-1' !== $key ) { // the current loop is the middle sidebar
						// we don't want this widget in this sidebar
						unset( $active_widgets[ $sidebars[ $key ] ][ $counter ] );
						unset( $migrated_widgets[ $counter ] );
						$migrating = false;
					}
				} else if ( false !== strpos( $widget->show_on, '%') ) {
					if ( 'sidebar-1' !== $key && 'sidebar-2' !== $key ) {
						// we don't want this widget in this sidebar
						unset( $active_widgets[ $sidebars[ $key ] ][ $counter ] );
						unset( $migrated_widgets[ $counter ] );
						$migrating = false;
					}
				} else {
					if ( 'sidebar-2' !== $key ) { // the current loop is the right sidebar
						// we don't want this widget in this sidebar
						unset( $active_widgets[ $sidebars[ $key ] ][ $counter ] );
						unset( $migrated_widgets[ $counter ] );
						$migrating = false;
					}
				}
			} elseif ( '' == $widget->show_on ) {
				// if it is not shown anywhere, it should be inactive
				// this means we should put it in the inactive widgets sidebar
				if ( 'wp_inactive_widgets' !== $key ) {
					unset( $active_widgets[ $sidebars[ $key ] ][ $counter ] );
					unset( $migrated_widgets[ $counter ] );
					$migrating = false;
				}
			}

			$conditions = array();
			$conditions['action'] = $data['conditions']['action'];
			$conditions['match_all'] = ( isset( $data['conditions']['match_all'] ) ? '1' : '0' );
			$conditions['rules'] = array();

			if ( isset( $data['conditions']['rules_major'] ) ) {
				foreach ( $data['conditions']['rules_major'] as $index => $major_rule ) {
					$conditions['rules'][] = array(
						'major' => $major_rule,
						'minor' => isset( $data['conditions']['rules_minor'][$index] ) ? $data['conditions']['rules_minor'][$index] : '',
						'has_children' => isset( $data['conditions']['page_children'][$index] ) ? true : false,
					);
				}

				$migrated_widgets[ $counter ]['conditions'] = $conditions;
			}

			if ( true === $migrating ) {
				$counter++;
				$update = $wpdb->query( 'UPDATE wp_sidebars SET `migrated` = "1" WHERE `title` = "' . $widget->title . '"' );
			}

		}

	}

	$previous_widgets = get_option( 'widget_custom_html', '' );
	$previously_active_widgets = get_option( 'sidebars_widgets', '' );

	if ( $previous_widgets !== $migrated_widgets && ! empty( $migrated_widgets ) ) {
		// save the widget content only if it has changed from whatever it was before
		update_option( 'widget_custom_html', $migrated_widgets );
	}

	if ( $previously_active_widgets !== $active_widgets ) {
		// save the $active_widgets array, if it has changed from whatever it was before
		update_option( 'sidebars_widgets', $active_widgets );
	}

}


function mp_sidebar_set_conditions_data( $show_on ) {
	$show_on = explode( ',', $show_on );
	$data = array();
	$data['conditions']['action'] = 'show';

	if ( is_array( $show_on ) ) {
		if ( in_array( '<front>', $show_on ) ) {
			$data['conditions']['rules_major'][] = 'page';
			$data['conditions']['rules_minor'][] = 'front';
		}
		foreach ( $show_on as $show_item ) {
			if ( '<front>' !== $show_item ) {
				$data['conditions'] = mp_sidebar_rule_iterator( $data['conditions'], $show_item);
			}
		}
		unset( $data['conditions']['match_all'] );
	} else {
		if ( '<front>' === $show_on ) {
			$data['conditions']['rules_major'][] = 'page';
			$data['conditions']['rules_minor'][] = 'front';
		} else {
			$data['conditions'] = mp_sidebar_rule_iterator( $data['conditions'], $show_on);
		}
	}
	
	return $data;
}


function mp_sidebar_rule_iterator( $conditions, $show_on ) {
	$url = str_replace( '/%', '', $show_on );
	$url = str_replace( '%', '', $url );
	$url = str_replace( 'tag/', '', $url );
	$category = get_category_by_slug( $url );
	$tag = get_term_by( 'slug', $url, 'post_tag' );

	if ( false !== $category ) {
		$id = $category->term_id;
		$conditions['rules_major'][] = 'category';
	} elseif ( false !== $tag ) {
		$id = $tag->term_id;
		$conditions['rules_major'][] = 'taxonomy';
	}

	if ( false !== $category || false !== $tag ) {
		$conditions['rules_minor'][] = (string) $id;
		if ( false !== strpos( $show_on, '/%') ) {
			// we only want to show the widget inside the category or tag, not the category or tag itself
			// put these on the main right sidebar
			$conditions['match_all'] = '1';
			$conditions['rules_major'][] = 'page';
			$conditions['rules_minor'][] = 'post_type-post';
		} else if ( false !== strpos( $show_on, '%') ) {
			// we want to show the widget inside the category or tag, and also on its archive page
			// put these on the middle sidebar and the right sidebar
			$conditions['match_all'] = '1';
			$conditions['rules_major'][] = 'page';

			// for posts, put it on the right sidebar
			if ( 'sidebar-1' === $key ) {
				$conditions['rules_minor'][] = 'post_type-post';
			} else if ( 'sidebar-2' === $key ) {
				// for archive, put in the middle sidebar
				$conditions['rules_minor'][] = 'archive';
			}
		} else {
			// put these in the middle sidebar
			$conditions['match_all'] = '1';
			$conditions['rules_major'][] = 'page';
			$conditions['rules_minor'][] = 'archive';
		}
	}

	return $conditions;
}