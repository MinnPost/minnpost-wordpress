<?php
/*
Plugin Name: MinnPost Roles and Capabilities
Description: Set all roles and capabilities for MinnPost access. This replaces the AAM plugin for us.
Version: 0.0.1
Author: Jonathan Stegall
Author URI: https://code.minnpost.com
Text Domain: minnpost-roles-and-capabilities
License: GPL2+
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

class Minnpost_Roles_And_Capabilities {

	/**
	 * @var string
	 * The plugin version
	*/
	private $version;

	/**
	 * @var object
	 * Static property to hold an instance of the class; this seems to make it reusable
	 *
	 */
	static $instance = null;

	/**
	 * Load the static $instance property that holds the instance of the class.
	 * This instance makes the class reusable by other plugins
	*
	 * @return object
	*
	*/
	static public function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new Minnpost_Roles_And_Capabilities();
		}
		return self::$instance;
	}

	/**
	 * This is our constructor
	 *
	 * @return void
	 */
	public function __construct() {

		$this->version = '0.0.1';
		$this->slug    = 'minnpost-roles-and-capabilities';

		$this->add_actions();

	}

	private function add_actions() {
		// setup roles
		register_activation_hook( __FILE__, array( $this, 'user_roles' ) );
		// going to need a way to refresh these manually like below
		add_action( 'admin_init', array( $this, 'user_roles' ) );
		add_action( 'init', array( $this, 'disallow_banned_user_comments' ), 10 );
		//add_action( 'admin_init', array( $this, 'test_capabilities' ) ); // temp method
	}

	/* temporary method */
	public function test_capabilities() {
		$data = get_userdata( get_current_user_id() );
		if ( is_object( $data ) ) {
			$current_user_caps = $data->allcaps;
			// print it to the screen
			echo '<pre>' . print_r( $current_user_caps, true ) . '</pre>';
		}
	}

	/**
	 * Create roles if they don't exist, and assign capabilities to all roles.
	 *
	*/
	public function user_roles() {

		$existing_roles = array();

		// add new roles and assign capabilities to them
		$extra_user_roles = $this->get_extra_user_roles();
		foreach ( $extra_user_roles as $role => $display_name ) {
			$result = add_role(
				$role,
				$display_name,
				$this->bundle_capabilities( $role )
			);
			if ( null === $result ) {
				// this role already exists, but let's make sure it has the right capabilities. add it to array of existing roles.
				$result                  = get_role( $role );
				$existing_roles[ $role ] = $result;
			} else {
				$result                          = get_role( $role );
				$existing_roles[ $result->name ] = $result;
			}
		}

		// assign core roles to existing role array
		$core_roles = $this->get_core_roles();
		foreach ( $core_roles as $role ) {
			$result                          = get_role( $role );
			$existing_roles[ $result->name ] = $result;
		}

		// assign capabilities to existing WordPress roles
		foreach ( $existing_roles as $name => $role ) {
			if ( is_object( $role ) ) {
				$capabilities = $this->bundle_capabilities( $name );
				foreach ( $capabilities as $capability ) {
					$role->add_cap( $capability );
				}
			}
		}
	}

	/**
	 * Detect whether a user has been banned. These users cannot comment.
	 * This depends on the 'banned' user role.
	*/
	public function disallow_banned_user_comments() {
		$user = wp_get_current_user();
		if ( ! ( $user instanceof WP_User ) ) {
			return;
		}
		if ( in_array( 'banned', (array) $user->roles ) ) {
			add_filter( 'comments_open', '__return_false' );
		}
	}

	/**
	 * Bundle all the capabilties for a given role
	 *
	 * @param string $role
	 * @return array $capabilities
	 *
	*/
	private function bundle_capabilities( $role = '' ) {
		$core_capabilities         = $this->get_core_capabilities( $role );
		$plugin_theme_capabilities = $this->plugin_theme_capabilities( $role );
		$custom_capabilities       = $this->custom_capabilities( $role );

		// combine: WordPress core capabilities, capabilities defined in plugins or themes, and custom ones we define right here.
		$capabilities = array_merge(
			$core_capabilities,
			$plugin_theme_capabilities,
			$custom_capabilities
		);
		return $capabilities;
	}

	/**
	 * Core user roles defined by WordPress
	 *
	 * @return array $core_roles
	 *
	*/
	private function get_core_roles() {
		$core_roles = array(
			'administrator',
			'editor',
			'author',
			'contributor',
			'subscriber',
		);
		return $core_roles;
	}

	/**
	 * Extra user roles
	 *
	 * @return array $extra_user_roles
	 *
	*/
	private function get_extra_user_roles() {
		$extra_user_roles = array(
			'business'                => __( 'Business', 'minnpost-roles-and-capabilities' ),
			'comment_moderator'       => __( 'Comment Moderator', 'minnpost-roles-and-capabilities' ),
			'staff'                   => __( 'Staff', 'minnpost-roles-and-capabilities' ),
			'member_platinum'         => __( 'Member - Platinum', 'minnpost-roles-and-capabilities' ),
			'member_gold'             => __( 'Member - Gold', 'minnpost-roles-and-capabilities' ),
			'member_silver'           => __( 'Member - Silver', 'minnpost-roles-and-capabilities' ),
			'member_bronze'           => __( 'Member - Bronze', 'minnpost-roles-and-capabilities' ),
			'unpublished_viewer_user' => __( 'Unpublished Viewer User', 'minnpost-roles-and-capabilities' ),
			'banned'                  => __( 'Banned', 'minnpost-roles-and-capabilities' ),
		);
		return $extra_user_roles;
	}

	/**
	 * Manage user capabilities that are part of core
	 * WordPress has these default capabilities:
	 *
	 *   read
	 *
	 *   edit_posts
	 *   edit_others_posts
	 *   edit_published_posts
	 *   publish_posts
	 *   delete_posts
	 *   delete_others_posts
	 *   delete_published_posts
	 *   delete_private_posts
	 *   edit_private_posts
	 *   read_private_posts
	 *
	 *   create_blocks
	 *   read_blocks
	 *   edit_blocks
	 *   edit_others_blocks
	 *   edit_published_blocks
	 *   publish_blocks
	 *   delete_blocks
	 *   delete_others_blocks
	 *   delete_published_blocks
	 *   delete_private_blocks
	 *   edit_private_blocks
	 *   read_private_blocks
	 *
	 *   edit_pages
	 *   edit_others_pages
	 *   edit_published_pages
	 *   publish_pages
	 *   delete_pages
	 *   delete_others_pages
	 *   delete_published_pages
	 *   delete_private_pages
	 *   edit_private_pages
	 *   read_private_pages
	 *
	 *   unfiltered_html
	 *
	 *   manage_categories
	 *   manage_links
	 *
	 *   upload_files
	 *   edit_files
	 *   unfiltered_upload
	 *
	 *   edit_users
	 *   delete_users
	 *   create_users
	 *   list_users
	 *   remove_users
	 *   promote_users
	 *
	 *   moderate_comments
	 *
	 *   manage_options
	 *
	 *   level_10
	 *   level_9
	 *   level_8
	 *   level_7
	 *   level_6
	 *   level_5
	 *   level_4
	 *   level_2
	 *   level_1
	 *   level_0
	 *
	 *   edit_dashboard
	 *
	 *   activate_plugins
	 *   edit_plugins
	 *   update_plugins
	 *   delete_plugins
	 *   install_plugins
	 *
	 *   switch_themes
	 *   edit_themes
	 *   update_themes
	 *   install_themes
	 *   edit_theme_options
	 *   delete_themes
	 *
	 *   update_core
	 *
	 *   import
	 *   export
	 *
	 * @param string $role
	 * @return array $core_capabilities
	*
	*/
	private function get_core_capabilities( $role = '' ) {
		$core_capabilities = array(
			'read'                    => array(
				'administrator',
				'editor',
				'business',
				'author',
				'contributor',
				'comment_moderator',
				'staff',
				'unpublished_viewer_user',
				'subscriber',
				'banned',
			),
			'edit_posts'              => array(
				'administrator',
				'editor',
				'business',
				'author',
				'contributor',
				'comment_moderator',
			),
			'edit_others_posts'       => array(
				'administrator',
				'editor',
				'business',
				'comment_moderator',
			),
			'edit_published_posts'    => array(
				'administrator',
				'editor',
				'business',
				'author',
				'contributor',
				'comment_moderator',
			),
			'publish_posts'           => array(
				'administrator',
				'editor',
				'business',
				'author',
			),
			'delete_posts'            => array(
				'administrator',
				'editor',
				'business',
				'author',
				'contributor',
			),
			'delete_others_posts'     => array(
				'administrator',
				'editor',
				'business',
			),
			'delete_published_posts'  => array(
				'administrator',
				'editor',
				'business',
				'author',
			),
			'delete_private_posts'    => array(
				'administrator',
				'editor',
				'business',
			),
			'edit_private_posts'      => array(
				'administrator',
				'editor',
				'business',
			),
			'read_private_posts'      => array(
				'administrator',
				'editor',
				'business',
				'author',
			),
			'read_blocks'             => array(
				'administrator',
				'editor',
				'business',
				'author',
				'contributor',
				'comment_moderator',
				'staff',
				'unpublished_viewer_user',
				'subscriber',
				'banned',
			),
			'create_blocks'           => array(
				'administrator',
				'editor',
				'business',
				'author',
				'contributor',
				'comment_moderator',
			),
			'edit_blocks'             => array(
				'administrator',
				'editor',
				'business',
				'author',
				'contributor',
				'comment_moderator',
			),
			'edit_others_blocks'      => array(
				'administrator',
				'editor',
				'business',
				'comment_moderator',
			),
			'edit_published_blocks'   => array(
				'administrator',
				'editor',
				'business',
				'author',
				'contributor',
				'comment_moderator',
			),
			'publish_blocks'          => array(
				'administrator',
				'editor',
				'business',
				'author',
			),
			'delete_blocks'           => array(
				'administrator',
				'editor',
				'business',
				'author',
				'contributor',
			),
			'delete_others_blocks'    => array(
				'administrator',
				'editor',
				'business',
			),
			'delete_published_blocks' => array(
				'administrator',
				'editor',
				'business',
				'author',
			),
			'delete_private_blocks'   => array(
				'administrator',
				'editor',
				'business',
			),
			'edit_private_blocks'     => array(
				'administrator',
				'editor',
				'business',
			),
			'read_private_blocks'     => array(
				'administrator',
				'editor',
				'business',
				'author',
			),
			'edit_pages'              => array(
				'administrator',
				'editor',
				'business',
			),
			'edit_others_pages'       => array(
				'administrator',
				'editor',
				'business',
			),
			'edit_published_pages'    => array(
				'administrator',
				'editor',
				'business',
			),
			'publish_pages'           => array(
				'administrator',
				'editor',
				'business',
			),
			'delete_pages'            => array(
				'administrator',
				'editor',
				'business',
			),
			'delete_others_pages'     => array(
				'administrator',
				'editor',
				'business',
			),
			'delete_published_pages'  => array(
				'administrator',
				'editor',
				'business',
			),
			'delete_private_pages'    => array(
				'administrator',
				'editor',
				'business',
			),
			'edit_private_pages'      => array(
				'administrator',
				'editor',
				'business',
			),
			'read_private_pages'      => array(
				'administrator',
				'editor',
				'business',
			),
			'unfiltered_html'         => array(
				'administrator',
				'editor',
				'business',
				'author',
			),
			'manage_categories'       => array(
				'administrator',
				'editor',
				'business',
			),
			'manage_links'            => array(
				'administrator',
				'editor',
			),
			'upload_files'            => array(
				'administrator',
				'editor',
				'business',
				'author',
			),
			'edit_files'              => array(
				'administrator',
			),
			'unfiltered_upload'       => array(
				'administrator',
				'editor',
				'business',
				'author',
			),
			'edit_users'              => array(
				'administrator',
				'editor',
				'business',
			),
			'delete_users'            => array(
				'administrator',
				'editor',
				'business',
			),
			'create_users'            => array(
				'administrator',
				'editor',
				'business',
			),
			'list_users'              => array(
				'administrator',
				'editor',
				'business',
			),
			'remove_users'            => array(
				'administrator',
				'editor',
				'business',
			),
			'promote_users'           => array(
				'administrator',
				'editor',
				'business',
			),
			'moderate_comments'       => array(
				'administrator',
				'editor',
				'business',
				'comment_moderator',
			),
			'manage_options'          => array(
				'administrator',
				'editor',
				'business',
			),
			'level_10'                => array(
				'administrator',
			),
			'level_9'                 => array(
				'administrator',
			),
			'level_8'                 => array(
				'administrator',
				'editor',
				'business',
			),
			'level_7'                 => array(
				'administrator',
				'editor',
				'business',
				'author',
			),
			'level_6'                 => array(
				'administrator',
				'editor',
				'business',
				'author',
				'contributor',
			),
			'level_5'                 => array(
				'administrator',
				'editor',
				'business',
				'author',
				'comment_moderator',
			),
			'level_4'                 => array(
				'administrator',
				'editor',
				'business',
				'author',
				'contributor',
				'comment_moderator',
			),
			'level_3'                 => array(
				'administrator',
				'editor',
				'business',
				'author',
				'contributor',
				'comment_moderator',
			),
			'level_2'                 => array(
				'administrator',
				'editor',
				'business',
				'author',
				'contributor',
				'comment_moderator',
			),
			'level_1'                 => array(
				'administrator',
				'editor',
				'business',
				'author',
				'contributor',
				'comment_moderator',
			),
			'level_0'                 => array(
				'administrator',
				'editor',
				'business',
				'author',
				'contributor',
				'comment_moderator',
				'staff',
				'unpublished_viewer_user',
				'subscriber',
			),
			'edit_dashboard'          => array(
				'administrator',
			),
			'activate_plugins'        => array(
				'administrator',
			),
			'edit_plugins'            => array(
				'administrator',
			),
			'update_plugins'          => array(
				'administrator',
			),
			'delete_plugins'          => array(
				'administrator',
			),
			'install_plugins'         => array(
				'administrator',
			),
			'switch_themes'           => array(
				'administrator',
			),
			'edit_themes'             => array(
				'administrator',
			),
			'update_themes'           => array(
				'administrator',
			),
			'install_themes'          => array(
				'administrator',
			),
			'edit_theme_options'      => array(
				'administrator',
				'editor',
				'business',
			),
			'delete_themes'           => array(
				'administrator',
			),
			'update_core'             => array(
				'administrator',
			),
			'import'                  => array(
				'administrator',
			),
			'export'                  => array(
				'administrator',
			),
		);
		if ( '' === $role ) {
			return $core_capabilities;
		} else {
			return $this->check_capability_roles( $role, $core_capabilities );
		}
	}

	/**
	 * Manage user capabilities that are added by plugins or themes, whether they're ours or from third parties.
	 *
	 *   edit_newsletter (minnpost-largo)
	 *   delete_newsletter (minnpost-largo)
	 *   edit_newsletters (minnpost-largo)
	 *   edit_others_newsletters (minnpost-largo)
	 *   publish_newsletters (minnpost-largo)
	 *   read_private_newsletters (minnpost-largo)
	 *   delete_newsletters (minnpost-largo)
	 *
	 *   configure_salesforce (object-sync-for-salesforce)
	 *   create_logs (object-sync-for-salesforce)
	 *   edit_log (object-sync-for-salesforce)
	 *   read_log (object-sync-for-salesforce)
	 *   delete_log (object-sync-for-salesforce)
	 *   edit_logs (object-sync-for-salesforce)
	 *   edit_others_logs (object-sync-for-salesforce)
	 *   publish_logs (object-sync-for-salesforce)
	 *   read_private_logs (object-sync-for-salesforce)
	 *   delete_logs (object-sync-for-salesforce)
	 *   edit_scheduled_action (object-sync-for-salesforce)
	 *   read_scheduled_action (object-sync-for-salesforce)
	 *   delete_scheduled_action (object-sync-for-salesforce)
	 *   delete_scheduled_actions (object-sync-for-salesforce)
	 *   edit_scheduled_actions (object-sync-for-salesforce)
	 *   edit_others_scheduled_actions (object-sync-for-salesforce)
	 *   publish_scheduled_actions (object-sync-for-salesforce)
	 *   read_private_scheduled_actions (object-sync-for-salesforce)
	 *   create_scheduled_actions (object-sync-for-salesforce)
	 *
	 *   manage_staff (staff-user-post-list)
	 *
	 *   manage_minnpost_membership_options (minnpost-membership)
	 *
	 *   copy_posts (duplicate-post)
	 *
	 *   edit_message (wp-message-inserter-plugin)
	 *   read_message (wp-message-inserter-plugin)
	 *   delete_message (wp-message-inserter-plugin)
	 *   edit_messages (wp-message-inserter-plugin)
	 *   edit_others_messages (wp-message-inserter-plugin)
	 *   publish_messages (wp-message-inserter-plugin)
	 *   read_private_messages (wp-message-inserter-plugin)
	 *   create_messages (wp-message-inserter-plugin)
	 *   manage_wp_message_inserter_options (wp-message-inserter-plugin)
	 *   delete_messages (wp-message-inserter-plugin)
	 *
	 *   edit_sponsors (cr3ativ-sponsor)
	 *   create_sponsors (cr3ativ-sponsor)
	 *   edit_sponsor_levels (cr3ativ-sponsor)
	 *
	 *   manage_advertising (appnexus-acm-provider)
	 *
	 *   read_private_tribe_event (the-events-calendar)
	 *   edit_tribe_event (the-events-calendar)
	 *   edit_others_tribe_event (the-events-calendar)
	 *   edit_private_tribe_event (the-events-calendar)
	 *   edit_published_tribe_event (the-events-calendar)
	 *   delete_tribe_event (the-events-calendar)
	 *   delete_others_tribe_event (the-events-calendar)
	 *   delete_private_tribe_event (the-events-calendar)
	 *   delete_published_tribe_event (the-events-calendar)
	 *   publish_tribe_event (the-events-calendar)
	 *   read_private_tribe_events (the-events-calendar)
	 *   edit_tribe_events (the-events-calendar)
	 *   edit_others_tribe_events (the-events-calendar)
	 *   edit_private_tribe_events (the-events-calendar)
	 *   edit_published_tribe_events (the-events-calendar)
	 *   delete_tribe_events (the-events-calendar)
	 *   delete_others_tribe_events (the-events-calendar)
	 *   delete_private_tribe_events (the-events-calendar)
	 *   delete_published_tribe_events (the-events-calendar)
	 *   publish_tribe_events (the-events-calendar)
	 *   read_private_tribe_venues (the-events-calendar)
	 *   edit_tribe_venues (the-events-calendar)
	 *   edit_others_tribe_venues (the-events-calendar)
	 *   edit_private_tribe_venues (the-events-calendar)
	 *   edit_published_tribe_venues (the-events-calendar)
	 *   delete_tribe_venues (the-events-calendar)
	 *   delete_others_tribe_venues (the-events-calendar)
	 *   delete_private_tribe_venues (the-events-calendar)
	 *   delete_published_tribe_venues (the-events-calendar)
	 *   publish_tribe_venues (the-events-calendar)
	 *   read_private_tribe_organizers (the-events-calendar)
	 *   edit_tribe_organizers (the-events-calendar)
	 *   edit_others_tribe_organizers (the-events-calendar)
	 *   edit_private_tribe_organizers (the-events-calendar)
	 *   edit_published_tribe_organizers (the-events-calendar)
	 *   delete_tribe_organizers (the-events-calendar)
	 *   delete_others_tribe_organizers (the-events-calendar)
	 *   delete_private_tribe_organizers (the-events-calendar)
	 *   delete_published_tribe_organizers (the-events-calendar)
	 *   publish_tribe_organizers (the-events-calendar)
	 *   read_private_aggregator-records (the-events-calendar)
	 *   edit_aggregator-records (the-events-calendar)
	 *   edit_others_aggregator-records (the-events-calendar)
	 *   edit_private_aggregator-records (the-events-calendar)
	 *   edit_published_aggregator-records (the-events-calendar)
	 *   delete_aggregator-records (the-events-calendar)
	 *   delete_others_aggregator-records (the-events-calendar)
	 *   delete_private_aggregator-records (the-events-calendar)
	 *   delete_published_aggregator-records (the-events-calendar)
	 *   publish_aggregator-records (the-events-calendar)
	 *
	 *   edit_popup_themes (popup-maker)
	 *   edit_popup (popup-maker)
	 *   delete_popup (popup-maker)
	 *   edit_popups (popup-maker)
	 *   edit_others_popups (popup-maker)
	 *   publish_popups (popup-maker)
	 *   read_private_popups (popup-maker)
	 *   edit_popup_theme (popup-maker)
	 *   delete_popup_theme (popup-maker)
	 *   edit_others_popup_themes (popup-maker)
	 *   publish_popup_themes (popup-maker)
	 *   read_private_popup_themes (popup-maker)
	 *
	 *   ef_view_calendar (inactive)
	 *   edit_post_subscriptions (inactive)
	 *   ef_view_story_budget (inactive)
	 *   edit_usergroups (inactive)
	 *   view_all_aryo_activity_log (inactive)
	 *
	 * @param string $role
	 * @return array $plugin_theme_capabilities
	 *
	*/
	private function plugin_theme_capabilities( $role = '' ) {
		$plugin_theme_capabilities = array(
			'edit_newsletter'                     => array(
				'administrator',
				'editor',
				'author',
			),
			'delete_newsletter'                   => array(
				'administrator',
				'editor',
				'author',
			),
			'edit_newsletters'                    => array(
				'administrator',
				'editor',
				'author',
			),
			'edit_others_newsletters'             => array(
				'administrator',
				'editor',
				'author',
			),
			'publish_newsletters'                 => array(
				'administrator',
				'editor',
				'author',
			),
			'read_private_newsletters'            => array(
				'administrator',
				'editor',
				'author',
			),
			'delete_newsletters'                  => array(
				'administrator',
				'editor',
				'author',
			),
			'configure_salesforce'                => array(
				'administrator',
				'business',
			),
			'create_logs'                         => array(
				'administrator',
			),
			'edit_log'                            => array(
				'administrator',
			),
			'read_log'                            => array(
				'administrator',
			),
			'delete_log'                          => array(
				'administrator',
			),
			'edit_logs'                           => array(
				'administrator',
			),
			'edit_others_logs'                    => array(
				'administrator',
			),
			'publish_logs'                        => array(
				'administrator',
			),
			'read_private_logs'                   => array(
				'administrator',
			),
			'delete_logs'                         => array(
				'administrator',
			),
			'edit_scheduled_action'               => array(
				'administrator',
			),
			'read_scheduled_action'               => array(
				'administrator',
			),
			'delete_scheduled_action'             => array(
				'administrator',
			),
			'delete_scheduled_actions'            => array(
				'administrator',
			),
			'edit_scheduled_actions'              => array(
				'administrator',
			),
			'edit_others_scheduled_actions'       => array(
				'administrator',
			),
			'publish_scheduled_actions'           => array(
				'administrator',
			),
			'read_private_scheduled_actions'      => array(
				'administrator',
			),
			'create_scheduled_actions'            => array(
				'administrator',
			),
			'manage_staff'                        => array(
				'administrator',
				'editor',
				'business',
			),
			'manage_minnpost_membership_options'  => array(
				'administrator',
				'business',
			),
			'copy_posts'                          => array(
				'administrator',
				'editor',
				'business',
			),
			'edit_message'                        => array(
				'administrator',
				'editor',
				'business',
			),
			'read_message'                        => array(
				'administrator',
				'editor',
				'business',
			),
			'delete_message'                      => array(
				'administrator',
				'editor',
				'business',
			),
			'edit_messages'                       => array(
				'administrator',
				'editor',
				'business',
			),
			'edit_others_messages'                => array(
				'administrator',
				'editor',
				'business',
			),
			'publish_messages'                    => array(
				'administrator',
				'editor',
				'business',
			),
			'read_private_messages'               => array(
				'administrator',
				'editor',
				'business',
			),
			'create_messages'                     => array(
				'administrator',
				'editor',
				'business',
			),
			'manage_wp_message_inserter_options'  => array(
				'administrator',
			),
			'delete_messages'                     => array(
				'administrator',
				'editor',
				'business',
			),
			'edit_sponsors'                       => array(
				'administrator',
				'business',
			),
			'create_sponsors'                     => array(
				'administrator',
				'business',
			),
			'edit_sponsor_levels'                 => array(
				'administrator',
			),
			'manage_advertising'                  => array(
				'administrator',
				'business',
			),
			'read_private_tribe_event'            => array(
				'administrator',
				'editor',
				'business',
				'author',
			),
			'edit_tribe_event'                    => array(
				'administrator',
				'editor',
				'business',
				'author',
				'contributor',
			),
			'edit_others_tribe_event'             => array(
				'administrator',
				'editor',
				'business',
				'business',
			),
			'edit_private_tribe_event'            => array(
				'administrator',
				'editor',
				'business',
				'author',
			),
			'edit_published_tribe_event'          => array(
				'administrator',
				'editor',
				'business',
				'author',
			),
			'delete_tribe_event'                  => array(
				'administrator',
				'editor',
				'business',
				'author',
				'contributor',
			),
			'delete_others_tribe_event'           => array(
				'administrator',
				'editor',
				'business',
			),
			'delete_private_tribe_event'          => array(
				'administrator',
				'editor',
				'business',
				'author',
			),
			'delete_published_tribe_event'        => array(
				'administrator',
				'editor',
				'business',
				'author',
			),
			'publish_tribe_event'                 => array(
				'administrator',
				'editor',
				'business',
				'author',
			),
			'read_private_tribe_events'           => array(
				'administrator',
				'editor',
				'business',
				'author',
			),
			'edit_tribe_events'                   => array(
				'administrator',
				'editor',
				'business',
				'author',
				'contributor',
			),
			'edit_others_tribe_events'            => array(
				'administrator',
				'editor',
				'business',
				'business',
			),
			'edit_private_tribe_events'           => array(
				'administrator',
				'editor',
				'business',
				'author',
			),
			'edit_published_tribe_events'         => array(
				'administrator',
				'editor',
				'business',
				'author',
			),
			'delete_tribe_events'                 => array(
				'administrator',
				'editor',
				'business',
				'author',
				'contributor',
			),
			'delete_others_tribe_events'          => array(
				'administrator',
				'editor',
				'business',
			),
			'delete_private_tribe_events'         => array(
				'administrator',
				'editor',
				'business',
				'author',
			),
			'delete_published_tribe_events'       => array(
				'administrator',
				'editor',
				'business',
				'author',
			),
			'publish_tribe_events'                => array(
				'administrator',
				'editor',
				'business',
				'author',
			),
			'read_private_tribe_venues'           => array(
				'administrator',
				'editor',
				'business',
				'author',
			),
			'edit_tribe_venues'                   => array(
				'administrator',
				'editor',
				'business',
				'author',
				'contributor',
			),
			'edit_others_tribe_venues'            => array(
				'administrator',
				'editor',
				'business',
			),
			'edit_private_tribe_venues'           => array(
				'administrator',
				'editor',
				'business',
				'author',
			),
			'edit_published_tribe_venues'         => array(
				'administrator',
				'editor',
				'business',
				'author',
			),
			'delete_tribe_venues'                 => array(
				'administrator',
				'editor',
				'business',
				'author',
				'contributor',
			),
			'delete_others_tribe_venues'          => array(
				'administrator',
				'editor',
				'business',
			),
			'delete_private_tribe_venues'         => array(
				'administrator',
				'editor',
				'business',
				'author',
			),
			'delete_published_tribe_venues'       => array(
				'administrator',
				'editor',
				'business',
				'author',
			),
			'publish_tribe_venues'                => array(
				'administrator',
				'editor',
				'business',
				'author',
			),
			'read_private_tribe_organizers'       => array(
				'administrator',
				'editor',
				'business',
				'author',
			),
			'edit_tribe_organizers'               => array(
				'administrator',
				'editor',
				'business',
				'author',
				'contributor',
			),
			'edit_others_tribe_organizers'        => array(
				'administrator',
				'editor',
				'business',
			),
			'edit_private_tribe_organizers'       => array(
				'administrator',
				'editor',
				'business',
				'author',
			),
			'edit_published_tribe_organizers'     => array(
				'administrator',
				'editor',
				'business',
				'author',
			),
			'delete_tribe_organizers'             => array(
				'administrator',
				'editor',
				'business',
				'author',
				'contributor',
			),
			'delete_others_tribe_organizers'      => array(
				'administrator',
				'editor',
				'business',
			),
			'delete_private_tribe_organizers'     => array(
				'administrator',
				'editor',
				'business',
				'author',
			),
			'delete_published_tribe_organizers'   => array(
				'administrator',
				'editor',
				'business',
				'author',
			),
			'publish_tribe_organizers'            => array(
				'administrator',
				'editor',
				'business',
				'author',
			),
			'read_private_aggregator-records'     => array(
				'administrator',
				'editor',
				'business',
				'author',
			),
			'edit_aggregator-records'             => array(
				'administrator',
				'editor',
				'business',
				'author',
				'contributor',
			),
			'edit_others_aggregator-records'      => array(
				'administrator',
				'editor',
				'business',
			),
			'edit_private_aggregator-records'     => array(
				'administrator',
				'editor',
				'business',
				'author',
			),
			'edit_published_aggregator-records'   => array(
				'administrator',
				'editor',
				'business',
				'author',
			),
			'delete_aggregator-records'           => array(
				'administrator',
				'editor',
				'business',
				'author',
				'contributor',
			),
			'delete_others_aggregator-records'    => array(
				'administrator',
				'editor',
				'business',
				'business',
			),
			'delete_private_aggregator-records'   => array(
				'administrator',
				'editor',
				'business',
				'author',
			),
			'delete_published_aggregator-records' => array(
				'administrator',
				'editor',
				'business',
				'author',
			),
			'publish_aggregator-records'          => array(
				'administrator',
				'editor',
				'business',
				'author',
			),
			'edit_popup_themes'                   => array(
				'administrator',
				'business',
			),
			'edit_popup'                          => array(
				'administrator',
				'business',
			),
			'delete_popup'                        => array(
				'administrator',
				'business',
			),
			'edit_popups'                         => array(
				'administrator',
				'business',
			),
			'edit_others_popups'                  => array(
				'administrator',
				'business',
			),
			'publish_popups'                      => array(
				'administrator',
				'business',
			),
			'read_private_popups'                 => array(
				'administrator',
				'business',
			),
			'edit_popup_theme'                    => array(
				'administrator',
				'business',
			),
			'delete_popup_theme'                  => array(
				'administrator',
				'business',
			),
			'edit_others_popup_themes'            => array(
				'administrator',
				'business',
			),
			'publish_popup_themes'                => array(
				'administrator',
				'business',
			),
			'read_private_popup_themes'           => array(
				'administrator',
				'business',
			),
		);
		if ( '' === $role ) {
			return $plugin_theme_capabilities;
		} else {
			return $this->check_capability_roles( $role, $plugin_theme_capabilities );
		}
	}

	/**
	 * Create and manage user capabilities we use that are not added by plugins
	 *
	 *   see_admin_bar
	 *   browse_without_ads
	 *   manage_jetpack
	 *   see_hidden_terms
	 *   manage_cron
	 *   manage_search
	 *   create_zones
	 *   edit_zones
	 *   manage_zones
	 *   view_unpublished_posts
	 *   see_tools_menu
	 *   see_profile_menu
	 *   access_blocked_content
	 *
	 * @param string $role
	 * @return array $custom_capabilities
	*
	*/
	private function custom_capabilities( $role = '' ) {
		$custom_capabilities = array(
			'access_blocked_content' => array(
				'administrator',
				'author',
				'business',
				'editor',
			),
			'browse_without_ads'     => array(
				'administrator',
				'editor',
			),
			'create_zones'           => array(
				'administrator',
				'editor',
			),
			'edit_zones'             => array(
				'administrator',
				'editor',
			),
			'manage_cron'            => array(
				'administrator',
				'editor',
			),
			'manage_jetpack'         => array(
				'administrator',
			),
			'manage_search'          => array(
				'administrator',
			),
			'manage_zones'           => array(
				'administrator',
				'editor',
			),
			'see_admin_bar'          => array(
				'administrator',
				'editor',
				'business',
				'author',
				'contributor',
				'comment_moderator',
			),
			'see_hidden_terms'       => array(
				'administrator',
			),
			'see_profile_menu'       => array(
				'administrator',
			),
			'see_tools_menu'         => array(
				'administrator',
				'business',
			),
			'view_unpublished_posts' => array(
				'administrator',
				'editor',
				'business',
				'author',
				'contributor',
				'unpublished_viewer_user',
			),
		);
		if ( '' === $role ) {
			return $custom_capabilities;
		} else {
			return $this->check_capability_roles( $role, $custom_capabilities );
		}
	}

	/**
	 * Get the capabilities attached to a specific role in an array
	 *
	 * @param string $role
	 * @param array $capabilities
	 * @return array $role_capabilities
	 *
	*/
	private function check_capability_roles( $role, $capabilities ) {
		$role_capabilities = array();
		foreach ( $capabilities as $capability => $roles ) {
			if ( in_array( $role, $roles ) ) {
				$role_capabilities[] = $capability;
			}
		}
		return $role_capabilities;
	}

}

// start doing stuff
add_action( 'plugins_loaded', array( 'Minnpost_Roles_And_Capabilities', 'get_instance' ) );
