<?php

/**
 * Copyright (c) 2015 Khang Minh <contact@betterwp.net>
 * @license http://www.gnu.org/licenses/gpl.html GNU GENERAL PUBLIC LICENSE VERSION 3.0 OR LATER
 */

// we need this check here because sometimes a plugin must include BWP_Version
// separately
if (!class_exists('BWP_Version')) :

/**
 * Class BWP_Version
 * @author Khang Minh <contact@betterwp.net>
 */
class BWP_Version
{
	/**
	 * Default version constraints
	 */
	public static $php_ver = '5.2.0';
	public static $wp_ver  = '3.6';

	private function __construct() {}

	public static function warn_required_versions($title, $domain, $php_ver = null, $wp_ver = null)
	{
		$php_ver = $php_ver ? $php_ver : self::$php_ver;
		$wp_ver  = $wp_ver ? $wp_ver : self::$wp_ver;

		echo '<div class="error"><p>' . sprintf(
			__('%s requires WordPress <strong>%s</strong> or higher '
			. 'and PHP <strong>%s</strong> or higher. '
			. 'The plugin will not function until you update your software. '
			. 'Please deactivate this plugin.', $domain),
			$title, $wp_ver, $php_ver)
		. '</p></div>';
	}

	/**
	 * Get the current system's PHP version
	 *
	 * If the first parameter is provided this will return whether or not the
	 * current system's PHP version is greater than or equal to the provided one
	 *
	 * @param string $version
	 * @return mixed string|bool
	 */
	public static function get_current_php_version($version = '')
	{
		return $version ? version_compare(PHP_VERSION, $version, '>=') : PHP_VERSION;
	}

	/**
	 * Get the current system's PHP version ID
	 *
	 * If the first parameter is provided this will return whether or not the
	 * current system's PHP version is greater than or equal to the provided one
	 *
	 * @param integer $version
	 * @return mixed integer|bool
	 */
	public static function get_current_php_version_id($version = null)
	{
		// @since rev 157 PHP_VERSION_ID is only available since PHP 5.2.7
		if (!defined('PHP_VERSION_ID')) {
			$version = explode('.', PHP_VERSION);
			define('PHP_VERSION_ID', $version[0] * 10000 + $version[1] * 100 + $version[2]);
		}

		return $version ? PHP_VERSION_ID >= (int) $version : PHP_VERSION_ID;
	}
}

endif;
