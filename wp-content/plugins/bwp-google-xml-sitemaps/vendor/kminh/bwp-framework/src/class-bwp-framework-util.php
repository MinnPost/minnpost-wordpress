<?php

/**
 * Copyright (c) 2015 Khang Minh <contact@betterwp.net>
 * @license http://www.gnu.org/licenses/gpl.html GNU GENERAL PUBLIC LICENSE VERSION 3.0 OR LATER
 */

/**
 * @author Khang Minh <contact@betterwp.net>
 */
class BWP_Framework_Util
{
	private function __construct() {}

	public static function is_debugging()
	{
		return defined('WP_DEBUG') && WP_DEBUG;
	}

	public static function is_multisite()
	{
		if (defined('BWP_MULTISITE'))
			return BWP_MULTISITE;

		if (function_exists('is_multisite') && is_multisite())
			return true;

		if (defined('MULTISITE'))
			return MULTISITE;

		if (defined('SUBDOMAIN_INSTALL') || defined('VHOST') || defined('SUNRISE'))
			return true;

		return false;
	}

	public static function is_subdomain_install()
	{
		if (defined('BWP_SUBDOMAIN_INSTALL'))
			return BWP_SUBDOMAIN_INSTALL;

		if (defined('SUBDOMAIN_INSTALL') && SUBDOMAIN_INSTALL)
			return true;

		return false;
	}

	public static function is_admin_user()
	{
		if (function_exists('current_user_can') && current_user_can('manage_options'))
			return true;

		if (defined('BWP_IS_ADMIN_USER'))
			return BWP_IS_ADMIN_USER;

		return false;
	}

	public static function is_super_admin()
	{
		if (function_exists('is_super_admin') && is_super_admin())
			return true;

		if (defined('BWP_IS_SUPER_ADMIN'))
			return BWP_IS_SUPER_ADMIN;

		return false;
	}

	/**
	 * Whether the currently logged in user is a site admin
	 *
	 * A site admin is the normal admin on a standard installation, and is a
	 * super admin on a multisite installation
	 *
	 * @return bool
	 */
	public static function is_site_admin()
	{
		// not on a multisite installation
		if (!self::is_multisite() && self::is_admin_user())
			return true;

		// on a multisite installation, must be a super admin
		if (self::is_multisite() && self::is_super_admin())
			return true;

		return false;
	}

	/**
	 * Whether the currently logged in user is a site admin in a multisite
	 * installation
	 *
	 * @return bool
	 */
	public static function is_multisite_admin()
	{
		if (self::is_multisite() && self::is_super_admin())
			return true;

		return false;
	}

	public static function is_on_main_blog()
	{
		global $blog_id;

		// not a multisite installation, we're always on main blog
		if (!self::is_multisite())
			return true;

		return intval($blog_id) === 1;
	}

	public static function can_update_site_option()
	{
		return self::is_site_admin() && self::is_on_main_blog();
	}

	public static function is_apache()
	{
		if (isset($_SERVER['SERVER_SOFTWARE'])
			&& false !== stripos($_SERVER['SERVER_SOFTWARE'], 'apache')
		) {
			return true;
		}
		return false;
	}

	public static function is_nginx()
	{
		if (isset($_SERVER['SERVER_SOFTWARE'])
			&& false !== stripos($_SERVER['SERVER_SOFTWARE'], 'nginx')
		) {
			return true;
		}

		return false;
	}

	/**
	 * Get a variable with a specific key from $_REQUEST
	 *
	 * This should sanitize the variable's value before returning it.
	 *
	 * @param string $key
	 * @param bool $empty_as_null consider empty value as null. Should check
	 *                            the sanitized value.
	 * @return mixed|null null if $key is not set
	 */
	public static function get_request_var($key, $empty_as_null = true)
	{
		if (!isset($_REQUEST[$key]))
			return null;

		$value = $_REQUEST[$key];

		if (is_array($value))
		{
			$value = array_map('stripslashes', $value);
			$value = array_map('strip_tags', $value);
			$value = array_map('trim', $value);
		}
		else
		{
			$value = trim(strip_tags(stripslashes($value)));
		}

		if ($empty_as_null && empty($value))
			return null;

		return $value;
	}
}
