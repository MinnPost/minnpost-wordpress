<?php

	/**
	 * Helpers functions
	 *
	 * @author Alex Kovalev <alex.kovalevv@gmail.com>
	 * @copyright (c) 2018, Webcraftic Ltd
	 *
	 * @package clearfy
	 * @since 1.0.0
	 */

	// Exit if accessed directly
	if( !defined('ABSPATH') ) {
		exit;
	}

	if( !class_exists('WbcrFactoryClearfy200_Helpers') ) {
		class WbcrFactoryClearfy200_Helpers {

			/**
			 * Is permalink enabled?
			 * @global WP_Rewrite $wp_rewrite
			 * @since 1.0.0
			 * @return bool
			 */
			public static function isPermalink()
			{
				global $wp_rewrite;

				if( !isset($wp_rewrite) || !is_object($wp_rewrite) || !$wp_rewrite->using_permalinks() ) {
					return false;
				}

				return true;
			}

			/**
			 * Display 404 page to bump bots and bad guys
			 *
			 * @param bool $simple If true force displaying basic 404 page
			 */
			public static function setError404()
			{
				global $wp_query;

				if( function_exists('status_header') ) {
					status_header('404');
					nocache_headers();
				}

				if( $wp_query && is_object($wp_query) ) {
					$wp_query->set_404();
					get_template_part(404);
				} else {
					global $pagenow;

					$pagenow = 'index.php';

					if( !defined('WP_USE_THEMES') ) {
						define('WP_USE_THEMES', true);
					}

					wp();

					$_SERVER['REQUEST_URI'] = self::userTrailingslashit('/hmwp_404');

					require_once(ABSPATH . WPINC . '/template-loader.php');
				}

				exit();
			}

			public static function useTrailingSlashes()
			{
				return ('/' === substr(get_option('permalink_structure'), -1, 1));
			}

			public static function userTrailingslashit($string)
			{
				return self::useTrailingSlashes()
					? trailingslashit($string)
					: untrailingslashit($string);
			}

			/**
			 * Returns true if a needle can be found in a haystack
			 *
			 * @param string $string
			 * @param string $find
			 * @param bool $case_sensitive
			 * @return bool
			 */
			public static function strContains($string, $find, $case_sensitive = true)
			{
				if( empty($string) || empty($find) ) {
					return false;
				}

				$pos = $case_sensitive
					? strpos($string, $find)
					: stripos($string, $find);

				return !($pos === false);
			}

			/**
			 * Tests if a text starts with an given string.
			 *
			 * @param string $string
			 * @param string $find
			 * @param bool $case_sensitive
			 * @return bool
			 */
			public static function strStartsWith($string, $find, $case_sensitive = true)
			{
				if( $case_sensitive ) {
					return strpos($string, $find) === 0;
				}

				return stripos($string, $find) === 0;
			}

			/**
			 * Tests if a text ends with an given string.
			 *
			 * @param $string
			 * @param $find
			 * @param bool $case_sensitive
			 * @return bool
			 */
			public static function strEndsWith($string, $find, $case_sensitive = true)
			{
				$expected_position = strlen($string) - strlen($find);

				if( $case_sensitive ) {
					return strrpos($string, $find, 0) === $expected_position;
				}

				return strripos($string, $find, 0) === $expected_position;
			}

			public static function arrayMergeInsert(array $arr, array $inserted, $position = 'bottom', $key = null)
			{
				if( $position == 'top' ) {
					return array_merge($inserted, $arr);
				}
				$key_position = ($key === null)
					? false
					: array_search($key, array_keys($arr));
				if( $key_position === false OR ($position != 'before' AND $position != 'after') ) {
					return array_merge($arr, $inserted);
				}
				if( $position == 'after' ) {
					$key_position++;
				}

				return array_merge(array_slice($arr, 0, $key_position, true), $inserted, array_slice($arr, $key_position, null, true));
			}

			public static function maybeGetPostJson($name)
			{
				if( isset($_POST[$name]) AND is_string($_POST[$name]) ) {
					$result = json_decode(stripslashes($_POST[$name]), true);
					if( !is_array($result) ) {
						$result = array();
					}

					return $result;
				} else {
					return array();
				}
			}

			public static function getEscapeJson(array $data)
			{
				return htmlspecialchars(json_encode($data), ENT_QUOTES, 'UTF-8');
			}
		}
	}