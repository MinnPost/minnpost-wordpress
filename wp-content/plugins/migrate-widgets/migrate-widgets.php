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

function initialize_sidebars() {

	$sidebars = array();
	// Supply the sidebars you want to initialize in a filter
	$sidebars = apply_filters( 'alter_initialization_sidebars', $sidebars );

	$active_widgets = get_option('sidebars_widgets');

	$args = array(
		'sidebars' => $sidebars,
		'active_widgets' => $active_widgets,
		'update_widget_content' => array(),
	);

	foreach ( $sidebars as $current_sidebar_short_name => $current_sidebar_id ) {
		$args['current_sidebar_short_name'] = $current_sidebar_short_name;
		// we are passing our arguments as a reference, so we can modify their contents
		do_action( 'migrate_widgets_sidebar_init', array( &$args ) );
	}

	// we only need to update sidebars, if the sidebars are not initialized yet
	// and we also have data to initialize the sidebars with
	if ( ! empty( $args['update_widget_content'] ) ) {
		foreach ( $args['update_widget_content'] as $widget => $widget_occurence ) {
			// the update_widget_content array stores all widget instances of each widget
			update_option( 'widget_' . $widget, $args['update_widget_content'][ $widget ] );
		}
		// after we have updated all the widgets, we update the active_widgets array
		update_option( 'sidebars_widgets', $args['active_widgets'] );
	}
}

function check_sidebar_content( $active_widgets, $sidebars, $sidebar_name ) {
	$sidebar_contents = $active_widgets[ $sidebars[ $sidebar_name ] ];
	if ( ! empty( $sidebar_contents ) ) {
		return $sidebar_contents;
	}
	return false;
}

add_action( 'migrate_widgets_sidebar_init', 'add_widgets_to_sidebar' );
function add_widgets_to_sidebar( $args ) {
	extract( $args[0] );
	// We check if the current sidebar already has content and if it does we exit
	$sidebar_element = check_sidebar_content( $active_widgets, $sidebars, $current_sidebar_short_name );
	if ( $sidebar_element !== false  ) {
		return;
	}
	do_action( 'migrate_widgets_widget_init', array( &$args ) );
}

add_action( 'migrate_widgets_widget_init', 'migrate_widgets_initialize_widgets' );
function migrate_widgets_initialize_widgets( $args ) {
	extract( $args[0][0] );
	$widgets = array();
	// Here the widgets previously defined in filter functions are initialized,
	// but only those corresponding to the current sidebar 

	global $wpdb;

	$test_widgets = $wpdb->get_results( 'SELECT `title`, `content`, `show_on`, `migrated` FROM wp_sidebars WHERE migrated != "1"' );

	foreach ( $test_widgets as $widget ) {

		$widgets = apply_filters( 'alter_initialization_widgets_' . $current_sidebar_short_name, $widgets );
		if ( ! empty( $widgets ) ) {
			do_action( 'create_widgets_for_sidebar', array( &$args ), $widgets );
		}

	}
}

add_action( 'create_widgets_for_sidebar', 'migrate_widgets_create_widgets', 10, 2 );
function migrate_widgets_create_widgets( $args, $widgets ) {
	extract( $args[0][0][0] );
	foreach ( $widgets as $widget => $widget_content ) {
		// The counter is increased on a widget basis. For instance, if you had three widgets,
		// two of them being the archives widget and one of the being a custom widget, then the
		// correct counter appended to each one of them would be archive-1, archive-2 and custom-1.
		// So the widget counter is not a global counter but one which counts the instances (the
		// widget_occurrence as I have called it) of each widget.
		$counter = count_widget_occurence( $widget, $args[0][0][0]['update_widget_content'] );
		// We add each instance to the active widgets...
		$args[0][0][0]['active_widgets'][ $sidebars[ $current_sidebar_short_name ] ][] = $widget . '-' . $counter;
		// ...and also save the content in another associative array.
		$args[0][0][0]['update_widget_content'][ $widget ][ $counter ] = $widget_content;
	}
}

function count_widget_occurence( $widget, $update_widget_content ) {
	$widget_occurrence = 0;
	// We look at the update_widget_content array which stores each
	// instance of the current widget with the current counter in an 
	// associative array. The key of this array is the name of the 
	// current widget.
	// Having three archives widgets for instance would look like this:
	// 'update_widget_content'['archives'] => [1][2][3] 
	if ( array_key_exists( $widget, $update_widget_content ) ) {
		$widget_counters = array_keys( $update_widget_content[ $widget ] );
		$widget_occurrence = end( $widget_counters );
	}
	$widget_occurrence++;
	return $widget_occurrence;
}

add_filter( 'alter_initialization_sidebars', 'current_initialization_sidebars' ) ;
// Use this filter hook to specify which sidebars you want to initialize
function current_initialization_sidebars( $sidebars ) {
	// The sidebars are assigned in this manner.
	// The array key is very important because it is used as a suffix in the initialization function
	// for each sidebar. The value is what is used in the html attributes.
	$sidebars['test'] = 'sidebar-test';
	return $sidebars;
}

add_filter( 'alter_initialization_widgets_test', 'migrate_widgets' );
// Add a filter hook for each sidebar you have. The hook name is derived from
// the array keys passed in the alter_initialization_sidebars filter. 
// Each filter has a name of 'alter_initialization_widgets_' and the array 
// key appended to it.
function migrate_widgets( $widgets ) {
	// add widgets to sidebar
	global $wpdb;

	$imported_widgets = $wpdb->get_results( 'SELECT `title`, `content`, `show_on`, `migrated` FROM wp_sidebars WHERE migrated != "1"' );

	foreach ( $imported_widgets as $key => $widget ) {
		if ( 0 === $key ) {
			$widget_id = $key + 1;
			$new_widget['custom_html'] = array(
				'title' => $widget->title,
				'content' => $widget->content,
				'wc_cache' => 'yes',
			);

			$data['wl']['custom_html-' . $widget_id ] = array(
				'incexc' => array(
					'condition' => 'selected',
				),
				'custom_post_types_taxonomies' => array(
					'is_singular-wp_log' => '0',
					'is_singular-newsletter' => '0',
					'is_singular-test' => '0',
					'is_tax-wp_log_type' => '0',
				),
				'location' => array(
					'is_front_page' => '0',
					'is_home' => '0',
					'is_singular' => '0',
					'is_single' => '0',
					'is_page' => '0',
					'is_attachment' => '0',
					'is_search' => '0',
					'is_404' => '0',
					'is_archive' => '0',
					'is_date' => '0',
					'is_day' => '0',
					'is_month' => '0',
					'is_year' => '0',
					'is_category' => '0',
					'is_tag' => '0',
					'is_author' => '0',
				),
				'word_count' => array(
					'check_wordcount' => '0',
					'check_wordcount_type' => 'less',
					'word_count' => '',
				),
				'url' => array(
					'urls' => '',
				),
				'admin_notes' => array(
					'notes' => '',
				),
			);

			$data['wl']['custom_html-' . $widget_id ]['incexc']['condition'] = 'selected';

			if ( '<front>' === $widget->show_on ) {
				$data['wl']['custom_html-' . $widget_id ]['location']['is_front_page'] = '1';
			} else {
				$url = str_replace( '/%', '/*', $widget->show_on );
				$data['wl']['custom_html-' . $widget_id ]['url']['urls'] = $url;
			}

			$context_options = apply_filters(
				'widget_context_options',
				(array) get_option( 'widget_logic_options', array() )
			);

			// Add / Update
			$context_options = array_merge( $context_options, $data['wl'] );

			$sidebars_widgets = wp_get_sidebars_widgets();
			$all_widget_ids = array();

			update_option( 'widget_logic_options', $context_options );

			$update = $wpdb->query( 'UPDATE wp_sidebars SET `migrated` = "1" WHERE `title` = "' . $widget->title . '"' );
		}

	}

	return $new_widget;
}



//add_filter( 'widget_update_callback', 'test_widget_update', 20, 3 );
function test_widget_update( $instance, $new_instance, $old_instance ) {

	//error_log('start');
	global $wpdb;

	$test_widgets = $wpdb->get_results( 'SELECT `title`, `content`, `show_on`, `migrated` FROM wp_sidebars WHERE migrated != "1"' );

	foreach ( $test_widgets as $key => $widget ) {
		//error_log('key is ' . $key );
		$data = array();
		if ( 0 === $key ) {
			//error_log('start');
			$data['conditions']['action'] = 'show';
			if ( '<front>' === $widget->show_on ) {
				//error_log('yep');
				//$new_widget['is_front_page'] = '1';
				//$new_widget['location']['is_front_page'] = '1';
				$data['conditions']['rules_major'][] = 'page';
				$data['conditions']['rules_minor'][] = 'front';
			} else {
				$url = str_replace( '/%', '', $widget->show_on );
				$url = str_replace( '%', '', $url );
				$category = get_category_by_slug( $url );
				if ( false !== $category ) {
					$id = $category->term_id;
					$data['conditions']['rules_major'][] = 'category';
					$data['conditions']['rules_minor'][] = (string) $id;
				}
			}

			if ( empty( $data['conditions'] ) ) {
				return $instance;
			}

			//error_log('we have some conditions');

			$conditions = array();
			$conditions['action'] = $data['conditions']['action'];
			$conditions['match_all'] = ( isset( $data['conditions']['match_all'] ) ? '1' : '0' );
			$conditions['rules'] = array();

			foreach ( $data['conditions']['rules_major'] as $index => $major_rule ) {
				//error_log('index is ' . $index . ' and major rule is ' . $major_rule);
				if ( ! $major_rule )
					continue;

				$conditions['rules'][] = array(
					'major' => $major_rule,
					'minor' => isset( $data['conditions']['rules_minor'][$index] ) ? $data['conditions']['rules_minor'][$index] : '',
					'has_children' => isset( $data['conditions']['page_children'][$index] ) ? true : false,
				);
			}

			if ( ! empty( $conditions['rules'] ) )
				$instance['conditions'] = $conditions;
			else
				unset( $instance['conditions'] );

			if (
					( isset( $instance['conditions'] ) && ! isset( $old_instance['conditions'] ) )
					||
					(
						isset( $instance['conditions'], $old_instance['conditions'] )
						&&
						serialize( $instance['conditions'] ) != serialize( $old_instance['conditions'] )
					)
				) {

			}
			else if ( ! isset( $instance['conditions'] ) && isset( $old_instance['conditions'] ) ) {

			}

			$update = $wpdb->query( 'UPDATE wp_sidebars SET `migrated` = "1" WHERE `title` = "' . $widget->title . '"' );
		}
	}

	//error_log('instance is ' . print_r($instance, true));

	return $instance;
}



register_activation_hook( __FILE__, 'migrate_widgets_activation_function' );
function migrate_widgets_activation_function() {
	initialize_sidebars();
}