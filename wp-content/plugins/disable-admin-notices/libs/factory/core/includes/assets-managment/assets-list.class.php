<?php
	/**
	 * The class contains a base class for all lists of assets.
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
	if( !class_exists('Wbcr_Factory400_AssetsList') ) {
		/**
		 * Assets List
		 *
		 * @since 1.0.0
		 */
		class Wbcr_Factory400_AssetsList {

			protected $all = array();
			public $header_place = array();
			public $footer_place = array();
			public $required = array();

			protected $default_place;

			/**
			 * @var Wbcr_Factory400_Plugin
			 */
			protected $plugin;

			/**
			 * @param Wbcr_Factory400_Plugin $plugin
			 * @param bool $defaultIsFooter
			 */
			public function __construct(Wbcr_Factory400_Plugin $plugin, $defaultIsFooter = true)
			{
				$this->plugin = $plugin;

				if( $defaultIsFooter ) {
					$this->default_place = &$this->footer_place;
				}
				if( !$defaultIsFooter ) {
					$this->default_place = &$this->header_place;
				}
			}

			/**
			 * Adds new items to the collection (default place).
			 * @param mixed
			 */
			public function add()
			{
				foreach(func_get_args() as $item) {
					$this->all[] = $item;
					$this->default_place[] = $item;
				}

				return $this;
			}

			/**
			 * Remove items from the collection
			 * @return $this
			 */
			public function deregister()
			{
				foreach(func_get_args() as $item) {

					if( !is_string($item) ) {
						return $this;
					}

					$key_in_all = array_search($item, $this->all);
					$key_in_default_place = array_search($item, $this->default_place);
					$key_in_header_place = array_search($item, $this->header_place);
					$key_inFooterPlace = array_search($item, $this->footer_place);

					if( $key_in_all ) {
						unset($this->all[$key_in_all]);
					}
					if( $key_in_default_place ) {
						unset($this->default_place[$key_in_default_place]);
					}
					if( $key_in_header_place ) {
						unset($this->header_place[$key_in_header_place]);
					}
					if( $key_inFooterPlace ) {
						unset($this->footer_place[$key_inFooterPlace]);
					}
				}

				return $this;
			}

			/**
			 * Adds new items to the collection (header).
			 * @param mixed
			 */
			public function addToHeader()
			{

				foreach(func_get_args() as $item) {
					$this->all[] = $item;
					$this->header_place[] = $item;
				}

				return $this;
			}

			/**
			 * Adds new items to the collection (footer).
			 * @param mixed
			 */
			public function addToFooter()
			{

				foreach(func_get_args() as $item) {
					$this->all[] = $item;
					$this->footer_place[] = $item;
				}

				return $this;
			}

			/**
			 * Checks whether the collection is empty.
			 *
			 * @param string $source if the 'bootstrap' specified, checks only whether the bootstrap assets were required.
			 * @return boolean
			 */
			public function isEmpty($source = 'wordpress')
			{
				if( 'bootstrap' === $source ) {
					return empty($this->required[$source]);
				}

				return empty($this->all) && empty($this->required);
			}

			public function IsHeaderEmpty()
			{
				return empty($this->header_place);
			}

			public function IsFooterEmpty()
			{
				return empty($this->footer_place);
			}

			/**
			 * Adds new items to the requried collection.
			 * @param mixed
			 */
			public function request($items, $source = 'wordpress')
			{

				if( is_array($items) ) {
					foreach($items as $item) {
						$this->required[$source][] = $item;
					}
				} else {
					$this->required[$source][] = $items;
				}

				return $this;
			}
		}
	}
