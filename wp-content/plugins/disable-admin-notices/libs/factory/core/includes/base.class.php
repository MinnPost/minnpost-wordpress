<?php
	// Exit if accessed directly
	if( !defined('ABSPATH') ) {
		exit;
	}
	
	if( !class_exists('Wbcr_Factory400_Base') ) {
		class  Wbcr_Factory400_Base {
			
			/**
			 * Буферизуем опции плагинов в этот атрибут, для быстрого доступа
			 *
			 * @var array
			 */
			private static $_opt_buffer = array();
			
			/**
			 * Префикс для пространства имен среди опций Wordpress
			 *
			 * @var string
			 */
			protected $prefix;
			
			/**
			 * Экзамеляр класса Wbcr_Factory400_Request, необходим управляет http запросами
			 *
			 * @var Wbcr_Factory400_Request
			 */
			public $request;
			
			public function __construct($plugin_path, $data)
			{
				$this->prefix = isset($data['prefix'])
					? $data['prefix']
					: null;
				
				if( empty($this->prefix) || !is_string($this->prefix) ) {
					throw new Exception('Не передан один из обязательных атрибутов (prefix) или атрибует не соотвествует типу данных string.');
				}
				
				$this->request = new Wbcr_Factory400_Request();

				if( !isset(self::$_opt_buffer[$this->prefix]) ) {
					$cache_options = get_option($this->prefix . 'cache_options', array());

					if( empty($cache_options) || !is_array($cache_options) ) {
						$cache_options = array();
						delete_option($this->prefix . 'cache_options');
					}

					self::$_opt_buffer[$this->prefix] = $cache_options;
				}
			}
			
			/**
			 * Получает опцию из кеша или из базы данные, если опция не кешируемая,
			 * то опция тянется только из базы данных. Не кешируемые опции это массивы,
			 * сериализованные массивы, строки больше 150 символов
			 *
			 * @param string $option_name
			 * @param bool $default
			 * @return mixed|void
			 */
			public function getOption($option_name, $default = false)
			{
				if( $option_name == 'cache_options' ) {
					return $default;
				}
				
				$get_cache_option = $this->getOptionFromCache($option_name);
				
				if( !is_null($get_cache_option) ) {
					return $get_cache_option === false
						? $default
						: $get_cache_option;
				}
				
				$option_value = get_option($this->prefix . $option_name);
				
				if( $this->isCacheable($option_value) ) {
					$this->setCacheOption($option_name, $this->normalizeValue($option_value));
				}
				
				return $option_value === false
					? $default
					: $this->normalizeValue($option_value);
			}
			
			/**
			 * Обновляет опцию в базе данных и в кеше, кеш обновляется только кешируемых опций.
			 * Не кешируемые опции это массивы, сериализованные массивы, строки больше 150 символов
			 *
			 * @param string $option_name
			 * @param string $value
			 * @return void
			 */
			public function updateOption($option_name, $value)
			{
				if( $this->isCacheable($value) ) {
					$this->setCacheOption($option_name, $this->normalizeValue($value));
				} else {
					if( isset(self::$_opt_buffer[$this->prefix][$option_name]) ) {
						unset(self::$_opt_buffer[$this->prefix][$option_name]);

						$this->updateOption('cache_options', self::$_opt_buffer[$this->prefix]);
					}
				}

				update_option($this->prefix . $option_name, $value);
			}
			
			/**
			 * Пакетное обновление опций, также метод пакетно обновляет кеш в базе данных
			 * и в буфере опций, кеш обновляется только кешируемых опций. Не кешируемые опции это массивы,
			 * сериализованные массивы, строки больше 150 символов
			 *
			 * @param array $options
			 * @return bool
			 */
			public function updateOptions($options)
			{
				if( empty($options) ) {
					return false;
				}
				
				foreach((array)$options as $option_name => $option_value) {
					$this->updateOption($option_name, $option_value);
				}
				
				$this->updateCacheOptions($options);
				
				return true;
			}
			
			/**
			 * Удаляет опцию из базы данных, если опция есть в кеше,
			 * индивидуально удаляет опцию из кеша.
			 *
			 * @param string $option_name
			 * @return void
			 */
			public function deleteOption($option_name)
			{
				if( isset(self::$_opt_buffer[$this->prefix][$option_name]) ) {
					unset(self::$_opt_buffer[$this->prefix][$option_name]);
					
					$this->updateOption('cache_options', self::$_opt_buffer[$this->prefix]);
				}

				delete_option($this->prefix . $option_name . '_is_active');
				delete_option($this->prefix . $option_name);
			}
			
			/**
			 * Пакетное удаление опций, после удаления опции происходит очистка кеша и буфера опций
			 *
			 * @param array $options
			 * @return void
			 */
			public function deleteOptions($options)
			{
				if( !empty($options) ) {
					foreach((array)$options as $option_name) {
						if( isset(self::$_opt_buffer[$this->prefix]) ) {
							unset(self::$_opt_buffer[$this->prefix]);
						}

						delete_option($this->prefix . $option_name . '_is_active');
						delete_option($this->prefix . $option_name);
					}

					$this->updateOption('cache_options', self::$_opt_buffer[$this->prefix]);
				}
			}
			
			/**
			 * Сбрасывает кеш опций, удаляет кеш из базы данных и буфер опций
			 *
			 * @return bool
			 */
			public function flushOptionsCache()
			{
				if( isset(self::$_opt_buffer[$this->prefix]) ) {
					unset(self::$_opt_buffer[$this->prefix]);
					self::$_opt_buffer[$this->prefix] = array();
				}
				
				$this->deleteOption('cache_options');
			}
			
			/**
			 * Получает все опций текущего плагина
			 *
			 * @param bool $is_cacheable - только кешируемые опции, кешируемые опции это массивы,
			 * сериализованные массивы, строки больше 150 символов
			 * @return array
			 */
			protected function getAllPluginOptions($is_cacheable = true)
			{
				global $wpdb;
				$options = array();
				
				$request = $wpdb->get_results($wpdb->prepare("
					SELECT option_name, option_value
					FROM {$wpdb->prefix}options
					WHERE option_name
					LIKE '%s'", $this->prefix . "%"));
				
				if( !empty($request) ) {
					foreach((array)$request as $option) {
						if( $is_cacheable && !$this->isCacheable($option->option_value) ) {
							continue;
						}
						$options[$option->option_name] = $this->normalizeValue($option->option_value);
					}
				}
				
				return $options;
			}
			
			
			/**
			 * Записывает только одну опцию в кеш базы данных и в буфер
			 *
			 * @param string $option_name
			 * @param string $value
			 * @return void
			 * @throws Exception
			 */
			protected function setCacheOption($option_name, $value)
			{
				$this->setBufferOption($option_name, $value);
				
				if( !empty(self::$_opt_buffer[$this->prefix]) ) {
					$this->updateOption('cache_options', self::$_opt_buffer[$this->prefix]);
				}
			}
			
			/**
			 * Пакетное обновление опций в кеше и буфер опций,
			 * все записываемые опции приводятся к регламентированному типу данных
			 *
			 * @param array $options
			 * @return bool
			 * @throws Exception
			 */
			protected function updateCacheOptions($options)
			{
				foreach((array)$options as $option_name => $value) {
					$option_name = str_replace($this->prefix, '', $option_name);
					$this->setBufferOption($option_name, $this->normalizeValue($value));
				}
				
				if( !empty(self::$_opt_buffer[$this->prefix]) ) {
					$this->updateOption('cache_options', self::$_opt_buffer[$this->prefix]);
				}
				
				return false;
			}
			
			/**
			 * Получает опцию из кеша или буфера, если опция не найдена и буфер пуст,
			 * то заполняет буфер кеширумыми опциями, которые уже записаны в базу данных.
			 *
			 * @param string $option_name
			 * @return null
			 * @throws Exception
			 */
			protected function getOptionFromCache($option_name)
			{
				if( empty(self::$_opt_buffer[$this->prefix]) ) {
					$all_options = $this->getAllPluginOptions();
					
					if( !empty($all_options) ) {
						$this->updateCacheOptions($all_options);
					}
				}
				
				$buffer_option = $this->getBufferOption($option_name);
				
				if( !is_null($buffer_option) ) {
					return $buffer_option;
				}
				
				return null;
			}
			
			/**
			 * Получает опцию из буфера опций
			 *
			 * @param string $option_name
			 * @return null|mixed
			 */
			private function getBufferOption($option_name)
			{
				if( isset(self::$_opt_buffer[$this->prefix][$option_name]) ) {
					return self::$_opt_buffer[$this->prefix][$option_name];
				}
				
				return null;
			}
			
			/**
			 * Записывает опции в буфер опций, если опция уже есть в буфере и их значения не совпадают,
			 * то новое значение перезаписывает старое
			 *
			 * @param string $option_name
			 * @param string $option_value
			 */
			private function setBufferOption($option_name, $option_value)
			{
				if( !isset(self::$_opt_buffer[$this->prefix][$option_name]) ) {
					self::$_opt_buffer[$this->prefix][$option_name] = $option_value;
				} else {
					if( self::$_opt_buffer[$this->prefix][$option_name] !== $option_value ) {
						self::$_opt_buffer[$this->prefix][$option_name] = $option_value;
					}
				}
			}
			
			/**
			 * Возвращает название опции в пространстве имен плагина
			 *
			 * @param string $option_name
			 * @return null|string
			 */
			public function getOptionName($option_name)
			{
				$option_name = trim(rtrim($option_name));
				if( empty($option_name) || !is_string($option_name) ) {
					return null;
				}
				
				return $this->prefix . $option_name;
			}
			
			/**
			 * Проверяет является ли опция кешируемой. Кешируемые опции это массивы,
			 * сериализованные массивы, строки больше 150 символов.
			 *
			 * @param string $data - переданое значение опции
			 * @return bool
			 */
			public function isCacheable($data)
			{
				if( (is_string($data) && (is_serialized($data) || strlen($data) > 150)) || is_array($data) ) {
					return false;
				}
				
				return true;
			}
			
			/**
			 * Приведение значений опций к строгому типу данных
			 *
			 * @param $string
			 * @return bool|int
			 */
			public function normalizeValue($string)
			{
				if( is_numeric($string) ) {
					$number = intval($string);
					
					if( strlen($number) != strlen($string) ) {
						throw new Exception('Error converting data type to a number.');
					}
					
					return $number;
				} else if( $string === 'false' ) {
					return false;
				} else if( $string === 'true' ) {
					return true;
				}
				
				return $string;
			}
		}
	}