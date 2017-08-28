<?php
/**
 *  WP-SpamShield Admin Settings Page
 *  File Version 1.9.17
 */

/* Make sure file remains secure if called directly */
if( !defined( 'ABSPATH' ) || !defined( 'WPSS_VERSION' ) ) {
	if( !headers_sent() ) { @header( $_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden', TRUE, 403 ); @header( 'X-Robots-Tag: noindex', TRUE ); }
	die( 'ERROR: Direct access to this file is not allowed.' );
}
/* Prevents unintentional error display if WP_DEBUG not enabled. */
if( TRUE !== WPSS_DEBUG && TRUE !== WP_DEBUG ) { @ini_set( 'display_errors', 0 ); @error_reporting( 0 ); }


/** BEGIN **/

			if( !is_admin() || !rs_wpss_is_user_admin() ) { self::wp_die(); }

			global $is_apache, $is_IIS, $is_iis7, $is_nginx;

			echo WPSS_EOL."\t\t\t".'<div class="wrap">'.WPSS_EOL."\t\t\t".'<h2>WP-SpamShield ' . __( 'Settings' ) . '</h2>'.WPSS_EOL;

			$ip					= @WP_SpamShield::get_ip_addr();
			$spam_count_raw		= rs_wpss_count();
			$spamshield_options	= WP_SpamShield::get_option();
			$admin_email		= get_option( 'admin_email' );
			if( empty( $spamshield_options['form_message_recipient'] ) || !is_email( $spamshield_options['form_message_recipient'] ) ) {
				$spamshield_options['form_message_recipient'] = $admin_email;
			}
			$wpss_options_default = unserialize( WPSS_OPTIONS_DEFAULT ); $wpss_options_bool = array();
			foreach( $wpss_options_default as $k => $v ) {
				if( $v === 1 || $v === 0 ) { $wpss_options_bool[$k] = $v; } /* Boolean integer */
				if( !isset( $spamshield_options[$k] )	) { $spamshield_options[$k] = $v; continue; }
				if( isset( $wpss_options_bool[$k] )		) { $spamshield_options[$k] = (int)(bool) $spamshield_options[$k]; } /* Sanitize stored values */
			}
			rs_wpss_update_session_data($spamshield_options);
			$spamshield_options_prev = $spamshield_options;	/* Previous options set - plugin will use them to compare when validating */
			$current_date	= date( WPSS_DATE_BASIC );
			$timenow		= time();
			$install_date	= empty( $spamshield_options['install_date'] ) ? $current_date : $spamshield_options['install_date'];
			$num_days_inst	= rs_wpss_date_diff($install_date, $current_date); if( $num_days_inst < 1 ) { $num_days_inst = 1; }
			$spam_count		= $spam_count_raw; if( $spam_count < 1 ) { $spam_count = 1; }
			$avg_blk_dly	= round( $spam_count / $num_days_inst );
			$avg_blk_dly_d	= rs_wpss_number_format( $avg_blk_dly );
			$est_hrs_ret	= $this->est_hrs_returned( $spam_count );
			$est_hrs_ret_d	= rs_wpss_number_format( $est_hrs_ret );

			/* Check Installation Status */
			$wpss_inst_status				= $this->check_install_status();
			if( !empty( $wpss_inst_status ) ) {
				$wpss_inst_status_image		= 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAA/ElEQVR4AdXQtVIDURhH8YvLQ+DPhJY4LdpiDe7uUtKgDfYAuEuLu1MxHweNe3aT3Jlf9L97MlG2Tul4bBT6cIsn3Boz+qwHkcrVw0X7ECftuHrzMsivVbRiyBhasQb5VeRKYA6COwTZ2YXiCYIZVwLLEGw7sT2CYFHrwIJfBnac2B678xf1QzDhxHYKH+hVPj/8iiT0ogO1aEQ3umzoRiNq0YEeJNi6eQpuIB66RIa1QC/ES/qsBQYgXjLgTuAZacjAqxaBLKNtthaBPKNtgRaBU6PthRaBB6PtcwAGgA50QTQKAO4GKiBeUm0tEIkyCN7x5KJ3CIoRrX7PJ4rs1G5u6JpTAAAAAElFTkSuQmCC';
				$wpss_inst_status_color		= '#77A51F'; /* '#D6EF7A', 'green' */
				$wpss_inst_status_bg_color	= '#EBF7D5'; /* '#77A51F', '#CCFFCC' */
				$wpss_inst_status_msg_main	= __( 'Installed Correctly', 'wp-spamshield' );
				$wpss_inst_status_msg_text	= WPSS_Func::lower( $wpss_inst_status_msg_main );
			} else {
				$wpss_inst_status_image		= 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAABHElEQVR4AWMYVGCZIUszELfTyvBMIP4PxSXUNpwdiH8gWfAXiHmoacFMuOEIvIRahivDDcXE2tSw4DgeCy5QargvkmEPgXgVFD9AEg+nxIJXSAbVIolXIIl/INfwUrTgmIgk14Mm10Cq4bygpIhmyEIk+dlY4kOYFAuWYzFgLQH5jcQarocjxexCUrMZhxpzYiy4hEPzUSQ1+3GouUXI8Eg8af4ckrpjeNQl47PgLR6N95HU3cCj7hMQM2EzvAmkgAC+DcQ3iVDXi264GCFN0NybBMQJQHyPCPWyyBZsIUKDF5J6dyLU74MptgULEMZJSBbEEKnHDaT4LpGKPwNxPhDnAvFHIvU8YgAxaIlBFrhCa6x51MRQMz0ZaA0AjjUGdu65IP4AAAAASUVORK5CYII=';
				$wpss_inst_status_color		= '#A63104'; /* '#FC956D', 'red' */
				$wpss_inst_status_bg_color	= '#FEDBCD'; /* '#A63104', '#FFCCCC' */
				$wpss_inst_status_msg_main	= __( 'Not Installed Correctly', 'wp-spamshield' );
				$wpss_inst_status_msg_text	= WPSS_Func::lower( $wpss_inst_status_msg_main );
				/* Add specifics - Standalone Nginx */
				if( !empty( $is_nginx ) && empty( $is_apache ) ) {
					$wpss_inst_status_msg_main	.= ' ' . sprintf( __( 'Your server is running <a href=%1$s>standalone Nginx</a>.', 'wp-spamshield' ), '"'. rs_wpss_append_url( 'https://www.redsandmarketing.com/plugins/wp-spamshield/?wpss=requirements#wpss_requirements' ) .'" target="_blank" rel="external" ' ); /* TO DO: TRANSLATE - Added 1.9.9.8.1 */
				}
			}

			/* Checks Complete */

			/* Save Options */
			if( !empty( $_POST['submit_wpss_general_options'] ) && rs_wpss_is_user_admin() && check_admin_referer('wpss_update_general_options_token','ugo_tkn') ) {
				echo '<div class="updated notice is-dismissible"><p><strong>' . __( 'Plugin Spam settings saved.', 'wp-spamshield' ) . '</strong></p></div>';
			}
			if( !empty( $_POST['submit_wpss_contact_options'] ) && rs_wpss_is_user_admin() && check_admin_referer('wpss_update_contact_options_token','uco_tkn') ) {
				echo '<div class="updated notice is-dismissible"><p><strong>' . __( 'Plugin Contact Form settings saved.', 'wp-spamshield' ) . '</strong></p></div>';
			}
			/* Filter Vars */
			$_GET_RAW = $_GET;
			$_GET_FIL = array();
			if( !empty( $_GET ) && is_array( $_GET ) ) {
				foreach( $_GET as $k => $v ) { $_GET_FIL[$k] = sanitize_text_field( stripslashes( $_GET[$k] ) ); }
			}

			/* Add IP to Blacklist */
			$ip_to_blacklist = $ip_blacklist_nonce_value = '';

			if( !empty( $_GET_FIL['bl_ip'] ) ) {
				$ip_to_blacklist 			= $_GET_FIL['bl_ip'];
			}
			$ip_nodot = str_replace( '.', '', $ip_to_blacklist );
			$ip_blacklist_nonce_action		= 'blacklist_ip_'.$ip_to_blacklist;
			$ip_blacklist_nonce_name		= 'bl'.$ip_nodot.'tkn';
			if( !empty ( $_GET_FIL[$ip_blacklist_nonce_name] ) ) {
				$ip_blacklist_nonce_value	= $_GET_FIL[$ip_blacklist_nonce_name];
			}
			$wpss_action = !empty( $_GET_FIL['wpss_action'] ) ? $_GET_FIL['wpss_action'] : '';
			if( $wpss_action === 'blacklist_ip' && !empty( $_GET_FIL['bl_ip'] ) && !empty( $_GET_FIL[$ip_blacklist_nonce_name] ) && rs_wpss_is_user_admin() && empty( $_POST['submit_wpss_general_options'] ) && empty( $_POST['submit_wpss_contact_options'] ) ) {
				if( rs_wpss_verify_nonce( $ip_blacklist_nonce_value, $ip_blacklist_nonce_action, $ip_blacklist_nonce_name ) ) {
					if( WP_SpamShield::is_valid_ip( $ip_to_blacklist ) ) {
						if( $ip_to_blacklist === $ip ) {
							echo '<div class="error notice is-dismissible"><p><strong>' . __( 'Are you sure you want to blacklist yourself? IP Address not added to Comment Blacklist.', 'wp-spamshield' ) . '</strong></p></div>'; /* NEEDS TRANSLATION */
						} elseif( rs_wpss_is_admin_ip( $ip_to_blacklist ) ) {
							echo '<div class="error notice is-dismissible"><p><strong>' . __( 'Are you sure you want to blacklist an Administrator? IP Address not added to Comment Blacklist.', 'wp-spamshield' ) . '</strong></p></div>'; /* NEEDS TRANSLATION */
						} else {
							$ip_to_blacklist_valid='1';
							rs_wpss_add_ip_to_blacklist($ip_to_blacklist);
							if( !empty($_GET_FIL['c']) && $ip_to_blacklist === get_comment_author_IP($_GET_FIL['c']) ) { wp_delete_comment($_GET_FIL['c'],TRUE); }
							echo '<div class="updated notice is-dismissible"><p><strong>' . __( 'IP Address added to Comment Blacklist.', 'wp-spamshield' ) . '</strong></p></div>';
						}
					} else {
						echo '<div class="error notice is-dismissible"><p><strong>' . __( 'Invalid IP Address - not added to Comment Blacklist.', 'wp-spamshield' ) . '</strong></p></div>';
					}
				} else {
					echo '<div class="error notice is-dismissible"><p><strong>' . __( 'Security token invalid or expired. IP Address not added to Comment Blacklist.', 'wp-spamshield' ) . '</strong></p></div>'; /* NEEDS TRANSLATION */
				}
			}

			/* Set Margins */
			$wpss_vert_margins = '15'; $wpss_horz_margins = '15'; $wpss_icon_size = '48';$wpss_sm_txt_spacer_size = '24';

			/* Display Install Status */
			?>
			<div style='width:797px;border-style:solid;border-width:1px;border-color:<?php echo $wpss_inst_status_color; ?>;background-color:<?php echo $wpss_inst_status_bg_color; ?>;padding:0px 15px 0px 15px;margin-top:30px;float:left;clear:left;'>
			<p><strong><?php echo "<img src='".$wpss_inst_status_image."' alt='' width='24' height='24' style='border-style:none;vertical-align:middle;padding-right:7px;' /> " . __( 'Installation Status', 'wp-spamshield' ) . ": <span style='color:".$wpss_inst_status_color.";'>".$wpss_inst_status_msg_main."</span>"; ?></strong></p>
			</div>

			<?php
			/**
			 *  TO DO:
			 *  Detect PHP Version and Memory Issues and Display Warning, with info on how to fix
			 */

			/* Start Layout */
			if( !empty( $spam_count_raw ) ) {
				echo WPSS_EOL."\t\t\t<div style='width:797px;border-style:solid;border-width:1px;border-color:#333333;background-color:#FEFEFE;padding:0px 15px 0px 15px;margin-top:".$wpss_vert_margins."px;float:left;clear:left;'>".WPSS_EOL."\t\t\t<p style='font-size:20px;line-height:180%;'><img src='data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAYAAABXAvmHAAACNklEQVR4Ae3aA6wdQRTG8dq2bTeuFbu2bdu2Eb2gtm3btm0b039epjrVZr3J3uR3eWZyvuc350Zx9NJlxoJIQbvQdExshtI2InpQmi+OZ1DCMxT3e/P9RNPP8UY8N9CPjZfFXdHoWhTAHSjhLsr6ofES2A0ljNWvJ8J7qL/YieJuNhxLNz0cd6CEC8jzU31qGeAv7mAEiiOmXc120o2OwQzswQsoSE/RROwgAhj2AnswE2N0D53MBHgBJUiHUUku/XsA017YEeAtLmMNuiObXOLXAI9RBFFlSVAC3EA0/WwgA9xC9DBAGCAMEAYIA4QBwgAeBbiJaCYDpMEHrwPcQQpEQ1Kj9B658Ph7E8Y917df7AjwBa/wUt8apXADKfR+CY3S9Ulwx3IAix4ijskvv2i4EeQA0XHLSoCbHgdIhOdQuGlmgy0eBygApW0xs8EkjwPUgdImmdmgqscB5kBpVc1skBTPvQjAmsR4AoVnSGr2cGuaRwH6Q2nTrJzOFXU7APU58BFKK2r1iDHC5QD7oLQIO85Ik+CeGwGoHQ2l3UMSuw56yzgdgLq+Ym0Fu4/WuzsVgJq2Yl0Pp+YDg+wOwOvjxZoBTg85JtkRQP6y0sa5NanpbCUAz2XCdVHb2e35WF0zAXhcHUqo69WQLxfuGwnAbQwskrMxZPd6UhkLy6Ek3NU1hfBUvLYCsfw0K64PJVzFQiihvl8n9elxFOovTiBTEN4v0RJKaB60d6xkxhmcjrzv0OUrOtlL0KDueBEAAAAASUVORK5CYII=' alt='' width='".$wpss_icon_size."' height='".$wpss_icon_size."' style='border-style:none;vertical-align:middle;padding-right:7px;' /> " . sprintf( __( 'WP-SpamShield has blocked <strong> %s </strong> spam!', 'wp-spamshield' ), rs_wpss_number_format( $spam_count_raw ) ) . "</p>".WPSS_EOL."\t\t\t";
				if( $avg_blk_dly >= 2 ) {
					echo "<p style='line-height:180%;'><img src='data:image/png;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAUUAAAALAAAAAABAAEAAAICRAEAOw==' alt='' width='".$wpss_icon_size."' height='".$wpss_sm_txt_spacer_size."' style='border-style:none;vertical-align:middle;padding-right:7px;' /> " . sprintf( __( 'That\'s <strong> %s </strong> spam a day that you don\'t have to worry about.', 'wp-spamshield' ), $avg_blk_dly_d ) . "</p>".WPSS_EOL."\t\t\t";
				}
				if( rs_wpss_is_lang_en_us() && $est_hrs_ret >= 2 ) {
					echo "<p style='line-height:180%;'><img src='data:image/png;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAUUAAAALAAAAAABAAEAAAICRAEAOw==' alt='' width='".$wpss_icon_size."' height='".$wpss_sm_txt_spacer_size."' style='border-style:none;vertical-align:middle;padding-right:7px;' /> " . sprintf( __( 'This plugin has saved you <strong> %s </strong> hours of managing spam. You\'re welcome!', 'wp-spamshield' ), $est_hrs_ret_d ) . "</p>".WPSS_EOL."\t\t\t";
				}
				echo "</div>".WPSS_EOL."\t\t\t";
			}
			if( !empty( $_POST['submitted_wpss_general_options'] ) && rs_wpss_is_user_admin() && check_admin_referer('wpss_update_general_options_token','ugo_tkn') ) {
				if( !empty( $_POST['comment_logging'] ) ) { $post_comment_logging = $_POST['comment_logging']; } else { $post_comment_logging = 0; }
				if( !empty( $_POST['comment_logging_all'] ) ) { $post_comment_logging_all = $_POST['comment_logging_all']; } else { $post_comment_logging_all = 0; }
				if( !empty( $post_comment_logging_all ) ) { $post_comment_logging = 1; }
				if( !empty( $post_comment_logging ) ) {
					if( !empty( $spamshield_options['comment_logging_start_date'] ) && $spamshield_options['comment_logging_start_date'] > 1451606400 ) {
						$comment_logging_start_date = $spamshield_options['comment_logging_start_date'];
					} else { $comment_logging_start_date = $timenow; rs_wpss_log_reset(); }
					$reset_interval_rsds		= 10 * YEAR_IN_SECONDS;
					$reset_interval_default		= ( self::is_debug() ) ? $reset_interval_rsds : WEEK_IN_SECONDS; /* Default is one week */
					$reset_interval_override	= $reset_interval_default; /* Change for TESTING only, and it will override defaults */
					$reset_interval				= ( $reset_interval_override !== $reset_interval_default ) ? $reset_interval_override : $reset_interval_default;
					$comment_logging_end_date	= $spamshield_options['comment_logging_end_date'] = ( !empty( $spamshield_options['comment_logging_end_date'] ) && $spamshield_options['comment_logging_end_date'] > 1451606400 ) ? $spamshield_options['comment_logging_end_date'] : $comment_logging_start_date + $reset_interval;
				} else { $comment_logging_start_date = $comment_logging_end_date = 0; }

				/* Reset Log when turning on Comment Logging */
				if( !empty( $post_comment_logging ) && empty( $spamshield_options['comment_logging'] ) ) { $comment_logging_start_date = $timenow; rs_wpss_log_reset(); }

				/* Update User Admin Status */
				$this->update_admin_status();
				/* Purge Caches */
				$this->purge_cache();
				/* Check if initial user approval process was run on activation */
				$wpss_init_user_approve_run = get_option( 'spamshield_init_user_approve_run' );
				if( empty( $wpss_init_user_approve_run ) ) { $this->approve_previous_users(); }
				/* Validate POST Values */
				$valid_post_spamshield_options = !empty( $_POST ) ? $_POST : array();
				$wpss_options_default = unserialize( WPSS_OPTIONS_DEFAULT );
				$wpss_options_general_boolean = array( 'comment_logging', 'comment_logging_all', 'enhanced_comment_blacklist', 'enable_whitelist', 'block_all_trackbacks', 'block_all_pingbacks', 'allow_proxy_users', 'hide_extra_data', 'registration_shield_disable', 'registration_shield_level_1', 'disable_cf7_shield', 'disable_gf_shield', 'disable_misc_form_shield', 'disable_email_encode', 'allow_comment_author_keywords', 'auto_update_plugin', 'auto_purge_cache', 'disable_security_alerts', 'promote_plugin_link', );
				foreach( $wpss_options_general_boolean as $i => $v ) {
					$valid_post_spamshield_options[$v] = ( !empty( $_POST[$v] ) && $_POST[$v] !== 'off' ) ? 1 : 0;
				}
				if( empty( $spamshield_options['comment_logging_all'] ) && $valid_post_spamshield_options['comment_logging_all'] == 1 ) { /* Turns Blocked Comment Logging Mode on if user selects "Log All Comments" */
					$valid_post_spamshield_options['comment_logging'] = 1;
				}
				if( !empty( $spamshield_options['comment_logging'] ) && $valid_post_spamshield_options['comment_logging'] == 0 ) { /* If Blocked Comment Logging Mode is turned off then deselects "Log All Comments" */
					$valid_post_spamshield_options['comment_logging_all'] = 0;
				}
				if( !empty( $_POST['comment_min_length'] ) && self::preg_match( "~^\d+$~", $_POST['comment_min_length'] ) ) {
					$comment_min_length_temp = (int) WP_SpamShield::sanitize_string( $_POST['comment_min_length'] );
					$valid_post_spamshield_options['comment_min_length'] = ( !empty( $comment_min_length_temp ) && $comment_min_length_temp <= 30 ) ? $comment_min_length_temp : $wpss_options_default['comment_min_length'];
				} else { $valid_post_spamshield_options['comment_min_length'] = $wpss_options_default['comment_min_length']; }

				/* Update Values */
				$option_list_go = array( 'comment_logging', 'comment_logging_all', 'enhanced_comment_blacklist', 'enable_whitelist', 'comment_min_length', 'block_all_trackbacks', 'block_all_pingbacks', 'allow_proxy_users', 'hide_extra_data', 'registration_shield_disable', 'registration_shield_level_1', 'disable_cf7_shield', 'disable_gf_shield', 'disable_misc_form_shield', 'disable_email_encode', 'allow_comment_author_keywords', 'auto_update_plugin', 'auto_purge_cache', 'disable_security_alerts', 'promote_plugin_link', );
				foreach( $option_list_go as $i => $v ) {
					if( 'auto_update_plugin' === $v && defined( 'WPSS_AUTOUP_DISABLE' ) && TRUE === WPSS_AUTOUP_DISABLE ) { $spamshield_options[$v] = 0; continue; }
					$spamshield_options[$v] = ( isset( $valid_post_spamshield_options[$v] ) ) ? $valid_post_spamshield_options[$v] : $spamshield_options[$v];
				}
				$spamshield_options['comment_logging_start_date']	= $comment_logging_start_date;
				$spamshield_options['comment_logging_end_date']		= $comment_logging_end_date;
				$spamshield_options['install_date']					= $install_date;
				self::update_option( $spamshield_options );
				if( !empty( $ip ) ) { update_option( 'spamshield_last_admin', $ip ); }
				$blacklist_keys_update = WP_SpamShield::sanitize_string( $_POST['wordpress_comment_blacklist'] );
				rs_wpss_update_bw_list_keys( 'black', $blacklist_keys_update );
				$whitelist_keys_update = WP_SpamShield::sanitize_string( $_POST['wpss_whitelist'] );
				rs_wpss_update_bw_list_keys( 'white', $whitelist_keys_update );
			}
			if( !empty( $_POST['submitted_wpss_contact_options'] ) && rs_wpss_is_user_admin() && check_admin_referer( 'wpss_update_contact_options_token', 'uco_tkn' ) ) {
				/* Update User Admin Status */
				$this->update_admin_status();
				/* Purge Caches */
				$this->purge_cache();
				/* Check if initial user approval process was run on activation */
				$wpss_init_user_approve_run = get_option( 'spamshield_init_user_approve_run' );
				if( empty( $wpss_init_user_approve_run ) ) { $this->approve_previous_users(); }
				/* Validate/Sanitize POST Values */
				$valid_post_spamshield_options = !empty( $_POST ) ? $_POST : array();
				$wpss_options_default = unserialize( WPSS_OPTIONS_DEFAULT );
				if( empty( $spamshield_options['form_message_recipient'] ) || !is_email( $spamshield_options['form_message_recipient'] ) ) {
					$spamshield_options['form_message_recipient'] = get_option('admin_email');
				}
				$wpss_options_contact_boolean = array ( 'form_include_website', 'form_require_website', 'form_include_phone', 'form_require_phone', 'form_include_company', 'form_require_company', 'form_include_drop_down_menu', 'form_require_drop_down_menu', 'form_include_user_meta', 'form_mail_encode' );
				foreach( $wpss_options_contact_boolean as $i => $v ) {
					$valid_post_spamshield_options[$v] = ( !empty( $_POST[$v] ) && $_POST[$v] !== 'off' ) ? 1 : 0;
				}
				$wpss_options_contact_text = array ( 'form_drop_down_menu_title', 'form_drop_down_menu_item_1', 'form_drop_down_menu_item_2', 'form_drop_down_menu_item_3', 'form_drop_down_menu_item_4', 'form_drop_down_menu_item_5', 'form_drop_down_menu_item_6', 'form_drop_down_menu_item_7', 'form_drop_down_menu_item_8', 'form_drop_down_menu_item_9', 'form_drop_down_menu_item_10', 'form_response_thank_you_message' );
				foreach( $wpss_options_contact_text as $i => $v ) {
					$valid_post_spamshield_options[$v] = !empty( $_POST[$v] ) ? WP_SpamShield::sanitize_opt_string( $_POST[$v] ) : $wpss_options_default[$v];
				}
				if( !empty( $_POST['form_message_width'] ) && self::preg_match( "~^\d+$~", $_POST['form_message_width'] ) ) {
					$form_message_width_temp = (int) WP_SpamShield::sanitize_string( $_POST['form_message_width'] );
					$valid_post_spamshield_options['form_message_width'] = $form_message_width_temp >= $wpss_options_default['form_message_width'] ? $form_message_width_temp : $wpss_options_default['form_message_width'];
				} else { $valid_post_spamshield_options['form_message_width'] = $wpss_options_default['form_message_width']; }
				if( !empty( $_POST['form_message_height'] ) && self::preg_match( "~^\d+$~", $_POST['form_message_height'] ) ) {
					$form_message_height_temp = (int) WP_SpamShield::sanitize_string( $_POST['form_message_height'] );
					$valid_post_spamshield_options['form_message_height'] = $form_message_height_temp >= 5 ? $form_message_height_temp : $wpss_options_default['form_message_height'];
				} else { $valid_post_spamshield_options['form_message_height'] = $wpss_options_default['form_message_height']; }
				if( !empty( $_POST['form_message_min_length'] ) && self::preg_match( "~^\d+$~", $_POST['form_message_min_length'] ) ) {
					$form_message_min_length_temp = (int) WP_SpamShield::sanitize_string( $_POST['form_message_min_length'] );
					$valid_post_spamshield_options['form_message_min_length'] = ( $form_message_min_length_temp >= 15 && $form_message_min_length_temp <= 150 ) ? $form_message_min_length_temp : $wpss_options_default['form_message_min_length'];
				} else { $valid_post_spamshield_options['form_message_min_length'] = $wpss_options_default['form_message_min_length']; }
				if( !empty( $_POST['form_message_recipient'] ) && is_string( $_POST['form_message_recipient'] ) ) {
					$form_message_recipient_temp = WP_SpamShield::sanitize_string( $_POST['form_message_recipient'] );
					$valid_post_spamshield_options['form_message_recipient'] = is_email( $form_message_recipient_temp ) ? $form_message_recipient_temp : $admin_email;
				} else { $valid_post_spamshield_options['form_message_recipient'] = $admin_email; }
				if( !empty( $_POST['form_response_thank_you_message'] ) && is_string( $_POST['form_response_thank_you_message'] ) ) {
					$form_response_thank_you_message_temp = WP_SpamShield::filter_null( trim( stripslashes( $_POST['form_response_thank_you_message'] ) ) );
					$valid_post_spamshield_options['form_response_thank_you_message'] = !empty( $form_response_thank_you_message_temp ) ? $form_response_thank_you_message_temp : $wpss_options_default['form_response_thank_you_message'];
				} else { $valid_post_spamshield_options['form_response_thank_you_message'] = $wpss_options_default['form_response_thank_you_message']; }
				$option_list_co = array( 'form_include_website', 'form_require_website', 'form_include_phone', 'form_require_phone', 'form_include_company', 'form_require_company', 'form_include_drop_down_menu', 'form_require_drop_down_menu', 'form_drop_down_menu_title', 'form_drop_down_menu_item_1', 'form_drop_down_menu_item_2', 'form_drop_down_menu_item_3', 'form_drop_down_menu_item_4', 'form_drop_down_menu_item_5', 'form_drop_down_menu_item_6', 'form_drop_down_menu_item_7', 'form_drop_down_menu_item_8', 'form_drop_down_menu_item_9', 'form_drop_down_menu_item_10', 'form_message_width', 'form_message_height', 'form_message_min_length', 'form_message_recipient', 'form_response_thank_you_message', 'form_include_user_meta', 'form_mail_encode', );
				foreach( $option_list_co as $i => $v ) { $spamshield_options[$v] = ( isset( $valid_post_spamshield_options[$v] ) ) ? $valid_post_spamshield_options[$v] : $spamshield_options[$v]; }
				/**
				 *  Validate Include/Require Options
				 *  Dummy-proofing the contact form settings so that you can't require a field without including it first.
				 *  @since 1.9.5.3
				 */
				$option_list_co_ir = array( 'website', 'phone', 'company', 'drop_down_menu', );
				foreach( $option_list_co_ir as $i => $v ) {
					$inc = 'form_include_'; $req = 'form_require_';
					$i_old = $spamshield_options_prev[$inc.$v]; $i_new = $spamshield_options[$inc.$v];
					$r_old = $spamshield_options_prev[$req.$v]; $r_new = $spamshield_options[$req.$v];
					if( !empty( $r_new ) && empty( $i_new ) && empty( $i_old ) && empty( $r_old ) ) { $spamshield_options[$inc.$v] = 1; }
					elseif( empty( $i_new ) && !empty( $r_new ) && !empty( $i_old ) && !empty( $r_old ) ) { $spamshield_options[$req.$v] = 0; }
				}
				$spamshield_options['install_date'] = $install_date;
				if( !empty( $spamshield_options['comment_logging_all'] ) ) { $spamshield_options['comment_logging'] = 1; }
				if( empty( $spamshield_options['comment_logging'] ) ) { $spamshield_options['comment_logging_all'] = 0; }
				self::update_option( $spamshield_options ); rs_wpss_update_session_data($spamshield_options);
				if( !empty( $ip ) ) { update_option( 'spamshield_last_admin', $ip ); }
			}
			$wpss_info_box_height			= rs_wpss_is_lang_en_us() ? '315' : '335';
			$wordpress_comment_blacklist	= rs_wpss_get_bw_list_keys( 'black' );
			$wpss_whitelist 				= rs_wpss_get_bw_list_keys( 'white' );

			?>

			<div style="width:375px;height:<?php echo $wpss_info_box_height; ?>px;border-style:solid;border-width:1px;border-color:#003366;background-color:#DDEEFF;padding:0px 15px 0px 15px;margin-top:<?php echo $wpss_vert_margins; ?>px;margin-right:<?php echo $wpss_horz_margins; ?>px;float:left;clear:left;">
			<p><a name="wpss_top"><h3><?php _e( 'Quick Navigation - Contents', 'wp-spamshield' ); ?></h3></a></p>
			<ol style="list-style-type:decimal;padding-left:30px;">
				<li><a href="#wpss_general_options"><?php _e('General Settings'); ?></a></li>
				<li><a href="#wpss_contact_form_options"><?php _e( 'Contact Form Settings', 'wp-spamshield' ); ?></a></li>
				<li><a href="<?php echo rs_wpss_append_url( WPSS_HOME_URL.'installation-instructions/' ); ?>" target="_blank" rel="external" ><?php _e( 'Installation Instructions', 'wp-spamshield' ); ?></a></li>
				<li><a href="<?php echo rs_wpss_append_url( WPSS_HOME_URL.'displaying-stats/' ); ?>" target="_blank" rel="external" ><?php _e( 'Displaying Spam Stats on Your Blog', 'wp-spamshield' ); ?></a></li>
				<li><a href="<?php echo rs_wpss_append_url( WPSS_HOME_URL.'installing-contact-form/' ); ?>" target="_blank" rel="external" ><?php _e( 'Adding a Contact Form to Your Blog', 'wp-spamshield' ); ?></a></li>
				<li><a href="<?php echo rs_wpss_append_url( WPSS_HOME_URL.'configuration/' ); ?>" target="_blank" rel="external" ><?php _e( 'Configuration Information', 'wp-spamshield' ); ?></a></li>
				<li><a href="<?php echo rs_wpss_append_url( WPSS_HOME_URL.'known-conflicts/' ); ?>" target="_blank" rel="external" ><?php _e( 'Known Plugin Conflicts', 'wp-spamshield' ); ?></a></li>
				<li><a href="<?php echo rs_wpss_append_url( WPSS_HOME_URL.'troubleshooting-guide/' ); ?>" target="_blank" rel="external" ><?php echo rs_wpss_tsg_txt() . ' / '. __( 'Support', 'wp-spamshield' ); ?></a></li>
				<li><a href="#wpss_let_others_know"><?php _e( 'Let Others Know About WP-SpamShield', 'wp-spamshield' ); ?></a></li>
				<li><a href="#wpss_download_plugin_documentation"><?php echo rs_wpss_doc_txt(); ?></a></li>
			</ol>
			</div>
			<div style="width:375px;height:<?php echo $wpss_info_box_height; ?>px;border-style:solid;border-width:1px;border-color:#003366;background-color:#DDEEFF;padding:0px 15px 0px 15px;margin-top:<?php echo $wpss_vert_margins; ?>px;margin-right:<?php echo $wpss_horz_margins; ?>px;float:left;">
			<p>
			<?php if( $spam_count_raw > 100 ) { ?>
			<a name="wpss_rate"><h3><?php _e( 'Happy with WP-SpamShield?', 'wp-spamshield' ); ?></h3></a></p>
			<p><img src='data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGMAAAATCAYAAACEGbNUAAABEUlEQVR42u3YPw4BQRzFcR0lDZXWGSRalUriBs7gCBqFVqnYzg0kOrVKJXrZcpYI4v8rXrGZYq3sTJbkTfJt1vw+W4g12YLWv61BEMjL2SPURgbN5OXoEVujF7qgirwcPEItZIhd0RRX5eXhYXhJiAUPVJTn0ePgDkVWxsIuvG43kefQ47PtzqHUcaYEQZ4Lj1gZhV9CB9SgIM+VR7CKbikhg0aclOfaI1hDpw/QPvack+fLI7hN+fOSl9nL/s2e0RC75fnyiPXRMQljK+yW58sjtrCGIrThDZ72kUyeT4+nAQ6HqMPrdTRHJvYn1MMn8lx7HGhy8wONE/ZsUMQ3kfJ8eWleZnFfl8czeVk8rd9cbylr4NQQ1Ok4AAAAAElFTkSuQmCC' alt='' width='99' height='19' align='right' style='border-style:none;padding:3px 0 20px 20px;float:right;' /><a href="<?php echo WPSS_WP_RATING_URL; ?>" target="_blank" rel="external" ><?php _e( 'Let others know by giving it a good rating on WordPress.org!', 'wp-spamshield' ); ?></a><br /><br />
			<?php } ?>

			<strong><?php echo rs_wpss_doc_txt(); ?>:</strong> <a href="<?php echo rs_wpss_append_url( WPSS_HOME_URL ); ?>" target="_blank" rel="external" ><?php echo rs_wpss_plug_hmpage_txt(); ?></a><br />
			<strong><?php _e( 'Tech Support', 'wp-spamshield' ); ?>:</strong> <a href="<?php echo rs_wpss_append_url( WPSS_SUPPORT_URL ); ?>" target="_blank" rel="external" ><?php _e( 'WP-SpamShield Support', 'wp-spamshield' ); ?></a><br />
			<strong><?php _e( 'Follow on Twitter', 'wp-spamshield' ); ?>:</strong> <a href="https://twitter.com/WPSpamShield" target="_blank" rel="external" >@WPSpamShield</a><br />
			<strong><?php _e( 'Let Others Know', 'wp-spamshield' ); ?>:</strong> <a href="https://www.redsandmarketing.com/blog/wp-spamshield-wordpress-plugin-released/#comments" target="_blank" rel="external" ><?php _e('Leave a Comment'); ?></a><br />
            <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank" style="margin-top:10px;">
            <input type="hidden" name="cmd" value="_s-xclick">
            <input type="hidden" name="hosted_button_id" value="DFMTNHJEPFFUL">
            <input type="image" src="data:image/png;base64,R0lGODlhSgAVAPcmAP+sLf7iq/7gpf7ksf7en/+6Tf+vM7+LNv+xOu7bskBedhA+a/+0QN+aLo9/WHBuWxA+aoCQl0BfeXB+f2BhUc+TMn+Jg7+YU76zkZ+HVp6jmX+Nj97Qre+iKo6Xk56gke+yT/63R3+LiTBUdO7Tm1BdXs4HAkBfd+7ZrH+Khs+VON7MomB0fkBgeq6ojf7HbGBze765o87Bnp6hlf/s0M7Do/7Rhb62mjBKWxA7YjBUczBUcv64SmB2gp9+Qs7EqP/89jBTcY6Uif+lNEBedN+dNIBwSa6wov/NgtEQBY6Vjb+OO/7amP++Xf+3RlBpev7UjP/Ti6+QVb++r8+hUs6/mf/05P/CYNEOBc6+lN7Knf7epP+oLH+MjJ6fjVBrfmBmXf/05v/ryf61Rv/ZoCBJbv/it3BoTY6WkP/py//YnyBCX/+vOkBVYP+/Wf63S767qP7WjP65Tf/w2f/FZu/gwv/u0++kMVBsgmB1gP7hqmB4h//uzv7dnv/w2HCAhP7Qf66smf+mLf/boP6/WTBMYf7Jcv+uM//y2yBIba6unv/sz//itv+pNP+yP/7mt/+pJv/15//rzv7pvv/syP/dqv/46v/03//OhP/w0/+/Xv+xOAAzZswAAP+ZMwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACH5BAEAACYALAAAAABKABUAAAj/AE0IHGjCk8GDCBMqXMiwocOEBCMONLgoTKSLGC8CAZKxo8ePIEN+tJLGoMSJcyypXMmypcuXMGPKfGnH00lPfi7p3Mmzp8+fQIMKDYrIJkFPYjIpXcq0qdOnUKNKncrHaMFBlLJq3cqVUoSvEaZ0HUuWa52yaClFMeppktu3cONOgsOpbt09cvPqfXsEz96/k6DY9MRjy6PDiBMr9sBJw6MELTj9OBxjgwcOhznESKBhQ4LDnDdoSJBAEacWmH9Y/qy49WEbPAy+MTSgtu3buHtwqlE7EKcuA3SPWLCA9xdOEkYgH4CCuATkaOzOmMFp+AIUuLMP0ENIjsExIWwE/xhPvnx5HZzI3+Ak4gOnPwFWcMoTAP2HABDSyxAhI8CJ9CJwcoN8JwTgnhLmJRjACyGEYJAjDDDwQh8CVGjhhRVyooCFQnDixROcaJHhhpzsMKIAVbCgQBklCqAAJwJ0qEAKLHCSAoYYMuFGhAwYxAYCQDJARxwEFGmkkRhwMkGRJCQCAQlEcFJkFpzAkOSSV5IAQRAuuKAkAVsSYEGVFpSJwZFHAnIFkGwadIgBcMZpQAF01kmnA5w8cIEUhXDiQAEPcJIBCG1wcgGeGRSA6AV5glCCoFRwUkIBjD6gKBgg2EmnE3LKaZAgAIQq6qikUmAXJzkYEeodONSVgw8AmLCqQqyczNoqJ2twskQRdVEAwBl2wUrqsKJyMRgkyCar7LIVHODsActC0mwHyDZbLbTIHtBAB9pC0kC33h5AbbTkIsvWEJukq+667Lbr7rvwxitvuo1Y5UkTmuSr77789uvvvwAHLLAmVgnkCRKYJKzwwgw37PDDEEccccETqVHJxRhnrPHGHHfs8ccck0HxUZ6YIcnJKKNMAw0pt+zyyzDHDDMjJp1E8kM456wzQycFBAA7" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!" />
            <img alt="" border="0" src="data:image/png;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAUUAAAALAAAAAABAAEAAAICRAEAOw==" width="1" height="1" />
            </form>
			</p>
			<?php 
			echo '<p><strong><a href="'.WPSS_DONATE_URL.'" title="' . __( 'WP-SpamShield is provided for free.', 'wp-spamshield' ) . ' ' . __( 'If you like the plugin, consider a donation to help further its development.', 'wp-spamshield' ) . '" target="_blank" rel="external" >' . rs_wpss_donate_txt() . '</a></strong></p>';
			?>
			</div>
			<div style='width:797px;border-style:solid;border-width:1px;border-color:#333333;background-color:#FEFEFE;padding:0px 15px 0px 15px;margin-top:<?php echo $wpss_vert_margins; ?>px;margin-right:<?php echo $wpss_horz_margins; ?>px;float:left;clear:left;'>
			<p><a name="wpss_general_options"><h3><?php _e('General Settings' ); ?></h3></a></p>
			<form name="wpss_general_options" method="post">
			<input type="hidden" name="submitted_wpss_general_options" value="1" />
            <?php wp_nonce_field( 'wpss_update_general_options_token', 'ugo_tkn'); ?>

			<fieldset class="options">
				<ul style="list-style-type:none;padding-left:30px;">

					<li>
					<label for="comment_logging">
						<input type="checkbox" id="comment_logging" name="comment_logging" <?php echo ( ( TRUE == (int)(bool) $spamshield_options['comment_logging'] ) ? 'checked="checked" ' : '' ); ?>value="1" />
						<strong><?php echo __( 'Blocked Comment Logging Mode', 'wp-spamshield' );
						if( rs_wpss_is_lang_en_us() ) { echo ' &mdash; ' . __( 'See what spam has been blocked!', 'wp-spamshield' ); /* TO DO: TRANSLATE */ }
						?></strong><br /><?php _e( 'Temporary diagnostic mode that logs blocked comment submissions for 7 days, then turns off automatically.', 'wp-spamshield' ); ?><br /><?php _e( 'Log is cleared each time this feature is turned on.', 'wp-spamshield' ); ?>
					</label>
					<?php
					if( !empty( $spamshield_options['comment_logging'] ) ) {
						/* If comment logging is on, check file permissions and attempt to fix. Reset .htaccess file for data dir, to allow IP of current admin to view log file. Let user know if not set correctly. */
						$wpss_hta_reset = rs_wpss_log_reset( NULL, TRUE, TRUE );
						if( empty( $wpss_hta_reset ) ) {
							echo '<br />'.WPSS_EOL.'<span style="color:red;"><strong>' . sprintf( __( 'The log file may not be writeable. You may need to manually correct the file permissions.<br />Set the permission for the "%1$s" directory to "%2$s" and all files within it to "%3$s".</strong><br />If that doesn\'t work, then please read the <a href="%4$s" %5$s>FAQ</a> for this topic.', 'wp-spamshield' ), WPSS_PLUGIN_DATA_PATH, '755', '644', rs_wpss_append_url( WPSS_HOME_URL.'faqs/?faqs=5#faqs_5' ), 'target="_blank"' ) . '</span><br />'.WPSS_EOL;
						}
					} else { rs_wpss_log_reset( NULL, FALSE, FALSE, TRUE ); /* Create log file if it doesn't exist */ }
					$wpss_log_key	= rs_wpss_get_log_key();
					$wpss_log_filnm	= ( WP_SpamShield::is_mdbug() ) ? 'temp-comments-log.txt' : 'temp-comments-log-'.$wpss_log_key.'.txt';
					?>
					<br /><strong><a href="<?php echo WPSS_PLUGIN_DATA_URL.'/'.$wpss_log_filnm; ?>" target="_blank"><?php _e( 'Download Comment Log File', 'wp-spamshield' ); ?></a> - <?php _e( 'Right-click, and select "Save Link As"', 'wp-spamshield' ); ?></strong><br />&nbsp;
					</li>
					<li>
					<label for="comment_logging_all">
						<input type="checkbox" id="comment_logging_all" name="comment_logging_all" <?php echo ( ( TRUE == (int)(bool) $spamshield_options['comment_logging_all'] ) ? 'checked="checked" ' : '' ); ?>value="1" />
						<strong><?php _e( 'Log All Comments', 'wp-spamshield' ); ?></strong><br /><?php _e( 'Requires that Blocked Comment Logging Mode be engaged. Instead of only logging blocked comments, this will allow the log to capture all comments while logging mode is turned on. This provides more technical data for comment submissions than WordPress provides, and helps us improve the plugin.<br />If you plan on submitting spam samples to us for analysis, it\'s helpful for you to turn this on, otherwise it\'s not necessary.', 'wp-spamshield' ); ?></label>
					<br /><a href="<?php echo rs_wpss_append_url( WPSS_HOME_URL.'configuration/?cnf=log_all_comments#cnf_log_all_comments' ); ?>" target="_blank" rel="external" ><?php _e( 'For more about this, see the documentation.', 'wp-spamshield' ); ?></a><br />&nbsp;
					</li>
					<li>
					<label for="enhanced_comment_blacklist">
						<input type="checkbox" id="enhanced_comment_blacklist" name="enhanced_comment_blacklist" <?php echo ( ( TRUE == (int)(bool) $spamshield_options['enhanced_comment_blacklist'] ) ? 'checked="checked" ' : '' ); ?>value="1" />
						<strong><?php _e( 'Enhanced Comment Blacklist', 'wp-spamshield' ); ?></strong><br /><?php _e( 'Enhances WordPress\'s Comment Blacklist - instead of just sending comments to moderation, they will be completely blocked. Also adds a link in the comment notification emails that will let you blacklist a commenter\'s IP with one click.<br />(Useful if you receive repetitive human spam or harassing comments from a particular commenter.)', 'wp-spamshield' ); ?></label>
					<br /><a href="<?php echo rs_wpss_append_url( WPSS_HOME_URL.'configuration/?cnf=enhanced_comment_blacklist#cnf_enhanced_comment_blacklist' ); ?>" target="_blank" rel="external" ><?php _e( 'For more about this, see the documentation.', 'wp-spamshield' ); ?></a><br />&nbsp;
					</li>
					<label for="wordpress_comment_blacklist">
						<strong><?php _e( 'Your current WordPress Comment Blacklist', 'wp-spamshield' ); ?></strong><br /><?php _e( 'When a comment contains any of these words in its content, name, URL, e-mail, or IP, it will be completely blocked, not just marked as spam. One word or IP per line. It is not case-sensitive and will match included words, so "press" on your blacklist will block "WordPress" in a comment.', 'wp-spamshield' ); ?><br />
						<textarea id="wordpress_comment_blacklist" name="wordpress_comment_blacklist" cols="80" rows="8" /><?php echo esc_textarea( $wordpress_comment_blacklist ); ?></textarea><br />
					</label>
					<?php _e( 'You can update this list here.', 'wp-spamshield' ); ?> <a href="<?php echo WPSS_ADMIN_URL; ?>/options-discussion.php"><?php _e( 'You can also update it on the WordPress Discussion Settings page.', 'wp-spamshield' ); ?></a><br />&nbsp;
					<li>
					<label for="enable_whitelist">
						<input type="checkbox" id="enable_whitelist" name="enable_whitelist" <?php echo ( ( TRUE == (int)(bool) $spamshield_options['enable_whitelist'] ) ? 'checked="checked" ' : '' ); ?>value="1" />
						<strong><?php _e( 'Enable WP-SpamShield Whitelist', 'wp-spamshield' ); ?></strong><br /><?php _e( 'Enables WP-SpamShield\'s Whitelist - for all form/POST submission channels that the plugin protects. When a submission is received from an e-mail or IP address on the whitelist, it will bypass spam filters and be allowed through.<br />(Useful if you have specific users that you want to let bypass the filters.)', 'wp-spamshield' ); ?></label>
					<br /><a href="<?php echo rs_wpss_append_url( WPSS_HOME_URL.'configuration/?cnf=enable_whitelist#cnf_enable_whitelist' ); ?>" target="_blank" rel="external" ><?php _e( 'For more about this, see the documentation.', 'wp-spamshield' ); ?></a><br />&nbsp;
					</li>
					<label for="wpss_whitelist">
						<strong><?php _e( 'Your current WP-SpamShield Whitelist', 'wp-spamshield' ); ?></strong><br /><?php _e( 'One email or IP address per line. Each entry must be a valid and complete email or IP address, like <em>user@yourwebsite.com</em> or <em>11.22.33.44</em>. It is not case-sensitive and will only make <em>exact matches</em>, not partial matches.', 'wp-spamshield' ); ?><br />
						<textarea id="wpss_whitelist" name="wpss_whitelist" cols="80" rows="8" /><?php echo esc_textarea( $wpss_whitelist ); ?></textarea><br />&nbsp;
					</label>
					<li>
					<label for="comment_min_length">
			<?php
			$comment_min_length = (int) WP_SpamShield::sanitize_string( $spamshield_options['comment_min_length'] );
			$comment_min_length = ( !empty( $comment_min_length ) && $comment_min_length <= 30 ) ? $comment_min_length : $wpss_options_default['comment_min_length'];
			?>
						<input type="number" size="4" id="comment_min_length" name="comment_min_length" value="<?php echo $comment_min_length; ?>" min="1" max="30" step="1" />
						<strong><?php echo sprintf( __( 'Minimum comment length (# of characters). (Minimum %1$s, Default %2$s)', 'wp-spamshield' ), '1', $wpss_options_default['comment_min_length'] ); ?></strong><br />&nbsp;
					</label>
					</li>
			<?php
			$boolean_options_go =
				array(
					array( 'block_all_trackbacks',			__( 'Disable trackbacks.', 'wp-spamshield' ),									__( 'Use if trackback spam is excessive. (Not recommended)', 'wp-spamshield' ) ),
					array( 'block_all_pingbacks',			__( 'Disable pingbacks.', 'wp-spamshield' ),									__( 'Use if pingback spam is excessive. Disadvantage is reduction of communication between blogs. (Not recommended)', 'wp-spamshield' ) ),
					array( 'allow_proxy_users',				__( 'Allow users behind proxy servers to comment?', 'wp-spamshield' ),			__( 'Many human spammers hide behind proxies, so you can uncheck this option for extra protection. (For highest user compatibility, leave it checked.)', 'wp-spamshield' ) ),
					array( 'hide_extra_data',				__( 'Hide extra technical data in comment notifications.', 'wp-spamshield' ),	__( 'This data is helpful if you need to submit a spam sample. If you dislike seeing the extra info, you can use this option.', 'wp-spamshield' ) ),
					array( 'registration_shield_disable',	__( 'Disable Registration Spam Shield.', 'wp-spamshield' ),						__( 'This option will disable the anti-spam shield for the WordPress registration form only. While not recommended, this option is available if you need it. Anti-spam will still remain active for comments, pingbacks, trackbacks, and contact forms.', 'wp-spamshield' ) ),
					array( 'disable_cf7_shield',			__( 'Disable anti-spam for Contact Form 7.', 'wp-spamshield' ),					__( 'This option will disable anti-spam protection for Contact Form 7 forms.', 'wp-spamshield' ) ),
					array( 'disable_gf_shield',				__( 'Disable anti-spam for Gravity Forms.', 'wp-spamshield' ),					__( 'This option will disable anti-spam protection for Gravity Forms.', 'wp-spamshield' ) ),
					array( 'disable_misc_form_shield',		__( 'Disable anti-spam for miscellaneous forms.', 'wp-spamshield' ),			__( 'This option will disable anti-spam protection for custom and miscellaneous forms on your site. (All forms that are not from WP-SpamShield, Contact Form 7, or Gravity Forms.)', 'wp-spamshield' ) . ' ' . __( 'NOTE: The anti-spam for miscellaneous forms feature also protects your site from many XML-RPC based attacks, such as brute force amplification attacks, so we recommend that you do not disable it.', 'wp-spamshield' ) ),
					array( 'disable_email_encode',			__( 'Disable email harvester protection.', 'wp-spamshield' ),					__( 'This option will disable the automatic encoding of email addresses and mailto links in your website content.', 'wp-spamshield' ) ),
					array( 'allow_comment_author_keywords',	__( 'Allow Keywords in Comment Author Names.', 'wp-spamshield' ),				sprintf( __( 'This will allow some keywords to be used in comment author names. By default, WP-SpamShield blocks many common spam keywords from being used in the comment "%1$s" field. This option is useful for sites with users that use pseudonyms, or for sites that simply want to allow business names and keywords to be used in the comment "%2$s" field. This option is not recommended, as it can potentially allow more human spam, but it is available if you choose. Your site will still be protected against all automated comment spam.', 'wp-spamshield' ), __( 'Name' ), __( 'Name' ) ) ),
					array( 'auto_update_plugin',			__( 'Enable Automatic Updates.', 'wp-spamshield' ),								__( 'WP-SpamShield can perform automatic updates using the WordPress plugin update API. Being that WP-SpamShield is an anti-spam and security plugin, just like an anti-virus, anti-malware or other security program on your computer, automatic updates are extremely important. (We recommend keeping this enabled.)', 'wp-spamshield' ) ),
					array( 'auto_purge_cache',				__( 'Enable Automatic Cache Purge.', 'wp-spamshield' ),							__( 'When plugins are activated, deactivated, or updated, WP-SpamShield automatically purges the page cache for a number of caching plugins, including WP Super Cache, WP Fastest Cache, and more. This ensures the smooth operation of your site and anti-spam functionality. (We recommend keeping this enabled.)', 'wp-spamshield' ) ),
					array( 'disable_security_alerts',		__( 'Disable Security Alerts.', 'wp-spamshield' ),								__( 'WP-SpamShield periodically checks the WPScan Vulnerability Database and alerts you if the version of WordPress installed on your site has any known security vulnerabilities and needs to be updated.', 'wp-spamshield' ) ),
					array( 'promote_plugin_link',			__( 'Help promote WP-SpamShield?', 'wp-spamshield' ),							__( 'This places a small link under the comments and contact form, letting others know what\'s blocking spam on your blog.', 'wp-spamshield' ) ),
				);
			foreach( $boolean_options_go as $i => $v ) {
				if( 'auto_update_plugin' === $v[0] && defined( 'WPSS_AUTOUP_DISABLE' ) && TRUE === WPSS_AUTOUP_DISABLE ) { continue; }
				$checked = ( TRUE == (int)(bool) $spamshield_options[$v[0]] ) ? 'checked="checked" ' : '';
				echo "\t\t\t\t\t".'<li>'.WPSS_EOL."\t\t\t\t\t".'<label for="'.$v[0].'">'.WPSS_EOL."\t\t\t\t\t\t".'<input type="checkbox" id="'.$v[0].'" name="'.$v[0].'" '.$checked.'value="1" />'.WPSS_EOL."\t\t\t\t\t\t".'<strong>'.$v[1].'</strong><br />'.$v[2].'<br />&nbsp;'.WPSS_EOL."\t\t\t\t\t".'</label>'.WPSS_EOL."\t\t\t\t\t".'</li>'.WPSS_EOL;
			}
			?>
				</ul>
			</fieldset>
			<p class="submit">
			<input type="submit" name="submit_wpss_general_options" value="<?php _e( 'Save Changes' ); ?>" class="button-primary" style="float:left;" />
			</p>
			</form>
			<p>&nbsp;</p>
			<p><div style="float:right;font-size:12px;">[ <a href="#wpss_top"><?php _e( 'BACK TO TOP', 'wp-spamshield' ); ?></a> ]</div></p>
			<p>&nbsp;</p>
			</div>
			<div style='width:797px;border-style:solid;border-width:1px;border-color:#003366;background-color:#DDEEFF;padding:0px 15px 0px 15px;margin-top:<?php echo $wpss_vert_margins; ?>px;margin-right:<?php echo $wpss_horz_margins; ?>px;float:left;clear:left;'>
			<p><a name="wpss_contact_form_options"><h3><?php _e( 'Contact Form Settings', 'wp-spamshield' ); ?></h3></a></p>
			<form name="wpss_contact_options" method="post">
			<input type="hidden" name="submitted_wpss_contact_options" value="1" />
            <?php wp_nonce_field( 'wpss_update_contact_options_token', 'uco_tkn' ); ?>

			<fieldset class="options">
				<ul style="list-style-type:none;padding-left:30px;">
			<?php
			$boolean_options_co = array(
				array( 'form_include_website',			__( 'Include "Website" field.', 'wp-spamshield' ) ),
				array( 'form_require_website',			__( 'Require "Website" field.', 'wp-spamshield' ) ),
				array( 'form_include_phone',			__( 'Include "Phone" field.', 'wp-spamshield' ) ),
				array( 'form_require_phone',			__( 'Require "Phone" field.', 'wp-spamshield' ) ),
				array( 'form_include_company',			__( 'Include "Company" field.', 'wp-spamshield' ) ),
				array( 'form_require_company',			__( 'Require "Company" field.', 'wp-spamshield' ) ),
				array( 'form_include_drop_down_menu',	__( 'Include drop-down menu select field.', 'wp-spamshield' ) ),
				array( 'form_require_drop_down_menu',	__( 'Require drop-down menu select field.', 'wp-spamshield' ) ),
			);
			foreach( $boolean_options_co as $i => $v ) {
				$checked = ( TRUE == $spamshield_options[$v[0]] ) ? 'checked="checked" ' : '';
				echo "\t\t\t\t\t".'<li>'.WPSS_EOL."\t\t\t\t\t".'<label for="'.$v[0].'">'.WPSS_EOL."\t\t\t\t\t\t".'<input type="checkbox" id="'.$v[0].'" name="'.$v[0].'" '.$checked.'value="1" />'.WPSS_EOL."\t\t\t\t\t\t".'<strong>'.$v[1].'</strong><br />&nbsp;'.WPSS_EOL."\t\t\t\t\t".'</label>'.WPSS_EOL."\t\t\t\t\t".'</li>'.WPSS_EOL;
			}
			$text_options_co = array(
				array( 'form_drop_down_menu_title',		'Title of drop-down select menu. (Menu won\'t be shown if empty.)' ),
				array( 'form_drop_down_menu_item_1',	'Drop-down select menu item 1. (Menu won\'t be shown if empty.)' ),
				array( 'form_drop_down_menu_item_1',	'Drop-down select menu item 1. (Leave blank if not using.)' ),
				);
			$i = 0;
			while( $i <= 10 ) {
				$k = ( $i == 0 ) ? 0 : 1; $k = ( $i >= 3 ) ? 2 : $k; $v = $text_options_co[$k];
				$v[0] = str_replace( '1', $i, $v[0] ); $v[1] = __( str_replace( '1', $i, $v[1] ), WPSS_PLUGIN_NAME );
				$value = WP_SpamShield::sanitize_opt_string( $spamshield_options[$v[0]] );
				if( empty( $value ) ) { $value = ''; }
				echo "\t\t\t\t\t".'<li>'.WPSS_EOL."\t\t\t\t\t".'<label for="'.$v[0].'">'.WPSS_EOL."\t\t\t\t\t\t".'<input type="text" size="40" id="'.$v[0].'" name="'.$v[0].'" value="'.$value.'" />'.WPSS_EOL."\t\t\t\t\t\t".'<strong>'.$v[1].'</strong><br />&nbsp;'.WPSS_EOL."\t\t\t\t\t".'</label>'.WPSS_EOL."\t\t\t\t\t".'</li>'.WPSS_EOL;
				++$i;
			}
			?>
					<li>
					<label for="form_message_width">
						<?php
						$form_message_width = (int) WP_SpamShield::sanitize_string( $spamshield_options['form_message_width'] );
						$form_message_width = ( empty( $form_message_width ) || $form_message_width < $wpss_options_default['form_message_width'] ) ? $wpss_options_default['form_message_width'] : $form_message_width;
						?>
						<input type="number" size="4" id="form_message_width" name="form_message_width" value="<?php echo $form_message_width; ?>" min="<?php echo $wpss_options_default['form_message_width']; ?>" max="400" step="1" />
						<strong><?php echo sprintf( __( '"Message" field width. (Minimum %s)', 'wp-spamshield' ), $wpss_options_default['form_message_width'] ); ?></strong><br />&nbsp;
					</label>
					</li>
					<li>
					<label for="form_message_height">
						<?php
						$form_message_height = (int) WP_SpamShield::sanitize_string( $spamshield_options['form_message_height'] );
						if( empty( $form_message_height ) ) { $form_message_height = $wpss_options_default['form_message_height']; }
						$form_message_height = ( $form_message_height < 5 ) ? 5 : $form_message_height;
						?>
						<input type="number" size="4" id="form_message_height" name="form_message_height" value="<?php echo $form_message_height; ?>" min="5" max="100" step="1" />
						<strong><?php echo sprintf( __( '"Message" field height. (Minimum %1$s, Default %2$s)', 'wp-spamshield' ), '5', $wpss_options_default['form_message_height'] ); ?></strong><br />&nbsp;
					</label>
					</li>
					<li>
					<label for="form_message_min_length">
						<?php
						$form_message_min_length = (int) WP_SpamShield::sanitize_string( $spamshield_options['form_message_min_length'] );
						if( empty( $form_message_min_length ) ) { $form_message_min_length = $wpss_options_default['form_message_min_length']; }
						$form_message_min_length = ( $form_message_min_length < 15 ) ? 15 : $form_message_min_length;
						?>
						<input type="number" size="4" id="form_message_min_length" name="form_message_min_length" value="<?php echo $form_message_min_length; ?>" min="15" max="150" step="1" />
						<strong><?php echo sprintf( __( 'Minimum message length (# of characters). (Minimum %1$s, Default %2$s)', 'wp-spamshield' ), '15', $wpss_options_default['form_message_min_length'] ); ?></strong><br />&nbsp;
					</label>
					</li>
					<li>
					<label for="form_message_recipient">
						<?php
						$form_message_recipient = WP_SpamShield::sanitize_string( $spamshield_options['form_message_recipient'] );
						$form_message_recipient = ( empty( $form_message_recipient ) || !is_email( $form_message_recipient ) ) ? $admin_email : $form_message_recipient;
						?>
						<input type="email" size="40" id="form_message_recipient" name="form_message_recipient" value="<?php echo $form_message_recipient; ?>" />
						<strong><?php _e( 'Optional: Enter alternate form recipient. Default is blog admin email.', 'wp-spamshield' ); ?></strong><br />&nbsp;
					</label>
					</li>
					<li>
					<label for="form_response_thank_you_message">
						<?php 
						$form_response_thank_you_message = esc_textarea( $spamshield_options['form_response_thank_you_message'] );
						_e( '<strong>Enter message to be displayed upon successful contact form submission.</strong><br />Can be plain text, HTML, or an ad, etc.', 'wp-spamshield' );
						?><br />
						<textarea id="form_response_thank_you_message" name="form_response_thank_you_message" cols="80" rows="3" /><?php if( empty( $form_response_thank_you_message ) ) { _e( 'Your message was sent successfully. Thank you.', 'wp-spamshield' ); } else { echo $form_response_thank_you_message; } ?></textarea><br />&nbsp;
					</label>
					</li>
					<li>
					<label for="form_include_user_meta">
						<input type="checkbox" id="form_include_user_meta" name="form_include_user_meta" <?php echo ( ( TRUE == (int)(bool) $spamshield_options['form_include_user_meta'] ) ? 'checked="checked" ' : '' ); ?>value="1" />
						<strong><?php _e( 'Include user technical data in email.', 'wp-spamshield' ); ?></strong><br /><?php _e( 'This adds some extra technical data to the end of the contact form email about the person submitting the form.<br />It includes: <strong>Browser / User Agent</strong>, <strong>Referrer</strong>, <strong>IP Address</strong>, <strong>Server</strong>, etc.<br />This is helpful for dealing with abusive or threatening comments. You can use the IP address provided to identify or block trolls from your site with whatever method you prefer.', 'wp-spamshield' ); ?><br />&nbsp;
					</label>
					</li>
					<li>
					<label for="form_mail_encode">
						<input type="checkbox" id="form_mail_encode" name="form_mail_encode" <?php echo ( ( TRUE == (int)(bool) $spamshield_options['form_mail_encode'] ) ? 'checked="checked" ' : '' ); ?>value="1" />
						<strong><?php _e( 'Use base64 encoding for email content.', 'wp-spamshield' ); ?></strong><br /><?php _e( 'This encodes the content of the WP-SpamShield contact form email using base64. Emails are normally sent in plain text.<br />Email apps can read base64 content or plain text so this only affects how it is transmitted.', 'wp-spamshield' ); ?><br />&nbsp;
					</label>
					</li>

				</ul>
			</fieldset>
			<p class="submit">
			<input type="submit" name="submit_wpss_contact_options" value="<?php _e( 'Save Changes' ); ?>" class="button-primary" style="float:left;" />
			</p>
			</form>
			<p>&nbsp;</p>
			<p><div style="float:right;font-size:12px;">[ <a href="#wpss_top"><?php _e( 'BACK TO TOP', 'wp-spamshield' ); ?></a> ]</div></p>
			<p>&nbsp;</p>
			</div>
			<div style='width:797px;border-style:solid;border-width:1px;border-color:#333333;background-color:#FEFEFE;padding:0px 15px 0px 15px;margin-top:<?php echo $wpss_vert_margins; ?>px;margin-right:<?php echo $wpss_horz_margins; ?>px;float:left;clear:left;'>
  			<p><a name="wpss_let_others_know"><h3><?php _e( 'Let Others Know About WP-SpamShield', 'wp-spamshield' ); ?></h3></a></p>
			<p><?php _e( '<strong>How does it feel to blog without being bombarded by automated comment spam?</strong> If you\'re happy with WP-SpamShield, there\'s a few things you can do to let others know:', 'wp-spamshield' ); ?></p>
			<ul style="list-style-type:disc;padding-left:30px;">
				<li><a href="https://www.redsandmarketing.com/blog/wp-spamshield-wordpress-plugin-released/#comments" target="_blank" rel="external" ><?php _e('Leave a Comment'); ?></a></li>
				<li><a href="<?php echo WPSS_WP_RATING_URL; ?>" target="_blank" rel="external" ><?php _e( 'Give WP-SpamShield a good rating on WordPress.org.', 'wp-spamshield' ); ?></a></li>
				<li><a href="<?php echo rs_wpss_append_url( WPSS_HOME_URL.'end-blog-spam/' ); ?>" target="_blank" rel="external" ><?php _e( 'Place a graphic link on your site.', 'wp-spamshield' ); ?></a> <?php _e( 'Let others know how they can help end blog spam.', 'wp-spamshield' ); ?> ( &lt;/BLOGSPAM&gt; )</li>
			</ul>
			<p><a href="<?php echo rs_wpss_append_url( WPSS_HOME_URL ); ?>" style="border-style:none;text-decoration:none;" target="_blank" rel="external" ><img src="<?php echo WPSS_PLUGIN_IMG_URL; ?>/end-blog-spam-button-01-black.png" alt="End Blog Spam! WP-SpamShield Comment Spam Protection for WordPress" width="140" height="66" style="border-style:none;text-decoration:none;margin-top:15px;margin-left:15px;" /></a></p>
			<p><div style="float:right;font-size:12px;">[ <a href="#wpss_top"><?php _e( 'BACK TO TOP', 'wp-spamshield' ); ?></a> ]</div></p>
			<p>&nbsp;</p>
			</div>
			<div style='width:797px;border-style:solid;border-width:1px;border-color:#003366;background-color:#DDEEFF;padding:0px 15px 0px 15px;margin-top:<?php echo $wpss_vert_margins; ?>px;margin-right:<?php echo $wpss_horz_margins; ?>px;float:left;clear:left;'>
			<p><a name="wpss_download_plugin_documentation"><h3><?php echo rs_wpss_doc_txt(); ?></h3></a></p>
			<p><?php echo rs_wpss_plug_hmpage_txt() . ' / ' . rs_wpss_doc_txt(); ?>: <a href="<?php echo rs_wpss_append_url( WPSS_HOME_URL ); ?>" target="_blank" rel="external" >WP-SpamShield</a><br />
			<?php _e('Leave a Comment'); ?>: <a href="https://www.redsandmarketing.com/blog/wp-spamshield-wordpress-plugin-released/" target="_blank" rel="external" ><?php _e( 'WP-SpamShield Release Announcement Blog Post', 'wp-spamshield' ); ?></a><br />
			<?php echo rs_wpss_plug_wppage_txt(); ?>: <a href="<?php echo WPSS_WP_URL; ?>" target="_blank" rel="external" >WP-SpamShield</a><br />
			<?php _e( 'Tech Support / Questions', 'wp-spamshield' ); ?>: <a href="<?php echo rs_wpss_append_url( WPSS_SUPPORT_URL ); ?>" target="_blank" rel="external" ><?php _e( 'WP-SpamShield Support Page', 'wp-spamshield' ); ?></a><br />
			<?php _e( 'End Blog Spam', 'wp-spamshield' ); ?>: <a href="<?php echo rs_wpss_append_url( WPSS_HOME_URL.'end-blog-spam/' ); ?>" target="_blank" rel="external" ><?php _e( 'Let Others Know About WP-SpamShield', 'wp-spamshield' ); ?>!</a><br />
			Twitter: <a href="https://twitter.com/WPSpamShield" target="_blank" rel="external" >@WPSpamShield</a><br />
			<?php 
			if( rs_wpss_is_lang_en_us() ) {
				echo 'Need WordPress Consulting? <a href="https://www.redsandmarketing.com/web-design/wordpress-consulting/" target="_blank" rel="external" >We can help.</a><br />';
			}
			?>
            <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank" style="margin-top:10px;">
            <input type="hidden" name="cmd" value="_s-xclick">
            <input type="hidden" name="hosted_button_id" value="DFMTNHJEPFFUL">
            <input type="image" src="data:image/png;base64,R0lGODlhSgAVAPcmAP+sLf7iq/7gpf7ksf7en/+6Tf+vM7+LNv+xOu7bskBedhA+a/+0QN+aLo9/WHBuWxA+aoCQl0BfeXB+f2BhUc+TMn+Jg7+YU76zkZ+HVp6jmX+Nj97Qre+iKo6Xk56gke+yT/63R3+LiTBUdO7Tm1BdXs4HAkBfd+7ZrH+Khs+VON7MomB0fkBgeq6ojf7HbGBze765o87Bnp6hlf/s0M7Do/7Rhb62mjBKWxA7YjBUczBUcv64SmB2gp9+Qs7EqP/89jBTcY6Uif+lNEBedN+dNIBwSa6wov/NgtEQBY6Vjb+OO/7amP++Xf+3RlBpev7UjP/Ti6+QVb++r8+hUs6/mf/05P/CYNEOBc6+lN7Knf7epP+oLH+MjJ6fjVBrfmBmXf/05v/ryf61Rv/ZoCBJbv/it3BoTY6WkP/py//YnyBCX/+vOkBVYP+/Wf63S767qP7WjP65Tf/w2f/FZu/gwv/u0++kMVBsgmB1gP7hqmB4h//uzv7dnv/w2HCAhP7Qf66smf+mLf/boP6/WTBMYf7Jcv+uM//y2yBIba6unv/sz//itv+pNP+yP/7mt/+pJv/15//rzv7pvv/syP/dqv/46v/03//OhP/w0/+/Xv+xOAAzZswAAP+ZMwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACH5BAEAACYALAAAAABKABUAAAj/AE0IHGjCk8GDCBMqXMiwocOEBCMONLgoTKSLGC8CAZKxo8ePIEN+tJLGoMSJcyypXMmypcuXMGPKfGnH00lPfi7p3Mmzp8+fQIMKDYrIJkFPYjIpXcq0qdOnUKNKncrHaMFBlLJq3cqVUoSvEaZ0HUuWa52yaClFMeppktu3cONOgsOpbt09cvPqfXsEz96/k6DY9MRjy6PDiBMr9sBJw6MELTj9OBxjgwcOhznESKBhQ4LDnDdoSJBAEacWmH9Y/qy49WEbPAy+MTSgtu3buHtwqlE7EKcuA3SPWLCA9xdOEkYgH4CCuATkaOzOmMFp+AIUuLMP0ENIjsExIWwE/xhPvnx5HZzI3+Ak4gOnPwFWcMoTAP2HABDSyxAhI8CJ9CJwcoN8JwTgnhLmJRjACyGEYJAjDDDwQh8CVGjhhRVyooCFQnDixROcaJHhhpzsMKIAVbCgQBklCqAAJwJ0qEAKLHCSAoYYMuFGhAwYxAYCQDJARxwEFGmkkRhwMkGRJCQCAQlEcFJkFpzAkOSSV5IAQRAuuKAkAVsSYEGVFpSJwZFHAnIFkGwadIgBcMZpQAF01kmnA5w8cIEUhXDiQAEPcJIBCG1wcgGeGRSA6AV5glCCoFRwUkIBjD6gKBgg2EmnE3LKaZAgAIQq6qikUmAXJzkYEeodONSVgw8AmLCqQqyczNoqJ2twskQRdVEAwBl2wUrqsKJyMRgkyCar7LIVHODsActC0mwHyDZbLbTIHtBAB9pC0kC33h5AbbTkIsvWEJukq+667Lbr7rvwxitvuo1Y5UkTmuSr77789uvvvwAHLLAmVgnkCRKYJKzwwgw37PDDEEccccETqVHJxRhnrPHGHHfs8ccck0HxUZ6YIcnJKKNMAw0pt+zyyzDHDDMjJp1E8kM456wzQycFBAA7" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!" />
            <img alt="" border="0" src="data:image/png;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAUUAAAALAAAAAABAAEAAAICRAEAOw==" width="1" height="1" />
            </form>
			</p>
			<?php 
			echo '<p><strong><a href="'.WPSS_DONATE_URL.'" target="_blank" rel="external" >' . rs_wpss_donate_txt() . '</a></strong><br />' . __( 'WP-SpamShield is provided for free.', 'wp-spamshield' ) . ' ' . __( 'If you like the plugin, consider a donation to help further its development.', 'wp-spamshield' ) . '<br />
			<strong>Donate with <a href="https://www.redsandmarketing.com/go/donate/wp-spamshield/" rel="nofollow external" target="_blank">PayPal</a></strong><br />
			<strong>Bitcoin:</strong>&nbsp; 1PYyD8Fu9DBV9gj4chYkfDDa77fSHMFTfm<br />
			<strong>Litecoin:</strong>&nbsp; LMAw3ugXsVMSobbJjeTP4WYFjvXT3Ku9hr<br />
			<strong>Ethereum:</strong>&nbsp; 0xB76085AFb961C5c4562c7Abe92C34DEAd44f258d</p>';
			?>

			<p><div style="float:right;font-size:12px;">[ <a href="#wpss_top"><?php _e( 'BACK TO TOP', 'wp-spamshield' ); ?></a> ]</div></p>
			<p>&nbsp;</p>
			</div>

			<?php
			/**
			 *  Recommended Partners
			 *	@removed 1.9.9.5
			 */
			?>
			<p style="clear:both;">&nbsp;</p>
			<p style="clear:both;"><em><?php 
			$this->settings_ver_ftr();
			?></em></p>
			<p><div style="float:right;clear:both;font-size:12px;">[ <a href="#wpss_top"><?php _e( 'BACK TO TOP', 'wp-spamshield' ); ?></a> ]</div></p>
			<p>&nbsp;</p>
			</div>
			<?php

/** END **/
