<?php
	/**
	 * The file contains a class to manage style assets.
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

	if( !class_exists('Wbcr_Factory400_StyleList') ) {

		/**
		 * Style List
		 *
		 * @since 1.0.0
		 */
		class Wbcr_Factory400_StyleList extends Wbcr_Factory400_AssetsList {

			public function connect($source = 'wordpress')
			{

				// register all global required scripts
				if( !empty($this->required[$source]) ) {

					foreach($this->required[$source] as $style) {
						if( 'wordpress' === $source ) {
							wp_enqueue_style($style);
						} elseif( 'bootstrap' === $source ) {
							$this->plugin->bootstrap->enqueueStyle($style);
						}
					}
				}

				if( $source == 'bootstrap' ) {
					return;
				}

				// register all other styles
				foreach($this->all as $style) {
					wp_enqueue_style(md5($style), $style, array(), $this->plugin->getPluginVersion());
				}
			}
		}
	}
