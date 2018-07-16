<?php
	/**
	 * The file contains a base class for plugin activators.
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

	if( !class_exists('Wbcr_Factory400_Activator') ) {
		/**
		 * Plugin Activator
		 *
		 * @since 1.0.0
		 */
		abstract class Wbcr_Factory400_Activator {

			/**
			 * Curent plugin.
			 * @var Wbcr_Factory400_Plugin
			 */
			public $plugin;

			public function __construct(Wbcr_Factory400_Plugin $plugin)
			{
				$this->plugin = $plugin;
			}

			public function activate()
			{
			}

			public function deactivate()
			{
			}

			public function update()
			{
			}

			// --------------------------------------------------------------------------------
			// Posts and pages
			// --------------------------------------------------------------------------------

			/**
			 * Adds post on activation.
			 * @return array Post info.
			 */
			public function addPost()
			{

				$args_count = func_num_args();

				$post_info_base = array();
				$meta_info_base = array();

				if( $args_count == 4 ) {

					$base = func_get_arg(0);

					$post_info_base = $base['post'];
					$meta_info_base = $base['meta'];
				}

				$option_name = ($args_count == 4)
					? func_get_arg(1)
					: func_get_arg(0);
				$post_info = ($args_count == 4)
					? func_get_arg(2)
					: func_get_arg(1);
				$meta_info = ($args_count == 4)
					? func_get_arg(3)
					: func_get_arg(2);

				if( $post_info == null ) {
					$post_info = array();
				}
				if( $meta_info == null ) {
					$meta_info = array();
				}

				$post_info = array_merge($post_info_base, $post_info);
				$meta_info = array_merge($meta_info_base, $meta_info);

				$insert_id = $this->createPost($post_info, $meta_info, $option_name);

				return array(
					'post_id' => $insert_id,
					'post' => $post_info,
					'meta' => $meta_info
				);
			}

			/**
			 * * Adds a page on activation.
			 *
			 * @return int|null|string|WP_Error
			 */
			public function addPage()
			{
				$option_name = func_get_arg(0);
				$post_info = func_get_arg(1);
				$meta_info = func_get_arg(2);

				if( $post_info == null ) {
					$post_info = array();
				}
				if( $meta_info == null ) {
					$meta_info = array();
				}

				$post_info['post_type'] = 'page';

				return $this->createPost($post_info, $meta_info, $option_name);
			}

			/**
			 * Creates post by using the specified info.
			 *
			 * @param array $post_info
			 * @param array $meta_info
			 * @param string $option_name
			 * @return int|null|string|WP_Error
			 */
			public function createPost($post_info, $meta_info, $option_name)
			{
				global $wpdb;

				$slug = $post_info['post_name'];
				$post_type = $post_info['post_type'];

				$postId = $wpdb->get_var($wpdb->prepare("SELECT ID FROM " . $wpdb->posts . " WHERE post_name = '%s' AND
                    post_type = '%s' LIMIT 1", $slug, $post_type));

				$option_value = $this->plugin->getOption($option_name);

				if( !$postId ) {
					$create = true;

					if( !empty($option_value) ) {
						$post_id = $wpdb->get_var($wpdb->prepare("SELECT ID FROM " . $wpdb->posts . " WHERE ID = '%d' AND
                            post_type = '%s' LIMIT 1", $option_value, $post_type));
						if( $post_id ) {
							$create = false;
						}
					};

					if( $create ) :
						if( !isset($post_info['post_status']) ) {
							$post_info['post_status'] = 'publish';
						}

						// '@' here is to hide unexpected output while plugin activation
						$option_value = @wp_insert_post($post_info);
						$postId = $option_value;
						$this->plugin->updateOption($option_name, $option_value);
					endif;
				} else {
					if( empty ($option_value) ) {
						$this->plugin->updateOption($option_name, $postId);
					}
				}

				$this->plugin->updateOption($option_name, $postId);

				// adds meta
				foreach($meta_info as $key => $value) {
					if( $value === true ) {
						$value = 'true';
					}
					if( $value === false ) {
						$value = 'false';
					}

					add_post_meta($postId, $key, $value);
				}

				return $postId;
			}
		}
	}