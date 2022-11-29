<?php

namespace Automattic\LegacyRedirector\Tests\Integration;

use Yoast\WPTestUtils\WPIntegration\TestCase as WPTestUtilsTestCase;

abstract class TestCase extends WPTestUtilsTestCase {

	/**
	 * Makes sure the foundational stuff is sorted so tests work.
	 */
	public function setUp() {

		// We need to trick the plugin into thinking it's run by WP-CLI.
		if ( ! defined( 'WP_CLI' ) ) {
			define( 'WP_CLI', true );
		}

		// We need to trick the plugin into thinking we're in admin.
		if ( ! defined( 'WP_ADMIN' ) ) {
			define( 'WP_ADMIN', true );
		}
	}
}
