<?php

namespace Automattic\LegacyRedirector\Tests\Unit;

use WPCOM_Legacy_Redirector_CLI;
use Yoast\WPTestUtils\BrainMonkey\TestCase;

final class CliTest extends TestCase {

	/**
	* @covers WPCOM_Legacy_Redirector_CLI::__construct
	*/
	public function test_cli_class_is_instantiable() {
		$cli = new WPCOM_Legacy_Redirector_CLI();

		self::assertInstanceOf( WPCOM_Legacy_Redirector_CLI::class, $cli );
	}
}
