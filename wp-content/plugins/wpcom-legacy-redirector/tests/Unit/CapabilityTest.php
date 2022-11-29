<?php
namespace Automattic\LegacyRedirector\Tests\Unit;

use Automattic\LegacyRedirector\Capability;
use Brain\Monkey\Functions;
use Yoast\WPTestUtils\BrainMonkey\TestCase;

/**
 * Capability Class Unit Test
 */
final class CapabilityTest extends TestCase {

	/**
	 * Test Capability->register method to make sure update_option is only called once and mocking wpcom_vip_add_role_caps function
	 * @covers \Automattic\LegacyRedirector\Capability::register
	 * @uses \Automattic\LegacyRedirector\Capability::get_capabilities_version_key
	 * @return void
	 */
	public function test_register_method_is_only_called_once() {

		$capability = new Capability();

		Functions\when( 'wpcom_vip_add_role_caps' )
			->justReturn( true );

		Functions\expect( 'get_option' )
			->once()
			->andReturn( 0 );

			Functions\expect( 'update_option' )
			->once()
			->andReturn( true );

		$capability->register();

		Functions\expect( 'get_option' )
			->once()
			->andReturn( $capability::CAPABILITIES_VER );

			Functions\expect( 'update_option' )
			->never();

		$capability->register();
	}
}
