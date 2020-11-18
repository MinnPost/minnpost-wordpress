<?php
/*
Plugin Name: MinnPost Roles and Capabilities
Description: Set all roles and capabilities for MinnPost access. This replaces the AAM plugin for us.
Version: 0.0.3
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
	 * @var string
	 * The setting name for the version
	*/
	private $version_option_name;

	/**
	 * @var string
	 * The current $roles version
	*/
	private $roles_version;

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

		$this->version             = '0.0.3';
		$this->version_option_name = 'minnpost_roles_capabilities_version';
		$this->roles_version       = get_option( $this->version_option_name, '' );
		$this->slug                = 'minnpost-roles-and-capabilities';

		$this->add_actions();

	}

	private function add_actions() {
		// setup roles
		add_action( 'admin_init', array( $this, 'check_version' ), 10 );
		add_filter( 'view_admin_as_full_access_capabilities', array( $this, 'vip_full_access_capabilities' ), 10, 1 );
		register_activation_hook( __FILE__, array( $this, 'user_roles' ) );
		add_action( 'init', array( $this, 'disallow_banned_user_comments' ), 10 );
		if ( is_admin() ) {
			//add_action( 'admin_init', array( $this, 'test_capabilities' ) ); // temp method
			add_action( 'admin_menu', array( $this, 'create_admin_menu' ) );
		}

	}

	/**
	* Check for $roles version
	* When the plugin is loaded in the admin, if the $roles version does not match the current version, perform these methods
	*
	*/
	public function check_version() {
		// user is running a version less than the current one
		if ( version_compare( $this->roles_version, $this->version, '<' ) ) {
			$this->user_roles();
		} else {
			return true;
		}
	}

	/**
	 * Set capabilities requied for view admin as plugin
	 *
	 * @param array $caps
	 * @return array $caps
	 *
	*/
	public function vip_full_access_capabilities( $caps ) {
		if ( null !== DISALLOW_FILE_MODS && true === DISALLOW_FILE_MODS ) {
			if ( false !== ( $key = array_search( 'delete_plugins', $caps, true ) ) ) {
				unset( $caps[ $key ] );
			}
		}
		return $caps;
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
			if ( ( defined( 'WPCOM_IS_VIP_ENV' ) && WPCOM_IS_VIP_ENV ) ) {
				$result = wpcom_vip_add_role(
					$role,
					$display_name,
					$this->bundle_capabilities( $role )
				);
			} else {
				$result = add_role(
					$role,
					$display_name,
					$this->bundle_capabilities( $role )
				);
			}
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
				$all_capabilities  = $this->bundle_capabilities();
				$role_capabilities = $this->bundle_capabilities( $name );
				// for each possible capability, check if it matches the expected capabilities for that role. if it does, assign it. otherwise, remove it.
				foreach ( $all_capabilities as $key => $value ) {
					if ( in_array( $key, $role_capabilities, true ) ) {
						$role->add_cap( $key );
					} else {
						$role->remove_cap( $key );
					}
				}
			}
		}

		// update the version option
		if ( '' === $this->roles_version || version_compare( $this->roles_version, $this->version, '<' ) ) {
			update_option( $this->version_option_name, $this->version );
		}

		return true;

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
		if ( in_array( 'banned', (array) $user->roles, true ) ) {
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
				'contributor',
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
	 *   view_query_monitor (query-monitor)
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
	 *   enable_liveblog (added manually for the liveblog plugin)
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
	 *   gravityforms_edit_forms (gravity-forms)
	 *   gravityforms_delete_forms (gravity-forms)
	 *   gravityforms_create_form (gravity-forms)
	 *   gravityforms_view_entries (gravity-forms)
	 *   gravityforms_edit_entries (gravity-forms)
	 *   gravityforms_delete_entries (gravity-forms)
	 *   gravityforms_view_settings (gravity-forms)
	 *   gravityforms_edit_settings (gravity-forms)
	 *   gravityforms_export_entries (gravity-forms)
	 *   gravityforms_uninstall (gravity-forms)
	 *   gravityforms_view_entry_notes (gravity-forms)
	 *   gravityforms_edit_entry_notes (gravity-forms)
	 *   gravityforms_view_updates (gravity-forms)
	 *   gravityforms_view_addons (gravity-forms)
	 *   gravityforms_preview_forms (gravity-forms)
	 *   gravityforms_system_status (gravity-forms)
	 *   gravityforms_logging (gravity-forms)
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
			'view_query_monitor'                  => array(
				'administrator',
			),
			'edit_newsletter'                     => array(
				'administrator',
				'editor',
				'author',
			),
			'read_newsletter'                     => array(
				'administrator',
				'editor',
				'business',
				'author',
			),
			'delete_newsletter'                   => array(
				'administrator',
				'editor',
				'author',
			),
			'delete_newsletters'                  => array(
				'administrator',
				'editor',
				'author',
			),
			'edit_newsletters'                    => array(
				'administrator',
				'editor',
				'business',
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
				'business',
				'author',
			),
			'create_newsletters'                  => array(
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
			'enable_liveblog'                     => array(
				'administrator',
				'editor',
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
			'gravityforms_edit_forms'             => array(
				'administrator',
				'editor',
				'business',
			),
			'gravityforms_delete_forms'           => array(
				'administrator',
				'editor',
				'business',
			),
			'gravityforms_create_form'            => array(
				'administrator',
				'editor',
				'business',
			),
			'gravityforms_view_entries'           => array(
				'administrator',
				'editor',
				'business',
			),
			'gravityforms_edit_entries'           => array(
				'administrator',
				'editor',
				'business',
			),
			'gravityforms_delete_entries'         => array(
				'administrator',
				'editor',
				'business',
			),
			'gravityforms_view_settings'          => array(
				'administrator',
			),
			'gravityforms_edit_settings'          => array(
				'administrator',
			),
			'gravityforms_export_entries'         => array(
				'administrator',
				'editor',
				'business',
			),
			'gravityforms_uninstall'              => array(
				'administrator',
			),
			'gravityforms_view_entry_notes'       => array(
				'administrator',
				'editor',
				'business',
			),
			'gravityforms_edit_entry_notes'       => array(
				'administrator',
				'editor',
				'business',
			),
			'gravityforms_view_updates'           => array(
				'administrator',
			),
			'gravityforms_view_addons'            => array(
				'administrator',
			),
			'gravityforms_preview_forms'          => array(
				'administrator',
				'editor',
				'business',
			),
			'gravityforms_system_status'          => array(
				'administrator',
			),
			'gravityforms_logging'                => array(
				'administrator',
			),
			'manage_redirects'                    => array(
				'administrator',
				'editor',
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
				'business',
				'editor',
				'author',
				'contributor',
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

	/**
	* Create WordPress admin options page
	*
	*/
	public function create_admin_menu() {
		$capability = 'administrator';
		add_users_page( 'Roles and Capabilities', 'Roles and Capabilities', $capability, $this->slug, array( $this, 'show_admin_page' ) );
	}

	/**
	* Display the admin settings page
	*
	* @return void
	*/
	public function show_admin_page() {
		$post_data = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );
		?>
		<div class="wrap">
			<h1><?php echo esc_html__( get_admin_page_title() , 'minnpost-roles-and-capabilities' ); ?></h1>
			<?php if ( empty( $post_data ) ) : ?>
				<form method="post" action="users.php?page=<?php echo esc_attr( $this->slug ); ?>">
					<input type="hidden" name="action" value="refresh-roles-capabilities" ?>
					<h3><?php echo esc_html__( 'Press the button to refresh all roles and capabilities for the site.', 'minnpost-roles-and-capabilities' ); ?></h3>
					<?php
						submit_button( esc_html__( 'Refresh', 'minnpost-roles-and-capabilities' ), 'primary', 'submit' );
					?>
				</form>
			<?php else : ?>
				<?php if ( 'refresh-roles-capabilities' === $post_data['action'] ) : ?>
					<?php if ( true === $this->user_roles() ) : ?>
						<p><?php echo __( 'All roles and capabilities have been refreshed. If you change them again, you can revisit this page and press the button.', 'minnpost-roles-and-capabilities' ); ?></p>
					<?php endif; ?>
				<?php endif; ?>
			<?php endif; ?>
		</div>
		<?php
	}

}

// start doing stuff. for the view-admin-as plugin, at least, we have to use muplugins_loaded
add_action( 'muplugins_loaded', array( 'Minnpost_Roles_And_Capabilities', 'get_instance' ) );
