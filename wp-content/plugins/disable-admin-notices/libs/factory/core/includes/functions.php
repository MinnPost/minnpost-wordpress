<?php
	/**
	 * Factory Function Library
	 *
	 * @author Alex Kovalev <alex.kovalevv@gmail.com>
	 * @copyright (c) 2018, Webcraftic Ltd
	 *
	 * @package factory-core
	 * @since 1.0.0
	 */
	
	// Exit if accessed directly
	if( !defined('ABSPATH') ) {
		exit;
	}
	
	if( function_exists('wbcr_factory_400_set_lazy_redirect') ) {
		/**
		 * Sets a lazy redirect.
		 *
		 * @since 3.0.6
		 * @return void
		 */
		function wbcr_factory_400_set_lazy_redirect($url)
		{
			update_option('wbcr_factory_400_lazy_redirect', $url);
		}
	}
	
	if( function_exists('wbcr_factory_400_do_lazy_redirect') ) {
		function wbcr_factory_400_do_lazy_redirect()
		{
			$url = get_option('wbcr_factory_400_lazy_redirect', null);
			
			if( empty($url) ) {
				return;
			}
			
			delete_option('wbcr_factory_400_lazy_redirect');
			wp_redirect($url);
		}
		
		add_action('admin_init', 'wbcr_factory_400_do_lazy_redirect');
	}
	
	/**
	 * Fires functions attached to a deprecated filter hook.
	 *
	 * When a filter hook is deprecated, the apply_filters() call is replaced with
	 * apply_filters_deprecated(), which triggers a deprecation notice and then fires
	 * the original filter hook.
	 *
	 * This is a copy of `apply_filters_deprecated` introduced in WP 4.6.
	 *
	 * @since 1.0.0
	 *
	 * @see wbcr_factory_400_deprecated_hook()
	 *
	 * @param string $tag The name of the filter hook.
	 * @param array $args Array of additional function arguments to be passed to apply_filters().
	 * @param string $version The version of BP Block Users that deprecated the hook.
	 * @param string $replacement Optional. The hook that should have been used.
	 * @param string $message Optional. A message regarding the change.
	 *
	 * @return mixed
	 */
	function wbcr_factory_400_apply_filters_deprecated($tag, $args, $version, $replacement = false, $message = null)
	{
		if( function_exists('apply_filters_deprecated') ) {
			return apply_filters_deprecated($tag, $args, $version, $replacement, $message);
		}
		if( !has_filter($tag) ) {
			return $args[0];
		}
		wbcr_factory_400_deprecated_hook($tag, $version, $replacement, $message);
		
		return apply_filters_ref_array($tag, $args);
	}
	
	/**
	 * Fires functions attached to a deprecated action hook.
	 *
	 * When an action hook is deprecated, the do_action() call is replaced with
	 * do_action_deprecated(), which triggers a deprecation notice and then fires
	 * the original hook.
	 *
	 * This is a copy of `do_action_deprecated` introduced in WP 4.6.
	 *
	 * @since 1.0.0
	 *
	 * @see _deprecated_hook()
	 *
	 * @param string $tag The name of the action hook.
	 * @param array $args Array of additional function arguments to be passed to do_action().
	 * @param string $version The version of BP Block Users that deprecated the hook.
	 * @param string $replacement Optional. The hook that should have been used.
	 * @param string $message Optional. A message regarding the change.
	 *
	 * @return void
	 */
	function wbcr_factory_400_do_action_deprecated($tag, $args, $version, $replacement = false, $message = null)
	{
		if( function_exists('do_action_deprecated') ) {
			do_action_deprecated($tag, $args, $version, $replacement, $message);
			
			return;
		}
		if( !has_action($tag) ) {
			return;
		}
		wbcr_factory_400_deprecated_hook($tag, $version, $replacement, $message);
		do_action_ref_array($tag, $args);
	}
	
	/**
	 * Marks a deprecated action or filter hook as deprecated and throws a notice.
	 *
	 * Use the 'wbcr_factory_400_deprecated_hook_run' action to get the backtrace describing where the
	 * deprecated hook was called.
	 *
	 * Default behavior is to trigger a user error if WP_DEBUG is true.
	 *
	 * This function is called by the do_action_deprecated() and apply_filters_deprecated()
	 * functions, and so generally does not need to be called directly.
	 *
	 * This is a copy of `_deprecated_hook` introduced in WP 4.6.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @param string $hook The hook that was used.
	 * @param string $version The version of WordPress that deprecated the hook.
	 * @param string $replacement Optional. The hook that should have been used.
	 * @param string $message Optional. A message regarding the change.
	 */
	function wbcr_factory_400_deprecated_hook($hook, $version, $replacement = null, $message = null)
	{
		/**
		 * Fires when a deprecated hook is called.
		 *
		 * @since 1.0.0
		 *
		 * @param string $hook The hook that was called.
		 * @param string $replacement The hook that should be used as a replacement.
		 * @param string $version The version of BP Block Users that deprecated the argument used.
		 * @param string $message A message regarding the change.
		 */
		do_action('deprecated_hook_run', $hook, $replacement, $version, $message);

		/**
		 * Filter whether to trigger deprecated hook errors.
		 *
		 * @since 1.0.0
		 *
		 * @param bool $trigger Whether to trigger deprecated hook errors. Requires
		 *                      `WP_DEBUG` to be defined true.
		 */
		if( WP_DEBUG && apply_filters('deprecated_hook_trigger_error', true) ) {
			$message = empty($message)
				? ''
				: ' ' . $message;
			if( !is_null($replacement) ) {
				trigger_error(sprintf(__('%1$s is <strong>deprecated</strong> since version %2$s! Use %3$s instead.'), $hook, $version, $replacement) . $message);
			} else {
				trigger_error(sprintf(__('%1$s is <strong>deprecated</strong> since version %2$s with no alternative available.'), $hook, $version) . $message);
			}
		}
	}

