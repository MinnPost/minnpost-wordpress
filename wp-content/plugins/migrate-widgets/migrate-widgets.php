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

	$sidebars = array(
		'sidebar-2' => 'sidebar-2', // do the middle sidebar first
		'sidebar-1' => 'sidebar-1', // this is the right sidebar
		'wp_inactive_widgets' => 'wp_inactive_widgets', // we do want to be able to migrate some widgets and keep them inactive
	);

	foreach ( $sidebars as $key => $value ) {
		if ( ! empty( $active_widgets[ $sidebars[ $key ] ] ) ) {
			$counter = count( $active_widgets[ $sidebars[ $key ] ] ) + 1;
		} else {
			$counter = 0;
		}

		$original_root = '<img src="https://www.minnpost.com/sites/default/files/images/thumbnails/';

		if ( 'sidebar-2' === $key ) {
			// image urls for middle sidebar
			$image_root = '<img src="https://www.minnpost.com/sites/default/files/imagecache/sidebar_middle/images/thumbnails/';
		} elseif ( 'sidebar-1' === $key ) {
			// image urls for right sidebar
			$image_root = '<img src="https://www.minnpost.com/sites/default/files/imagecache/sidebar_right/images/thumbnails/';
		}

		foreach ( $sidebar_item_widgets as $widget ) {
			$widget_content = str_replace( $original_root, $image_root, $widget->content );

			$search_key = array_search( $widget->title, array_column( $migrated_widgets, 'title' ) );

			if ( false !== $search_key ) { // this widget is already found in the migrated_widgets array of titles
				if ( 'wp_inactive_widgets' === $key ) {
					continue;
				}
				if ( false === strpos( $widget->show_on, '%' ) && false !== strpos( $widget->show_on, '/' ) ) {
					// there is not a % without a / so it doesn't get used in multiple cases
					continue;
				}
			}

			// add this widget to this sidebar
			$active_widgets[ $sidebars[ $key ] ][ $counter ] = 'custom_html-' . $counter;

			// and write into it:
			$migrated_widgets[ $counter ] = array(
				'title' => $widget->title,
				'content' => $widget_content,
				'wc_cache' => 'yes',
			);

			$data = mp_sidebar_set_conditions_data( $widget->show_on, $key, $counter );

			if ( '' !== $widget->show_on && isset( $data['show_on'] ) && isset( $data['class']['logic'] ) ) {
				if ( $data['show_on'] === $key && 'wp_inactive_widgets' !== $key ) {
					// the key matches the show_on value and this is not the inactive sidebar
					$migrating = true;
				} else {
					// if it is not shown anywhere, it should be inactive
					// this means we should put it in the inactive widgets sidebar
					unset( $active_widgets[ $sidebars[ $key ] ][ $counter ] );
					unset( $migrated_widgets[ $counter ] );
					$migrating = false;
				}
			} else {
				// if it is not shown anywhere, it should be inactive
				// this means we should put it in the inactive widgets sidebar
				if ( 'wp_inactive_widgets' !== $key ) {
					unset( $active_widgets[ $sidebars[ $key ] ][ $counter ] );
					unset( $migrated_widgets[ $counter ] );
					$migrating = false;
				} else {
					$migrating = true;
				}
			}

			unset( $data['show_on'] ); // we don't need to save this value

			if ( ! empty( $data ) ) {
				$migrated_widgets[ $counter ][ 'extended_widget_opts-custom_html-' . $counter ] = $data;
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


function mp_sidebar_set_conditions_data( $show_on, $key, $counter ) {
	$show_on = explode( ',', $show_on );
	$data = array(
		'id_base' => 'custom_html-' . $counter,
		'visibility' => array(
			'options' => 'hide',
			'selected' => '0',
		),
		'devices' => array(
			'options' => 'hide',
		),
		'alignment' => array(
			'desktop' => 'default',
		),
		'class' => array(
			'selected' => '2',
			'id' => '',
			'classes' => '',
		),
		'tabselect' => '3',
	);

	if ( is_array( $show_on ) ) {
		$data['class']['logic'] = array();
		if ( in_array( '<front>', $show_on ) ) {
			$data['class']['logic'][] = 'is_home()';
			$data['show_on'] = 'sidebar-2';
		}
		foreach ( $show_on as $show_item ) {
			if ( '<front>' !== $show_item ) {
				$iterator = mp_sidebar_rule_iterator( $show_item, $key );
				if ( '' !== $iterator['logic'] && '' !== $iterator['show_on'] ) {
					$data['class']['logic'][] = $iterator['logic'];
					$data['show_on'] = $iterator['show_on'];
				}
			}
		}
	} else {
		$data['class']['logic'] = '';
		if ( '<front>' === $show_on ) {
			$data['class']['logic'] = 'is_home()';
			$data['show_on'] = 'sidebar-2';
		} else {
			$iterator = mp_sidebar_rule_iterator( $show_item, $key );
			if ( '' !== $iterator['logic'] && '' !== $iterator['show_on'] ) {
				$data['class']['logic'] = $iterator['logic'];
				$data['show_on'] = $iterator['show_on'];
			}
		}
	}

	if ( is_array( $data['class']['logic'] ) ) {
		$data['class']['logic'] = implode( ' || ', $data['class']['logic'] );
	}

	return $data;
}


function mp_sidebar_rule_iterator( $show_on, $key ) {
	$url = str_replace( '/%', '', $show_on );
	$url = str_replace( '%', '', $url );
	$url = str_replace( 'tag/', '', $url );
	$category = get_category_by_slug( $url );
	$tag = get_term_by( 'slug', $url, 'post_tag' );
	$page = get_page_by_path( $url );

	if ( false !== $category ) {
		$id = $category->term_id;
	} elseif ( false !== $tag ) {
		$id = $tag->term_id;
	} elseif ( null !== $page ) {
		$id = $page->ID;
	} else {
		return;
	}

	$data = array();
	$data['logic'] = '';
	$data['show_on'] = '';

	if ( false !== $category || false !== $tag ) {
		if ( false !== strpos( $show_on, '/%' ) ) {
			// this only shows on the right side
			// it is a category/tag, but only the content inside
			// not the archive
			if ( 'sidebar-1' === $key ) {
				if ( false !== $category ) {
					$data['logic'] = '( is_singular() && in_category(' . $id . ') )';
				} else {
					$data['logic'] = '( is_singular() && has_tag(' . $id . ') )';
				}
				$data['show_on'] = $key;
			}
		} elseif ( false !== strpos( $show_on, '%' ) ) {
			// we want to show the widget inside the category or tag, and also on its archive page
			// put these on the middle sidebar and the right sidebar
			// something is getting added here every time though i think
			// for posts, put it on the right sidebar
			if ( 'sidebar-1' === $key ) {
				if ( false !== $category ) {
					$data['logic'] = '( is_singular() && in_category(' . $id . ') )';
				} else {
					$data['logic'] = '( is_singular() && has_tag(' . $id . ') )';
				}
			} elseif ( 'sidebar-2' === $key ) {
				if ( false !== $category ) {
					$data['logic'] = 'is_category(' . $id . ')';
				} else {
					$data['logic'] = 'is_tag(' . $id . ')';
				}
			}
			$data['show_on'] = $key; // it shows everywhere
		} else {
			// put these in the middle sidebar
			// it is a category/tag, but not the contents
			if ( 'sidebar-2' === $key ) {
				if ( false !== $category ) {
					$data['logic'] = 'is_category(' . $id . ')';
				} else {
					$data['logic'] = 'is_tag(' . $id . ')';
				}
				$data['show_on'] = $key;
			}
		}
	} elseif ( false !== $page ) {
		if ( 'sidebar-1' === $key ) {
			$data['logic'] = 'is_page(' . $id . ')';
			$data['show_on'] = $key;
		}
	}

	return $data;
}
