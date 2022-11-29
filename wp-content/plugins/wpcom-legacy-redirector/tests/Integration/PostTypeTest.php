<?php

namespace Automattic\LegacyRedirector\Tests\Integration;

use Automattic\LegacyRedirector\Post_Type;

final class PostTypeTest extends TestCase {
	public function test_post_type_is_registered() {
		$this->assertTrue( post_type_exists( Post_Type::POST_TYPE ) );
	}
}
