<?php
	/**
	 * Impressive page themplate class
	 *
	 * @author Alex Kovalev <alex.kovalevv@gmail.com>
	 * @copyright (c) 2018, Webcraftic Ltd
	 *
	 * @package factory-pages
	 * @since 1.0.0
	 */
	
	// Exit if accessed directly
	if( !defined('ABSPATH') ) {
		exit;
	}
	
	if( !class_exists('Wbcr_FactoryPages401_ImpressiveThemplate') ) {
		/**
		 * Class Wbcr_FactoryPages401_ImpressiveThemplate
		 *
		 * @method string getInfoWidget() - get widget content information
		 * @method string getRatingWidget(array $args = array()) - get widget content rating
		 * @method string getDonateWidget() - get widget content donate
		 */
		abstract class Wbcr_FactoryPages401_ImpressiveThemplate extends Wbcr_FactoryPages401_AdminPage {
			
			//public $menu_target = 'options-general.php';
			
			/**
			 * @var bool
			 */
			public $internal = true;
			
			/**
			 * @var string
			 */
			public $type = 'options';
			
			/**
			 * @var string
			 */
			public $page_parent_page;
			
			/**
			 * @var string
			 */
			public $page_menu_dashicon;
			
			/**
			 * @var int
			 */
			public $page_menu_position = 10;
			
			/**
			 * @var bool
			 */
			public $show_page_title = true;
			
			/**
			 * @var bool
			 */
			public $show_right_sidebar_in_options = false;
			
			/**
			 * @var bool
			 */
			public $show_bottom_sidebar = true;
			
			/**
			 * @param Wbcr_Factory400_Plugin $plugin
			 */
			public function __construct(Wbcr_Factory400_Plugin $plugin)
			{
				$this->menuIcon = FACTORY_PAGES_401_URL . '/templates/assets/img/webcraftic-plugin-icon.png';
				
				parent::__construct($plugin);
				
				global $factory_impressive_page_menu;
				
				$dashicon = (!empty($this->page_menu_dashicon))
					? ' ' . $this->page_menu_dashicon
					: '';
				
				$this->title_plugin_action_link = __('Settings', 'wbcr_factory_pages_401');
				
				//if( $this->type == 'options' ) {
				//$this->show_right_sidebar_in_options = true;
				//$this->show_bottom_sidebar = false;
				//}
				
				$factory_impressive_page_menu[$plugin->getPluginName()][$this->getResultId()] = array(
					'type' => $this->type, // page, options
					'url' => $this->getBaseUrl(),
					'title' => '<span class="dashicons' . $dashicon . '"></span> ' . $this->getMenuTitle(),
					'position' => $this->page_menu_position,
					'parent' => $this->page_parent_page
				);
			}
			
			public function __call($name, $arguments)
			{
				if( substr($name, 0, 3) == 'get' ) {
					$called_method_name = 'show' . substr($name, 3);
					if( method_exists($this, $called_method_name) ) {
						ob_start();
						
						$this->$called_method_name($arguments);
						$content = ob_get_contents();
						ob_end_clean();
						
						return $content;
					}
				}
				
				return null;
			}
			
			/**
			 * Requests assets (js and css) for the page.
			 *
			 * @see FactoryPages401_AdminPage
			 *
			 * @since 1.0.0
			 * @return void
			 */
			public function assets($scripts, $styles)
			{
				
				$this->scripts->request('jquery');
				
				$this->scripts->request(array(
					'control.checkbox',
					'control.dropdown',
					'bootstrap.tooltip'
				), 'bootstrap');
				
				$this->styles->request(array(
					'bootstrap.core',
					'bootstrap.form-group',
					'bootstrap.separator',
					'control.dropdown',
					'control.checkbox'
				), 'bootstrap');
				
				$this->styles->add(FACTORY_PAGES_401_URL . '/templates/assets/css/impressive.page.template.css');
				//$this->styles->add('https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
			}
			
			/**
			 * @return string
			 */
			public function getMenuTitle()
			{
				return $this->menu_title;
			}

			/**
			 * @return string
			 */
			public function getPageTitle()
			{
				return $this->getMenuTitle();
			}

			/**
			 * @return string
			 */
			public function getPluginTitle()
			{
				return $this->plugin->getPluginTitle();
			}

			/**
			 * @return string
			 */
			public function getPageUrl()
			{
				return $this->getBaseUrl();
			}
			
			/**
			 * Get options with namespace
			 * @param $option_name
			 * @param bool $default
			 * @return mixed|void
			 */
			public function getOption($option_name, $default = false)
			{
				return $this->plugin->getOption($option_name, $default);
			}

			/**
			 * @return string
			 */
			protected function getBaseUrl()
			{
				$result_id = $this->getResultId();
				
				if( $this->menu_target ) {
					return add_query_arg(array('page' => $result_id), admin_url($this->menu_target));
				} else {
					return add_query_arg(array('page' => $result_id), admin_url('admin.php'));
				}
			}
			
			/**
			 * Shows a page or options
			 *
			 * @sinve 1.0.0
			 * @return void
			 */
			public function indexAction()
			{
				global $factory_impressive_page_menu;
				
				if( 'options' === $factory_impressive_page_menu[$this->plugin->getPluginName()][$this->getResultId()]['type'] ) {
					$this->showOptions();
				} else {
					$this->showPage();
				}
			}

			/**
			 * Flush cache and rules
			 *
			 * @sinve 4.0.0
			 * @return void
			 */
			public function flushCacheAndRulesAction()
			{
				check_admin_referer('wbcr_factory_' . $this->getResultId() . '_flush_action');

				// todo: test cache control
				if( function_exists('w3tc_pgcache_flush') ) {
					w3tc_pgcache_flush();
				} elseif( function_exists('wp_cache_clear_cache') ) {
					wp_cache_clear_cache();
				} elseif( function_exists('rocket_clean_files') ) {
					rocket_clean_files(esc_url($_SERVER['HTTP_REFERER']));
				} else if( isset($GLOBALS['wp_fastest_cache']) && method_exists($GLOBALS['wp_fastest_cache'], 'deleteCache') ) {
					$GLOBALS['wp_fastest_cache']->deleteCache();
				}

				wbcr_factory_400_do_action_deprecated('wbcr_factory_400_imppage_flush_cache', array(
					$this->plugin->getPluginName(),
					$this->getResultId()
				), '4.0.1', 'wbcr_factory_400_imppage_after_form_save');

				/**
				 * @since 4.0.1
				 */
				do_action('wbcr_factory_400_imppage_after_form_save', $this->plugin, $this);

				$this->afterFormSave();

				$this->redirectToAction('index', apply_filters('wbcr_factory_400_imppage_after_form_save_redirect_args', array(
					$this->plugin->getPluginName() . '_saved' => 1
				)));
			}


			/**
			 * Вызывается всегда при загрузке страницы, перед опциями формы с типом страницы options
			 */
			protected function warningNotice()
			{
				/*if( WP_CACHE ) {
					$this->printWarningNotice(__("It seems that a caching/performance plugin is active on this site. Please manually invalidate that plugin's cache after making any changes to the settings below.", 'wbcr_factory_pages_401'));
				}*/
				// Метод предназначен для вызова в дочернем классе
			}

			/**
			 * Вызывается всегда при загрузке страницы, перед опциями формы с типом страницы options
			 *
			 * @since 4.0.0
			 * @param array $notices
			 * @return array
			 */
			protected function getActionNotices($notices)
			{
				// Метод предназначен для вызова в дочернем классе
				return $notices;
			}

			/**
			 * Вызывается перед сохранением опций формы
			 *
			 * @since 4.0.0
			 * @return void
			 */
			protected function beforeFormSave()
			{
				// Метод предназначен для вызова в дочернем классе
			}

			/**
			 * Вызывается после сохранением опций формы, когда выполнен сброс кеша и совершен редирект
			 *
			 * @since 4.0.0
			 * @return void
			 */
			protected function afterFormSave()
			{
				// Метод предназначен для вызова в дочернем классе
			}

			/**
			 * Вызывается в процессе выполнения сохранения, но после сохранения всех опций
			 *
			 * @since 4.0.0
			 * @return void
			 */
			protected function formSaved()
			{
				// Метод предназначен для вызова в дочернем классе
			}

			public function printWarningNotice($message)
			{
				echo '<div class="alert alert-warning wbcr-factory-warning-notice"><p><span class="dashicons dashicons-warning"></span> ' . $message . '</p></div>';
			}

			public function printErrorNotice($message)
			{
				echo '<div class="alert alert-danger wbcr-factory-warning-notice"><p><span class="dashicons dashicons-dismiss"></span> ' . $message . '</p></div>';
			}

			public function printSuccessNotice($message)
			{
				echo '<div class="alert alert-success wbcr-factory-warning-notice"><p><span class="dashicons dashicons-plus"></span> ' . $message . '</p></div>';
			}

			protected function printAllNotices()
			{
				$this->warningNotice();
				$this->showActionsNotice();

				/**
				 * @since 4.0.1
				 */
				do_action('wbcr_factory_pages_401_imppage_print_all_notices', $this->plugin, $this);
			}

			private function showActionsNotice()
			{
				$notices = array(
					array(
						'conditions' => array(
							$this->plugin->getPluginName() . '_saved' => '1'
						),
						'type' => 'success',
						'message' => __('The settings have been updated successfully!', 'wbcr_factory_pages_401') . (WP_CACHE
								? '<br>' . __("It seems that a caching/performance plugin is active on this site. Please manually invalidate that plugin's cache after making any changes to the settings below.", 'wbcr_factory_pages_401')
								: '')
					)
				);
				
				$notices = apply_filters('wbcr_factory_pages_401_imppage_actions_notice', $notices, $this->plugin, $this->id);
				$notices = $this->getActionNotices($notices);
				
				foreach($notices as $key => $notice) {
					$show_message = true;
					
					if( isset($notice['conditions']) && !empty($notice['conditions']) ) {
						foreach($notice['conditions'] as $condition_name => $value) {
							if( !isset($_REQUEST[$condition_name]) || $_REQUEST[$condition_name] != $value ) {
								$show_message = false;
							}
						}
					}
					if( !$show_message ) {
						continue;
					}
					
					$notice_type = isset($notice['type'])
						? $notice['type']
						: 'success';

					switch( $notice_type ) {
						case 'success':
							$this->printSuccessNotice($notice['message']);
							break;
						case 'danger':
							$this->printErrorNotice($notice['message']);
							break;
						default:
							$this->printWarningNotice($notice['message']);
							break;
					}
				}
			}
			
			protected function showPageMenu()
			{
				global $factory_impressive_page_menu;
				
				$page_menu = $factory_impressive_page_menu[$this->plugin->getPluginName()];
				$self_page_id = $this->getResultId();
				$current_page = isset($page_menu[$self_page_id])
					? $page_menu[$self_page_id]
					: null;
				
				$parent_page_id = !empty($current_page['parent'])
					? $this->getResultId($current_page['parent'])
					: null;
				
				uasort($page_menu, array($this, 'pageMenuSort'));
				?>
				<ul>
					<?php foreach($page_menu as $page_screen => $page): ?>
						<?php
						if( !empty($page['parent']) ) {
							continue;
						}
						$active_tab = '';
						if( $page_screen == $self_page_id || $page_screen == $parent_page_id ) {
							$active_tab = ' wbcr-factory-active-tab';
						}
						?>
						<li class="wbcr-factory-nav-tab<?= $active_tab ?>">
							<a href="<?php echo $page['url'] ?>" id="<?= $page_screen ?>-tab">
								<?php echo $page['title'] ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php
			}

			/**
			 * @param int $a
			 * @param int $b
			 * @return bool
			 */
			protected function pageMenuSort($a, $b)
			{
				return $a['position'] < $b['position'];
			}
			
			protected function showPageSubMenu()
			{
				global $factory_impressive_page_menu;
				$self_page_id = $this->getResultId();
				$page_menu = $factory_impressive_page_menu[$this->plugin->getPluginName()];
				$current_page = isset($page_menu[$self_page_id])
					? $page_menu[$self_page_id]
					: null;
				
				$page_submenu = array();
				foreach($page_menu as $page_screen => $page) {
					if( !empty($page['parent']) ) {
						$page_parent_id = $this->getResultId($page['parent']);
						
						if( isset($page_menu[$page_parent_id]) ) {
							$page['title'] = strip_tags($page['title']);
							$page_submenu[$page_parent_id][$page_screen] = $page;
						}
					}
				}
				
				if( empty($page_submenu) ) {
					return;
				}
				
				$get_menu_id = null;
				$has_parent = !empty($current_page) && !empty($current_page['parent']);
				$parent_page_id = $has_parent
					? $this->getResultId($current_page['parent'])
					: null;
				
				if( ($has_parent && isset($page_submenu[$parent_page_id])) ) {
					$get_menu_id = $parent_page_id;
				} else if( !$has_parent && isset($page_submenu[$self_page_id]) ) {
					$get_menu_id = $self_page_id;
				}
				
				if( !isset($page_submenu[$get_menu_id]) ) {
					return;
				}
				
				$unshift = array();
				if( isset($page_menu[$get_menu_id]) ) {
					$page_menu[$get_menu_id]['title'] = strip_tags($page_menu[$get_menu_id]['title']);
					
					$unshift[$get_menu_id][$get_menu_id] = $page_menu[$get_menu_id];
					$page_submenu[$get_menu_id] = $unshift[$get_menu_id] + $page_submenu[$get_menu_id];
				}
				
				?>
				<h2 class="nav-tab-wrapper wp-clearfix">
					<?php foreach((array)$page_submenu[$get_menu_id] as $page_screen => $page): ?>
						<?php
						$active_tab = '';
						if( $page_screen == $this->getResultId() ) {
							$active_tab = ' nav-tab-active';
						}
						?>
						<a href="<?php echo $page['url'] ?>" id="<?= esc_attr($page_screen) ?>-tab" class="nav-tab<?= esc_attr($active_tab) ?>">
							<?php echo $page['title'] ?>
						</a>
					<?php endforeach; ?>
				</h2>
			<?php
			}
			
			protected function showHeader()
			{
				?>
				<div class="wbcr-factory-page-header">
					<div class="wbcr-factory-header-logo"><?= $this->getPluginTitle(); ?>
						<span class="version"><?= $this->plugin->getPluginVersion() ?> </span>
						<?php if( $this->show_page_title ): ?><span class="dash">—</span><?php endif; ?>
					</div>
					<?php if( $this->show_page_title ): ?>
						<div class="wbcr-factory-header-title">
							<h2><?php _e('Page') ?>: <?= $this->getPageTitle() ?></h2>
						</div>
					<?php endif; ?>
					<?php if( $this->type == 'options' ): ?>
						<div class="wbcr-factory-control">
						<input name="<?= $this->plugin->getPluginName() ?>_save_action" class="wbcr-factory-type-save" type="submit" value="<?php _e('Save settings', 'wbcr_factory_pages_401'); ?>">
						<?php wp_nonce_field('wbcr_factory_' . $this->getResultId() . '_save_action'); ?>
						</div><?php endif; ?>
				</div>
			<?php
			}
			
			protected function showRightSidebar()
			{
				$widgets = $this->getPageWidgets('right');
				
				if( empty($widgets) ) {
					return;
				}
				
				foreach($widgets as $widget_content):
					echo $widget_content;
				endforeach;
			}
			
			protected function showBottomSidebar()
			{
				$widgets = $this->getPageWidgets('bottom');
				
				if( empty($widgets) ) {
					return;
				}
				?>
				<div class="row">
				<div class="wbcr-factory-top-sidebar">
					<?php foreach($widgets as $widget_content): ?>
						<div class="col-sm-4">
							<?= $widget_content ?>
						</div>
					<?php endforeach; ?>
				</div>
			<?php
			}

			protected function getPageWidgets($position = 'bottom')
			{
				wbcr_factory_400_apply_filters_deprecated('wbcr_factory_pages_401_imppage_right_sidebar_widgets', array(
					array(
						'info_widget' => $this->getInfoWidget(),
						'rating_widget' => $this->getRatingWidget(),
						'donate_widget' => $this->getDonateWidget()
					),
					$this->getResultId()
				), '4.0.1', 'wbcr_factory_pages_401_imppage_get_widgets');

				wbcr_factory_400_apply_filters_deprecated('wbcr_factory_pages_401_imppage_bottom_sidebar_widgets', array(
					array(
						'info_widget' => $this->getInfoWidget(),
						'rating_widget' => $this->getRatingWidget(),
						'donate_widget' => $this->getDonateWidget()
					),
					$this->getResultId()
				), '4.0.1', 'wbcr_factory_pages_401_imppage_get_widgets');

				return apply_filters('wbcr_factory_pages_401_imppage_get_widgets', array(
					'info_widget' => $this->getInfoWidget(),
					'rating_widget' => $this->getRatingWidget(),
					'donate_widget' => $this->getDonateWidget()
				), $position, $this->plugin, $this);
			}
			
			/**
			 *
			 */
			protected function showOptions()
			{
				
				global $factory_impressive_page_menu;

				$form = new Wbcr_FactoryForms400_Form(array(
					'scope' => rtrim($this->plugin->getPrefix(), '_'),
					'name' => $this->getResultId() . "-options"
				), $this->plugin);
				
				$form->setProvider(new Wbcr_FactoryForms400_OptionsValueProvider($this->plugin));
				
				$options = $this->getOptions();
				
				if( isset($options[0]) && isset($options[0]['items']) && is_array($options[0]['items']) ) {
					
					/*array_unshift($options[0]['items'], array(
						'type' => 'html',
						'html' => array($this, 'printAllNotices')
					));*/
					
					foreach($options[0]['items'] as $key => $value) {
						
						if( $value['type'] == 'div' ) {
							if( isset($options[0]['items'][$key]['items']) && !empty($options[0]['items'][$key]['items']) ) {
								foreach($options[0]['items'][$key]['items'] as $group_key => $group_value) {
									$options[0]['items'][$key]['items'][$group_key]['layout']['column-left'] = '6';
									$options[0]['items'][$key]['items'][$group_key]['layout']['column-right'] = '6';
								}
								
								continue;
							}
						}
						
						if( in_array($value['type'], array(
							'checkbox',
							'textarea',
							'integer',
							'textbox',
							'dropdown',
							'list'
						)) ) {
							$options[0]['items'][$key]['layout']['column-left'] = '6';
							$options[0]['items'][$key]['layout']['column-right'] = '6';
						}
					}
				}
				
				$form->add($options);
				
				if( isset($_POST[$this->plugin->getPluginName() . '_save_action']) ) {

					check_admin_referer('wbcr_factory_' . $this->getResultId() . '_save_action');

					if( !current_user_can('administrator') && !current_user_can($this->capabilitiy) ) {
						wp_die(__('You do not have permission to edit page.', 'wbcr_factory_pages_401'));
						exit;
					}

					wbcr_factory_400_do_action_deprecated('wbcr_factory_400_imppage_before_save', array(
						$form,
						$this->plugin->getPluginName()
					), '4.0.1', 'wbcr_factory_400_imppage_before_form_save');

					do_action('wbcr_factory_400_imppage_before_form_save', $form, $this->plugin, $this);

					$this->beforeFormSave();
					
					$form->save();

					wbcr_factory_400_do_action_deprecated('wbcr_factory_400_imppage_saved', array(
						$form,
						$this->plugin->getPluginName()
					), '4.0.1', 'wbcr_factory_400_imppage_form_saved');
					
					do_action('wbcr_factory_400_imppage_form_saved', $form, $this->plugin, $this);

					$this->formSaved();
					
					$this->redirectToAction('flush-cache-and-rules', array(
						'_wpnonce' => wp_create_nonce('wbcr_factory_' . $this->getResultId() . '_flush_action')
					));
				}
				
				?>
				<div id="WBCR" class="wrap">
					<div class="wbcr-factory-pages-401-impressive-page-template factory-bootstrap-400 factory-fontawesome-000">
						<div class="wbcr-factory-options wbcr-factory-options-<?= esc_attr($this->id) ?>">
							<div class="wbcr-factory-left-navigation-bar">
								<?php $this->showPageMenu() ?>
							</div>
							<?php
								$min_height = 0;
								foreach($factory_impressive_page_menu[$this->plugin->getPluginName()] as $page) {
									if( !isset($page['parent']) || empty($page['parent']) ) {
										$min_height += 61;
									}
								}
							?>
							<div class="wbcr-factory-page-inner-wrap">
								<div class="wbcr-factory-content-section<?php if( !$this->show_right_sidebar_in_options ): echo ' wbcr-fullwidth'; endif ?>">
									<?php $this->showPageSubMenu() ?>
									<div class="wbcr-factory-content" style="min-height:<?= $min_height ?>px">
										<form method="post" class="form-horizontal">
											<?php $this->showHeader(); ?>
											<?php $this->printAllNotices(); ?>
											<?php $form->html(); ?>
										</form>
									</div>
								</div>
								<?php if( $this->show_right_sidebar_in_options ): ?>
									<div class="wbcr-factory-right-sidebar-section">
										<?php $this->showRightSidebar(); ?>
									</div>
								<?php endif; ?>
							</div>
						</div>
						
						<?php
							if( $this->show_bottom_sidebar ) {
								$this->showBottomSidebar();
							}
						?>
						
						<div class="clearfix"></div>
					</div>
				</div>
				</div>
			<?php
			}
			
			protected function showPage()
			{
				global $factory_impressive_page_menu;
				?>
				<div id="WBCR" class="wrap">
					<div class="wbcr-factory-pages-401-impressive-page-template factory-bootstrap-400 factory-fontawesome-000">
						<div class="wbcr-factory-page wbcr-factory-page-<?= $this->id ?>">
							<?php $this->showHeader(); ?>
							
							<div class="wbcr-factory-left-navigation-bar">
								<?php $this->showPageMenu() ?>
							</div>
							<?php
								$min_height = 0;
								foreach($factory_impressive_page_menu[$this->plugin->getPluginName()] as $page) {
									if( !isset($page['parent']) || empty($page['parent']) ) {
										$min_height += 61;
									}
								}
							?>
							<div class="wbcr-factory-page-inner-wrap">
								<div class="wbcr-factory-content-section<?php if( !$this->show_right_sidebar_in_options ): echo ' wbcr-fullwidth'; endif ?>">
									<?php $this->showPageSubMenu() ?>
									<div class="wbcr-factory-content" style="min-height:<?= $min_height ?>px">
										<?php $this->printAllNotices(); ?>
										<?php $this->showPageContent() ?>
									</div>
								</div>
							</div>
						</div>
						<div class="clearfix"></div>
						<?php $this->showBottomSidebar(); ?>
					</div>
				</div>
			<?php
			}
			
			/**
			 * @return void
			 */
			public function showPageContent()
			{
				// используется в классе потомке
			}
			
			
			public function showInfoWidget()
			{
				?>
				<div class="wbcr-factory-sidebar-widget">
					<ul>
						<li>
						<span class="wbcr-factory-hint-icon-simple wbcr-factory-simple-red">
							<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAkAAAAJCAQAAABKmM6bAAAAUUlEQVQIHU3BsQ1AQABA0X/komIrnQHYwyhqQ1hBo9KZRKL9CBfeAwy2ri42JA4mPQ9rJ6OVt0BisFM3Po7qbEliru7m/FkY+TN64ZVxEzh4ndrMN7+Z+jXCAAAAAElFTkSuQmCC" alt=""/>
						</span>
							- <?php _e('A neutral setting that can not harm your site, but you must be sure that you need to use it.', 'wbcr_factory_pages_401'); ?>
						</li>
						<li>
						<span class="wbcr-factory-hint-icon-simple wbcr-factory-simple-grey">
							<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAkAAAAJCAQAAABKmM6bAAAAUUlEQVQIHU3BsQ1AQABA0X/komIrnQHYwyhqQ1hBo9KZRKL9CBfeAwy2ri42JA4mPQ9rJ6OVt0BisFM3Po7qbEliru7m/FkY+TN64ZVxEzh4ndrMN7+Z+jXCAAAAAElFTkSuQmCC" alt=""/>
						</span>
							- <?php _e('When set this option, you must be careful. Plugins and themes may depend on this function. You must be sure that you can disable this feature for the site.', 'wbcr_factory_pages_401'); ?>
						</li>
						<li>
						<span class="wbcr-factory-hint-icon-simple wbcr-factory-simple-green">
							<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAkAAAAJCAQAAABKmM6bAAAAUUlEQVQIHU3BsQ1AQABA0X/komIrnQHYwyhqQ1hBo9KZRKL9CBfeAwy2ri42JA4mPQ9rJ6OVt0BisFM3Po7qbEliru7m/FkY+TN64ZVxEzh4ndrMN7+Z+jXCAAAAAElFTkSuQmCC" alt=""/>
						</span>
							- <?php _e('Absolutely safe setting, We recommend to use.', 'wbcr_factory_pages_401'); ?>
						</li>
					</ul>
					----------<br>
					
					<p><?php _e('Hover to the icon to get help for the feature you selected.', 'wbcr_factory_pages_401'); ?></p>
				</div>
			<?php
			}
			
			public function showRatingWidget(array $args)
			{
				if( !isset($args[0]) || empty($args[0]) ) {
					$page_url = "https://goo.gl/tETE2X";
				} else {
					$page_url = $args[0];
				}
				
				$page_url = apply_filters('wbcr_factory_pages_401_imppage_rating_widget_url', $page_url, $this->plugin->getPluginName(), $this->getResultId());
				
				?>
				<div class="wbcr-factory-sidebar-widget">
					<p>
						<strong><?php _e('Do you want the plugin to improved and update?', 'wbcr_factory_pages_401'); ?></strong>
					</p>
					
					<p><?php _e('Help the author, leave a review on wordpress.org. Thanks to feedback, I will know that the plugin is really useful to you and is needed.', 'wbcr_factory_pages_401'); ?></p>
					
					<p><?php _e('And also write your ideas on how to extend or improve the plugin.', 'wbcr_factory_pages_401'); ?></p>
					
					<p>
						<i class="wbcr-factory-icon-5stars"></i>
						<a href="<?= $page_url ?>" title="Go rate us" target="_blank">
							<strong><?php _e('Go rate us and push ideas', 'wbcr_factory_pages_401'); ?></strong>
						</a>
					</p>
				</div>
			<?php
			}
			
			public function showDonateWidget()
			{
				?>
				<div class="wbcr-factory-sidebar-widget">
					<p>
						<strong><?php _e('Donation for plugin development', 'wbcr_factory_pages_401'); ?></strong>
					</p>
					
					<?php if( get_locale() !== 'ru_RU' ): ?>
						<form id="wbcr-factory-paypal-donation-form" action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
							<input type="hidden" name="cmd" value="_s-xclick">
							<input type="hidden" name="hosted_button_id" value="VDX7JNTQPNPFW">
							
							<div class="wbcr-factory-donation-price">5$</div>
							<input type="image" src="<?= FACTORY_PAGES_401_URL ?>/templates/assets/img/paypal-donate.png" border="0" name="submit" alt="PayPal – The safer, easier way to pay online!">
						</form>
					<?php else: ?>
						<iframe frameborder="0" allowtransparency="true" scrolling="no" src="https://money.yandex.ru/embed/donate.xml?account=410011242846510&quickpay=donate&payment-type-choice=on&mobile-payment-type-choice=on&default-sum=300&targets=%D0%9D%D0%B0+%D0%BF%D0%BE%D0%B4%D0%B4%D0%B5%D1%80%D0%B6%D0%BA%D1%83+%D0%BF%D0%BB%D0%B0%D0%B3%D0%B8%D0%BD%D0%B0+%D0%B8+%D1%80%D0%B0%D0%B7%D1%80%D0%B0%D0%B1%D0%BE%D1%82%D0%BA%D1%83+%D0%BD%D0%BE%D0%B2%D1%8B%D1%85+%D1%84%D1%83%D0%BD%D0%BA%D1%86%D0%B8%D0%B9.+&target-visibility=on&project-name=Webcraftic&project-site=&button-text=05&comment=on&hint=%D0%9A%D0%B0%D0%BA%D1%83%D1%8E+%D1%84%D1%83%D0%BD%D0%BA%D1%86%D0%B8%D1%8E+%D0%BD%D1%83%D0%B6%D0%BD%D0%BE+%D0%B4%D0%BE%D0%B1%D0%B0%D0%B2%D0%B8%D1%82%D1%8C+%D0%B2+%D0%BF%D0%BB%D0%B0%D0%B3%D0%B8%D0%BD%3F&mail=on&successURL=" width="508" height="187"></iframe>
					<?php endif; ?>
				</div>
			<?php
			}
			
			/**
			 * Shows the html block with a confirmation dialog.
			 *
			 * @sinve 1.0.0
			 * @return void
			 */
			public function confirmPageTemplate($data)
			{
				?>
				<div id="WBCR" class="wrap">
					<div class="wbcr-factory-pages-401-impressive-page-template factory-bootstrap-400 factory-fontawesome-000">
						<div id="wbcr-factory-confirm-dialog">
							<h2><?php echo $data['title'] ?></h2>
							
							<p class="wbcr-factory-confirm-description"><?php echo $data['description'] ?></p>
							
							<?php if( isset($data['hint']) ): ?>
								<p class="wbcr-factory-confirm-hint"><?php echo $data['hint'] ?></p>
							<?php endif; ?>
							
							<div class='wbcr-factory-confirm-actions'>
								<?php foreach($data['actions'] as $action) { ?>
									<a href='<?php echo $action['url'] ?>' class='<?php echo $action['class'] ?>'>
										<?php echo $action['title'] ?>
									</a>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>
			<?php
			}
		}
	}

