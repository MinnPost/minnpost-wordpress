<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://example.com
 * @since             0.0.1
 * @package           Permalink_Without_Parent_Category
 *
 * @wordpress-plugin
 * Plugin Name:       Permalink Without Parent Category
 * Plugin URI:        http://example.com/plugin-name-uri/
 * Description:       This allows posts that use %category% in the permalink pattern to only show the child category, not the parent.
 * Version:           0.0.1
 * Author:            Jonathan Stegall
 * Author URI:        http://jonathan stegall.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       permalink-without-parent-category
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-permalink-without-parent-category-activator.php
 */
function activate_Permalink_Without_Parent_Category() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-permalink-without-parent-category-activator.php';
	Permalink_Without_Parent_Category_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-permalink-without-parent-category-deactivator.php
 */
function deactivate_Permalink_Without_Parent_Category() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-permalink-without-parent-category-deactivator.php';
	Permalink_Without_Parent_Category_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_Permalink_Without_Parent_Category' );
register_deactivation_hook( __FILE__, 'deactivate_Permalink_Without_Parent_Category' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-permalink-without-parent-category.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_Permalink_Without_Parent_Category() {

	$plugin = new Permalink_Without_Parent_Category();
	$plugin->run();

}
run_Permalink_Without_Parent_Category();
