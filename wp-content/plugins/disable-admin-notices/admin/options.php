<?php
	/**
	 * Options for additionally form
	 * @author Webcraftic <wordpress.webraftic@gmail.com>
	 * @copyright (c) 21.01.2018, Webcraftic
	 * @version 1.0
	 */

	// Exit if accessed directly
	if( !defined('ABSPATH') ) {
		exit;
	}

	/**
	 * @return array
	 */
	function wbcr_dan_get_plugin_options()
	{
		$options = array();

		$options[] = array(
			'type' => 'html',
			'html' => '<div class="wbcr-factory-page-group-header">' . '<strong>' . __('Admin notifications, Update nags', 'disable-admin-notices') . '</strong>' . '<p>' . __('Do you know the situation, when some plugin offers you to update to premium, to collect technical data and shows many annoying notices? You are close these notices every now and again but they newly appears and interfere your work with WordPress. Even worse, some plugin’s authors delete “close” button from notices and they shows in your admin panel forever.', 'disable-admin-notices') . '</p>' . '</div>'
		);

		$options[] = array(
			'type' => 'dropdown',
			'name' => 'hide_admin_notices',
			'way' => 'buttons',
			'title' => __('Hide admin notices', 'disable-admin-notices'),
			'data' => array(
				array(
					'all',
					__('All notices', 'disable-admin-notices'),
					__('Hide all notices globally.', 'disable-admin-notices')
				),
				array(
					'only_selected',
					__('Only selected', 'disable-admin-notices'),
					__('Hide selected notices only. You will see the link "Hide notification forever" in each notice. Push it and they will not bother you anymore.', 'disable-admin-notices')
				),
				array(
					'not_hide',
					__("Don't nide", 'disable-admin-notices'),
					__('Do not hide notices and do not show “Hide notification forever” link for admin.', 'disable-admin-notices')
				)
			),
			'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'green'),
			'hint' => __('Some plugins shows notifications about premium version, data collecting or promote their services. Even if you push close button (that sometimes are impossible), notices are shows again in some time. This option allows you to control notices. Hide them all or each individually. Some plugins shows notifications about premium version, data collecting or promote their services. Even if you push close button (that sometimes are impossible), notices are shows again in some time. This option allows you to control notices. Hide them all or each individually.', 'disable-admin-notices'),
			'default' => 'only_selected',
			'events' => array(
				'all' => array(
					'show' => '.factory-control-hide_admin_notices_user_roles',
					'hide' => '.factory-control-reset_notices_button'
				),
				'only_selected' => array(
					'hide' => '.factory-control-hide_admin_notices_user_roles',
					'show' => '.factory-control-reset_notices_button'
				),
				'not_hide' => array(
					'hide' => '.factory-control-hide_admin_notices_user_roles, .factory-control-reset_notices_button'
				)
			)
		);

		$options[] = array(
			'type' => 'checkbox',
			'way' => 'buttons',
			'name' => 'show_notices_in_adminbar',
			'title' => __('Enable hidden notices in adminbar', 'disable-admin-notices'),
			'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'green'),
			'hint' => __('By default, the plugin hides all notices, which you specified. If you enable this option, the plugin will collect all hidden notices and show them into the top admin toolbar. It will not disturb you but will allow to look notices at your convenience.', 'disable-admin-notices'),
			'default' => false
		);

		$options[] = array(
			'type' => 'html',
			'html' => 'wbcr_dan_reset_notices_button'
		);

		$options[] = array(
			'type' => 'separator'
		);

		return $options;
	}

	/**
	 * @param $form
	 * @param $page Wbcr_FactoryPages401_ImpressiveThemplate
	 * @return mixed
	 */
	function wbcr_dan_additionally_form_options($form, $page)
	{
		if( empty($form) ) {
			return $form;
		}

		$options = wbcr_dan_get_plugin_options();

		foreach(array_reverse($options) as $option) {
			array_unshift($form[0]['items'], $option);
		}

		return $form;
	}

	add_filter('wbcr_clr_additionally_form_options', 'wbcr_dan_additionally_form_options', 10, 2);

	/**
	 * @param $html_builder Wbcr_FactoryForms400_Html
	 */
	function wbcr_dan_reset_notices_button($html_builder)
	{
		$form_name = $html_builder->getFormName();
		$reseted = false;

		if( isset($_POST['wbcr_dan_reset_action']) ) {
			check_admin_referer($form_name, 'wbcr_dan_reset_nonce');

			WDN_Plugin::app()->deleteOption('hidden_notices');

			$reseted = true;
		}

		$count_hidden_notices = 0;
		$hidden_notices = WDN_Plugin::app()->getOption('hidden_notices');

		if( !empty($hidden_notices) ) {
			$count_hidden_notices = sizeof($hidden_notices);
		}

		?>
		<div class="form-group form-group-checkbox factory-control-reset_notices_button">
			<label for="wbcr_clearfy_reset_notices_button" class="col-sm-6 control-label">
				<span class="factory-hint-icon factory-hint-icon-grey" data-toggle="factory-tooltip" data-placement="right" title="" data-original-title="<?php _e('Push reset hidden notices if you need to show hidden notices again.', 'disable-admin-notices') ?>">
					<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAkAAAAJCAQAAABKmM6bAAAAUUlEQVQIHU3BsQ1AQABA0X/komIrnQHYwyhqQ1hBo9KZRKL9CBfeAwy2ri42JA4mPQ9rJ6OVt0BisFM3Po7qbEliru7m/FkY+TN64ZVxEzh4ndrMN7+Z+jXCAAAAAElFTkSuQmCC" alt="">
				</span>
			</label>

			<div class="control-group col-sm-6">
				<div class="factory-checkbox factory-from-control-checkbox factory-buttons-way btn-group">
					<form method="post">
						<?php wp_nonce_field($form_name, 'wbcr_dan_reset_nonce'); ?>
						<input type="submit" name="wbcr_dan_reset_action" value="<?php printf(__('Reset hidden notices (%s)', 'disable-admin-notices'), $count_hidden_notices) ?>" class="button button-default"/>
						<?php if( $reseted ): ?>
							<div style="color:green;margin-top:5px;"><?php _e('Hidden notices are successfully reset, now you can see them again!', 'disable-admin-notices') ?></div>
						<?php endif; ?>
					</form>
				</div>
			</div>
		</div>
	<?php
	}

