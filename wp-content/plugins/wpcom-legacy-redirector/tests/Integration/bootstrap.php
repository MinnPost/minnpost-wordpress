<?php

use Yoast\WPTestUtils\WPIntegration;

require_once dirname( dirname( __DIR__ ) ) . '/vendor/yoast/wp-test-utils/src/WPIntegration/bootstrap-functions.php';

$_tests_dir = getenv( 'WP_TESTS_DIR' );

if ( ! $_tests_dir ) {
	$_tests_dir = '/tmp/wordpress-tests-lib';
}

// Give access to tests_add_filter() function.
require_once $_tests_dir . '/includes/functions.php';

function _manually_load_plugin() {
	require dirname( dirname( __DIR__ ) ) . '/wpcom-legacy-redirector.php';
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

// Make sure the Composer autoload file has been generated.
WPIntegration\check_composer_autoload_exists();
// Load WordPress.
require $_tests_dir . '/includes/bootstrap.php';
/*
 * Register the custom autoloader to overload the PHPUnit MockObject classes when running on PHP 8.
 *
 * This function has to be called _last_, after the WP test bootstrap to make sure it registers
 * itself in FRONT of the Composer autoload (which also prepends itself to the autoload queue).
 */
WPIntegration\register_mockobject_autoloader();

// Add custom test case.
require __DIR__ . '/TestCase.php';
