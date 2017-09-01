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

	$sidebars = array (
		'sidebar-test' => 'sidebar-test',
	);

	// We don't want to undo user changes, so we look for changes first.
	$active_widgets = get_option( 'sidebars_widgets' );
	if ( ! empty ( $active_widgets[ $sidebars['sidebar-test'] ] ) ) {
		// Okay, no fun anymore. There is already some content.
		return;
	}

	global $wpdb;
	$sidebar_item_widgets = $wpdb->get_results( 'SELECT `title`, `content`, `show_on`, `migrated` FROM wp_sidebars WHERE migrated != "1"' );


	// The sidebars are empty, let's put something into them.
	$counter = 0;
	foreach ( $sidebar_item_widgets as $key => $widget ) {

		// add this widget to this sidebar
		$active_widgets[ $sidebars['sidebar-test'] ][$counter] = 'custom_html-' . $counter;

		// … and write some text into it:
		$migrated_widgets[ $counter ] = array(
			'title' => $widget->title,
			'content' => $widget->content,
			'wc_cache' => 'yes',
		);


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
				} else {
					// put these in the middle sidebar
					$data['conditions']['match_all'] = '1';
					$data['conditions']['rules_major'][] = 'page';
					$data['conditions']['rules_minor'][] = 'archive';
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

		$counter++;

		$update = $wpdb->query( 'UPDATE wp_sidebars SET `migrated` = "1" WHERE `title` = "' . $widget->title . '"' );

	}

	update_option( 'widget_custom_html', $migrated_widgets );
	// Now save the $active_widgets array.
	update_option( 'sidebars_widgets', $active_widgets );

}
