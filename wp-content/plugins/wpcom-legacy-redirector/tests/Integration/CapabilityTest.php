<?php

namespace Automattic\LegacyRedirector\Tests\Integration;

use Automattic\LegacyRedirector\Capability;
use WP_User;

/**
 * CapabilityTest class.
 */
final class CapabilityTest extends TestCase {

	/**
	 * tearDown method to be called after each test.
	 *
	 * @return void
	 */
	public function tearDown() {

		(new Capability())->unregister();
	}

	/**
	 * Test capabilities for Capability::MANAGE_REDIRECTS_CAPABILITY.
	 *
	 * @return void
	 */
	public function test_new_admin_capability() {

		$capability = new Capability();

		// We need to force clear capabilities here as the wp_options `roles` option might not get cleared after a failed test.
		$capability->unregister();

		// in WP_User class, if multisite and user is administrator, all capabilities are allowed, so this test is not useful.
		if ( ! is_multisite() ) {
			// We check if a new admin user does not have the redirect capability.
			$user_id = 1;
			$this->assertUserNotHasRedirectCapability( $user_id );
		}

		$this->assertRoleNotHasRedirectsCapability( 'administrator' );
		$this->assertRoleNotHasRedirectsCapability( 'editor' );
		$this->assertRoleNotHasRedirectsCapability( 'subscriber' );
		$this->assertRoleNotHasRedirectsCapability( '' );

		$capability->register();

		$this->assertRoleHasRedirectsCapability( 'administrator' );
		$this->assertRoleHasRedirectsCapability( 'editor' );
		$this->assertRoleNotHasRedirectsCapability( 'subscriber' ); // Should be no change.
		$this->assertRoleNotHasRedirectsCapability( '' ); // Should be no change.
	}

	/**
	 * Test the Capability unregister method.
	 *
	 * @return void
	 */
	public function test_capability_can_be_unregistered() {
		$capability = new Capability();
		$capability->register();

		$this->assertFalse( $capability->register() );

		$this->assertRoleHasRedirectsCapability( 'administrator' );

		$capability->unregister();

		$this->assertRoleNotHasRedirectsCapability( 'administrator' );

		$this->assertTrue( $capability->register() );
	}

	/**
	 * Check if a specific user has Redirects capability.
	 *
	 * @param int|WP_User $user ID of the user, or WP_User object.
	 * @return bool True if the user has the redirects capability, false otherwise.
	 */
	private function assertUserHasRedirectCapability( $user ) {

		if ( is_numeric( $user ) ) {
			$user = wp_set_current_user( $user );
		}

		return $this->assertTrue( $user->has_cap( Capability::MANAGE_REDIRECTS_CAPABILITY ) );
	}

	/**
	 * Check if a specific user does NOT have Redirects capability.
	 *
	 * @param int|WP_User $user ID of the user, or WP_User object.
	 * @return bool True if the user does not have the redirects capability, false otherwise.
	 */
	private function assertUserNotHasRedirectCapability( $user ) {

		if ( is_numeric( $user ) ) {
			$user = wp_set_current_user( $user );
		}

		return $this->assertFalse( $user->has_cap( Capability::MANAGE_REDIRECTS_CAPABILITY ) );
	}

	/**
	 * Check if a role has Redirects capability.
	 *
	 * @param string $role Name of the role to check e.g. administrator.
	 * @return bool True if the role has the redirects capability, false otherwise.
	 */
	private function assertRoleHasRedirectsCapability( $role ) {
		$user_id = $this->factory->user->create( array( 'role' => $role ) );
		$user    = wp_set_current_user( $user_id );

		return $this->assertUserHasRedirectCapability( $user );
	}

	/**
	 * Check if a role does NOT have Redirects capability.
	 *
	 * @param string $role Name of the role to check e.g. administrator.
	 * @return bool True if the role does not have the redirects capability, false otherwise.
	 */
	private function assertRoleNotHasRedirectsCapability( $role ) {
		$user_id = $this->factory->user->create( array( 'role' => $role ) );
		$user    = wp_set_current_user( $user_id );

		return $this->assertUserNotHasRedirectCapability( $user );
	}
}
