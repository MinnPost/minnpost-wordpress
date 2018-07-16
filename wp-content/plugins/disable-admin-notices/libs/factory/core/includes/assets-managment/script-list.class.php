<?php
	/**
	 * The file contains a class to manage script assets.
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

	if( !class_exists('Wbcr_Factory400_ScriptList') ) {

		/**
		 * Script List
		 *
		 * @since 1.0.0
		 */
		class Wbcr_Factory400_ScriptList extends Wbcr_Factory400_AssetsList {

			public $localize_data = array();
			public $use_ajax = false;

			/**
			 * @param string $source
			 */
			public function connect($source = 'wordpress')
			{

				// register all global required scripts
				if( !empty($this->required[$source]) ) {
					foreach($this->required[$source] as $script) {
						if( 'wordpress' === $source ) {
							wp_enqueue_script($script);
						} elseif( 'bootstrap' === $source ) {
							$this->plugin->bootstrap->enqueueScript($script);
						}
					}
				}

				if( $source == 'bootstrap' ) {
					return;
				}

				$is_first_script = true;
				$is_footer = false;

				// register all other scripts
				foreach(array($this->header_place, $this->footer_place) as $scriptPlace) {
					foreach($scriptPlace as $script) {

						wp_register_script($script, $script, array(), $this->plugin->getPluginVersion(), $is_footer);

						if( $is_first_script && $this->use_ajax ) {
							wp_localize_script($script, 'factory', array('ajaxurl' => admin_url('admin-ajax.php')));
						}

						if( !empty($this->localize_data[$script]) ) {

							wp_localize_script($script, $this->localize_data[$script][0], $this->localize_data[$script][1]);
						}

						wp_enqueue_script($script);

						$is_first_script = false;
					}

					$is_footer = true;
				}
			}

			public function useAjax()
			{
				$this->use_ajax = true;
			}

			public function localize($varname, $data)
			{
				$bindTo = count($this->all) == 0
					? null
					: end($this->all);

				if( !$bindTo ) {
					return;
				}

				$this->localize_data[$bindTo] = array($varname, $data);

				return $this;
			}
		}
	}
