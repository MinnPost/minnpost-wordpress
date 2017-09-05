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
		'sidebar-1' => 'sidebar-1',
		'sidebar-2' => 'sidebar-2',
	);

	foreach ( $sidebars as $key => $value ) {
	
		if ( ! empty ( $active_widgets[ $sidebars[ $key ] ] ) ) {
			//return;
			$counter = count( $active_widgets[ $sidebars[ $key ] ] ) + 1;
		} else {
			$counter = 0;
		}

		foreach ( $sidebar_item_widgets as $widget ) {

			// add this widget to this sidebar
			$active_widgets[ $sidebars[$key] ][$counter] = 'custom_html-' . $counter;

			// and write into it:
			$migrated_widgets[ $counter ] = array(
				'title' => $widget->title,
				'content' => $widget->content,
				'wc_cache' => 'yes',
			);
			$migrating = true;

			$data = array();
			$data['conditions']['action'] = 'show';
			if ( '<front>' === $widget->show_on ) {
				$data['conditions']['rules_major'][] = 'page';
				$data['conditions']['rules_minor'][] = 'front';
			} else {
				$url = str_replace( '/%', '', $widget->show_on );
				$url = str_replace( '%', '', $url );
				$url = str_replace( 'tag/', '', $url );
				$category = get_category_by_slug( $url );
				$tag = get_term_by( 'slug', $url, 'post_tag' );
				if ( false !== $category ) {
					$id = $category->term_id;
					$data['conditions']['rules_major'][] = 'category';
				} elseif ( false !== $tag ) {
					$id = $tag->term_id;
					$data['conditions']['rules_major'][] = 'taxonomy';
				}

				if ( false !== $category || false !== $tag ) {
					$data['conditions']['rules_minor'][] = (string) $id;
					if ( false !== strpos( $widget->show_on, '%') ) {
						// we only want to show the widget inside the category or tag, not the category or tag itself
						// put these on the main right sidebar
						$data['conditions']['match_all'] = '1';
						$data['conditions']['rules_major'][] = 'page';
						$data['conditions']['rules_minor'][] = 'post_type-post';

						if ( 'sidebar-2' === $key ) { // the current loop is the middle sidebar
							// add this widget to this sidebar
							unset( $active_widgets[ $sidebars[$key] ][$counter] );
							// and write into it:
							unset( $migrated_widgets[ $counter ] );
							$migrating = false;
						}

					} else {
						// put these in the middle sidebar
						$data['conditions']['match_all'] = '1';
						$data['conditions']['rules_major'][] = 'page';
						$data['conditions']['rules_minor'][] = 'archive';

						if ( 'sidebar-1' === $key ) { // the current loop is the middle sidebar
							// add this widget to this sidebar
							unset( $active_widgets[ $sidebars[$key] ][$counter] );
							// and write into it:
							unset( $migrated_widgets[ $counter ] );
							$migrating = false;
						}

					}
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

	update_option( 'widget_custom_html', $migrated_widgets );
	// Now save the $active_widgets array.
	update_option( 'sidebars_widgets', $active_widgets );

}
