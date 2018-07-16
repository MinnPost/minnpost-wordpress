<?php
	/**
	 * Restore notice
	 * @author Webcraftic <wordpress.webraftic@gmail.com>
	 * @copyright (c) 12.01.2018, Webcraftic
	 * @version 1.0
	 */

	// Exit if accessed directly
	if( !defined('ABSPATH') ) {
		exit;
	}

	function wbcr_dan_ajax_restore_notice()
	{
		check_ajax_referer(WDN_Plugin::app()->getPluginName() . '_ajax_restore_notice_nonce', 'security');

		if( !current_user_can('update_plugins') ) {
			echo json_encode(array('error' => __('You don\'t have enough capability to edit this information.', 'disable-admin-notices')));
			exit;
		}

		$notice_id = isset($_POST['notice_id'])
			? sanitize_text_field($_POST['notice_id'])
			: null;

		if( empty($notice_id) ) {
			echo json_encode(array('error' => __('Undefinded notice id.', 'disable-admin-notices')));
			exit;
		}

		$get_hidden_notices = WDN_Plugin::app()->getOption('hidden_notices');

		if( !empty($get_hidden_notices) && isset($get_hidden_notices[$notice_id]) ) {
			unset($get_hidden_notices[$notice_id]);
		}

		WDN_Plugin::app()->updateOption('hidden_notices', $get_hidden_notices);

		echo json_encode(array('success' => __('Success', 'disable-admin-notices')));
		exit;
	}

	add_action('wp_ajax_wbcr_dan_restore_notice', 'wbcr_dan_ajax_restore_notice');
