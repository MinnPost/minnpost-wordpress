<?php
	/**
	 * Factory Plugin
	 *
	 * @author Alex Kovalev <alex.kovalevv@gmail.com>
	 * @copyright (c) 2018, Webcraftic Ltd
	 *
	 * @package core
	 * @since 1.0.0
	 */

	// Exit if accessed directly
	if( !defined('ABSPATH') ) {
		exit;
	}

	if( defined('FACTORY_400_LOADED') ) {
		return;
	}
	define('FACTORY_400_LOADED', true);

	define('FACTORY_400_VERSION', '000');

	define('FACTORY_400_DIR', dirname(__FILE__));
	define('FACTORY_400_URL', plugins_url(null, __FILE__));

	#comp merge
	require_once(FACTORY_400_DIR . '/includes/functions.php');
	require_once(FACTORY_400_DIR . '/includes/request.class.php');
	require_once(FACTORY_400_DIR . '/includes/base.class.php');

	require_once(FACTORY_400_DIR . '/includes/assets-managment/assets-list.class.php');
	require_once(FACTORY_400_DIR . '/includes/assets-managment/script-list.class.php');
	require_once(FACTORY_400_DIR . '/includes/assets-managment/style-list.class.php');

	require_once(FACTORY_400_DIR . '/includes/plugin.class.php');

	require_once(FACTORY_400_DIR . '/includes/activation/activator.class.php');
	require_once(FACTORY_400_DIR . '/includes/activation/update.class.php');
	#endcomp
