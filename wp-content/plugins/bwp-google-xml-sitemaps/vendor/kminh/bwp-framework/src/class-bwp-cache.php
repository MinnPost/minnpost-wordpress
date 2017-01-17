<?php

/**
 * Copyright (c) 2015 Khang Minh <contact@betterwp.net>
 * @license http://www.gnu.org/licenses/gpl.html GNU GENERAL PUBLIC LICENSE VERSION 3.0 OR LATER
 */

/**
 * Provides caching mechanism for BWP plugins
 *
 * @author Khang Minh <contact@betterwp.net>
 */
class BWP_Cache
{
	protected $bridge;

	/**
	 * The group to store all items under
	 *
	 * @var string
	 */
	protected $group;

	public function __construct(BWP_WP_Bridge $bridge, $group)
	{
		$this->bridge = $bridge;
		$this->group  = $group;
	}

	/**
	 * Set cached value of a property
	 *
	 * @param string $key
	 * @param mixed $value
	 * @param bool $shared whether to share with other plugins
	 * @uses wp_cache_set
	 * @return true if successful, false if failed
	 */
	public function set($key, $value, $shared = false)
	{
		return $this->bridge->wp_cache_set($key, $value, $shared ? 'bwp_plugins' : $this->group);
	}

	/**
	 * Get cached value of a property
	 *
	 * @param string $key
	 * @param bool $shared whether to get shared value
	 * @param mixed $not_found_value value to return when key not found in cache
	 *
	 * @uses wp_cache_get
	 * @return mixed
	 */
	public function get($key, $shared = false, $not_found_value = null)
	{
		$value = $this->bridge->wp_cache_get($key, $shared ? 'bwp_plugins' : $this->group, false, $found);

		if ($found) {
			return $value;
		}

		return $not_found_value;
	}
}
