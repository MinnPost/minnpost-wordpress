<?php
	
	/**
	 * This class configures hide admin notices
	 * @author Webcraftic <wordpress.webraftic@gmail.com>
	 * @copyright (c) 2017 Webraftic Ltd
	 * @version 1.0
	 */

	// Exit if accessed directly
	if( !defined('ABSPATH') ) {
		exit;
	}

	class WDN_ConfigHideNotices extends Wbcr_FactoryClearfy200_Configurate {

		public function registerActionsAndFilters()
		{
			if( is_admin() ) {
				$hide_notices_type = $this->getOption('hide_admin_notices');
				if( $hide_notices_type != 'not_hide' ) {
					add_action('admin_print_scripts', array($this, 'catchNotices'), 999);

					if( empty($hide_notices_type) || $hide_notices_type == 'only_selected' ) {
						add_action('admin_head', array($this, 'printNotices'), 999);
					}

					if( !empty($hide_notices_type) && $this->getOption('show_notices_in_adminbar') ) {
						add_action('admin_bar_menu', array($this, 'notificationsPanel'), 999);
						add_action('admin_enqueue_scripts', array($this, 'notificationsPanelStyles'));
					}

					add_action('admin_head', array($this, 'printNonce'), 999);
				}
			}
		}

		public function printNotices()
		{
			add_action('admin_notices', array($this, 'noticesCollection'));
		}

		public function printNonce()
		{
			?>
			<!-- Disable admin notices plugin (Clearfy tools) -->
			<script>
				var wbcr_dan_ajax_restore_nonce = "<?=wp_create_nonce($this->plugin->getPluginName() . '_ajax_restore_notice_nonce')?>";
				var wbcr_dan_ajax_hide_notice_nonce = "<?=wp_create_nonce($this->plugin->getPluginName() . '_ajax_hide_notices_nonce')?>";
			</script>
		<?php
		}


		public function notificationsPanelStyles()
		{
			wp_enqueue_style('wbcr-notification-panel-styles', WDN_PLUGIN_URL . '/admin/assets/css/notifications-panel.css', array(), $this->plugin->getPluginVersion());
			wp_enqueue_script('wbcr-notification-panel-scripts', WDN_PLUGIN_URL . '/admin/assets/js/notifications-panel.js', array(), $this->plugin->getPluginVersion());
		}

		public function notificationsPanel(&$wp_admin_bar)
		{
			if( !current_user_can('administrator') ) {
				return;
			}

			$notifications = $this->getOption('hidden_notices');

			if( empty($notifications) ) {
				return;
			}

			$cont_notifications = sizeof($notifications);

			// Add top menu
			$wp_admin_bar->add_menu(array(
				'id' => 'wbcr-han-notify-panel',
				'parent' => 'top-secondary',
				'title' => sprintf(__('Notifications %s', 'disable-admin-notices'), '<span class="wbcr-han-adminbar-counter">' . $cont_notifications . '</span>'),
				'href' => false
			));

			// loop
			if( !empty($notifications) ) {
				$i = 0;
				foreach($notifications as $notice_id => $message) {
					$message = $this->getExcerpt(stripslashes($message), 0, 350);
					$message .= '<div class="wbcr-han-panel-restore-notify-line"><a href="#" data-notice-id="' . esc_attr($notice_id) . '" class="wbcr-han-panel-restore-notify-link">' . __('Restore notice', 'clearfy') . '</a></div>';

					$wp_admin_bar->add_menu(array(
						'id' => 'wbcr-han-notify-panel-item-' . $i,
						'parent' => 'wbcr-han-notify-panel',
						'title' => $message,
						'href' => false,
						'meta' => array(
							'class' => ''
						)
					));

					$i++;
				}
			}
		}

		public function noticesCollection()
		{
			global $wbcr_dan_plugin_all_notices;

			if( empty($wbcr_dan_plugin_all_notices) ) {
				return;
			}
			?>
			<!-- Disable admin notices plugin (Clearfy tools) -->
			<style>
				.wbcr-dan-hide-notices {
					position: relative;
					padding: 5px 5px 0;
					background: #fff;
				}

				.wbcr-dan-hide-notices > div {
					margin: 0 !important;
				}

				.wbcr-dan-hide-notice-link {
					display: block;
					text-align: right;
					margin: 5px 0 5px 5px;
					font-weight: bold;
					color: #F44336;
				}

				.is-dismissible .wbcr-dan-hide-notice-link {
					margin-right: -30px;
				}

				.wbcr-dan-hide-notice-link:active, .wbcr-dan-hide-notice-link:focus {
					box-shadow: none;
					outline: none;
				}
			</style>

			<!-- Disable admin notices plugin (Clearfy tools) -->
			<script>
				jQuery(document).ready(function($) {
					$(document).on('click', '.wbcr-dan-hide-notice-link', function() {
						var self = $(this),
							noticeID = self.data('notice-id'),
							noticeHtml = self.closest('.wbcr-dan-hide-notices').clone(),
							contanierEl = self.closest('.wbcr-dan-hide-notices').parent();

						noticeHtml.find('.wbcr-dan-hide-notice-link').remove();

						if( !noticeID ) {
							alert('Undefinded error. Please report the bug to our support forum.');
						}

						contanierEl.hide();

						$.ajax(ajaxurl, {
							type: 'post',
							dataType: 'json',
							data: {
								action: 'wbcr_dan_hide_notices',
								security: wbcr_dan_ajax_hide_notice_nonce,
								notice_id: noticeID,
								notice_html: noticeHtml.html()
							},
							success: function(data, textStatus, jqXHR) {
								if( data == 'error' && data.error ) {
									alert(data.error);

									contanierEl.show();
									return;
								}

								contanierEl.remove();
							}
						});
					});
				});
			</script>
			<?php
			foreach($wbcr_dan_plugin_all_notices as $val) {
				echo $val;
			}
		}

		public function catchNotices()
		{
			global $wp_filter, $wbcr_dan_plugin_all_notices;

			$hide_notices_type = $this->getOption('hide_admin_notices');

			if( empty($hide_notices_type) || $hide_notices_type == 'only_selected' ) {
				$get_hidden_notices = $this->getOption('hidden_notices');

				$content = array();
				foreach((array)$wp_filter['admin_notices']->callbacks as $filters) {
					foreach($filters as $callback_name => $callback) {

						if( 'usof_hide_admin_notices_start' == $callback_name || 'usof_hide_admin_notices_end' == $callback_name ) {
							continue;
						}

						ob_start();

						// #CLRF-140 fix bug for php7
						// when the developers forgot to delete the argument in the function of implementing the notification.
						$args = array();
						$accepted_args = isset($callback['accepted_args']) && !empty($callback['accepted_args'])
							? $callback['accepted_args']
							: 0;

						if( $accepted_args > 0 ) {
							for($i = 0; $i < (int)$accepted_args; $i++) {
								$args[] = null;
							}
						}
						//===========

						call_user_func_array($callback['function'], $args);
						$cont = ob_get_clean();

						if( empty($cont) ) {
							continue;
						}

						$uniq_id1 = md5($cont);
						$uniq_id2 = md5($callback_name);

						if( is_array($callback['function']) && sizeof($callback['function']) == 2 ) {
							$class = $callback['function'][0];
							if( is_object($class) ) {
								$class_name = get_class($class);
								$method_name = $callback['function'][1];
								$uniq_id2 = md5($class_name . ':' . $method_name);
							}
						}
						//838339d1a188e17fec838c2df3058603
						//838339d1a188e17fec838c2df3058603
						if( !empty($get_hidden_notices) ) {

							$skip_notice = true;
							foreach((array)$get_hidden_notices as $key => $notice) {
								$splited_notice_id = explode('_', $key);
								if( empty($splited_notice_id) || sizeof($splited_notice_id) < 2 ) {
									continue;
								}
								$compare_notice_id_1 = $splited_notice_id[0];
								$compare_notice_id_2 = $splited_notice_id[1];

								if( $compare_notice_id_1 == $uniq_id1 || $compare_notice_id_2 == $uniq_id2 ) {
									$skip_notice = false;
								}
							}

							if( !$skip_notice ) {
								continue;
							}
						}

						$hide_link = '<a href="#" data-notice-id="' . $uniq_id1 . '_' . $uniq_id2 . '" class="wbcr-dan-hide-notice-link">[' . __('Hide notification forever', 'disable-admin-notices') . ']</a>';

						$cont = preg_replace('/<(script|style)([^>]+)?>(.*?)<\/(script|style)>/is', '', $cont);
						$cont = rtrim(trim($cont));
						$cont = preg_replace('/^(<div[^>]+>)(.*?)(<\/div>)$/is', '$1<div class="wbcr-dan-hide-notices">$2' . $hide_link . '</div>$3', $cont);

						if( empty($cont) ) {
							continue;
						}
						$content[] = $cont;
					}
				}

				$wbcr_dan_plugin_all_notices = $content;
			}

			if( is_user_admin() ) {
				if( isset($wp_filter['user_admin_notices']) ) {
					unset($wp_filter['user_admin_notices']);
				}
			} elseif( isset($wp_filter['admin_notices']) ) {
				unset($wp_filter['admin_notices']);
			}
			if( isset($wp_filter['all_admin_notices']) ) {
				foreach($wp_filter['all_admin_notices']->callbacks as $f_key => $f) {
					foreach($f as $c_name => $clback) {
						#Fix for Divi theme
						if( $c_name != 'et_pb_export_layouts_interface' ) {
							unset($wp_filter['all_admin_notices']->callbacks[$f_key][$c_name]);
						}
					}
				}
			}
		}

		/**
		 * Get excerpt from string
		 *
		 * @param String $str String to get an excerpt from
		 * @param Integer $startPos Position int string to start excerpt from
		 * @param Integer $maxLength Maximum length the excerpt may be
		 * @return String excerpt
		 */
		function getExcerpt($str, $startPos = 0, $maxLength = 100)
		{
			if( strlen($str) > $maxLength ) {
				$excerpt = substr($str, $startPos, $maxLength - 3);
				$lastSpace = strrpos($excerpt, ' ');
				$excerpt = substr($excerpt, 0, $lastSpace);
				$excerpt .= '...';
			} else {
				$excerpt = $str;
			}

			return $excerpt;
		}
	}