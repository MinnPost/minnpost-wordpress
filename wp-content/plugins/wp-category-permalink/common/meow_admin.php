<?php

if ( !class_exists( 'Meow_Admin' ) ) {

	class Meow_Admin {

		public static $loaded = false;
		public static $version = "0.2";
		public $prefix = null;
		public $item = null;

		public function __construct( $prefix = null, $item = null ) {
			if ( !Meow_Admin::$loaded ) {
				if ( is_admin() ) {
					add_action( 'admin_menu', array( $this, 'admin_menu_start' ) );
					add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
					add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
				}
			}
			if ( !empty( $prefix ) && !empty( $item ) ) {
				$this->prefix = $prefix;
				$this->item = $item;
				if ( is_admin() ) {
					add_action( 'update_option_' . $prefix . '_pro_serial', array( $this, 'serial_updated' ), 10, 2 );
					add_action( 'admin_menu', array( $this, 'admin_menu_for_serialkey' ) );
				}
			}
			Meow_Admin::$loaded = true;
		}

		function display_ads() {
			return !get_option( 'meowapps_hide_ads', false );
		}

		function display_title( $title = "Meow Apps",
			$author = "By <a style='text-decoration: none;' href='http://meowapps.com' target='_blank'>Jordy Meow</a>" ) {
			if ( $this->display_ads() ) {
				echo '<a class="meow-header-ad" target="_blank" href="http://www.shareasale.com/r.cfm?b=906810&u=767054&m=41388&urllink=&afftrack="">
				<img src="' . $this->common_url( 'img/wpengine.png' ) . '" height="60" border="0" /></a>';
			}
			?>
			<h1 style="line-height: 16px;">
				<img width="36" style="margin-right: 10px; float: left; position: relative; top: -5px;"
					src="<?php echo $this->meowapps_logo_url(); ?>"><?php echo $title; ?><br />
				<span style="font-size: 12px"><?php echo $author; ?></span>
			</h1>
			<div style="clear: both;"></div>
			<?php
		}

		function admin_enqueue_scripts() {
			wp_register_style( 'meowapps-admin-css', $this->common_url( 'meow-admin.css' ) );
			wp_enqueue_style( 'meowapps-admin-css' );
		}

		function admin_menu_start() {
			if ( get_option( 'meowapps_hide_meowapps', false ) ) {
				register_setting( 'general', 'meowapps_hide_meowapps' );
				add_settings_field( 'meowapps_hide_ads', 'Meow Apps Menu', array( $this, 'meowapps_hide_dashboard_callback' ), 'general' );
				return;
			}

			// Creates standard menu if it does NOT exist
			global $submenu;
			if ( !isset( $submenu[ 'meowapps-main-menu' ] ) ) {
				add_menu_page( 'Meow Apps', 'Meow Apps', 'manage_options', 'meowapps-main-menu',
					array( $this, 'admin_meow_apps' ), 'dashicons-camera', 82 );
				add_submenu_page( 'meowapps-main-menu', __( 'Dashboard', 'meowapps' ),
					__( 'Dashboard', 'meowapps' ), 'manage_options',
					'meowapps-main-menu', array( $this, 'admin_meow_apps' ) );
			}

			add_settings_section( 'meowapps_common_settings', null, null, 'meowapps_common_settings-menu' );
			add_settings_field( 'meowapps_hide_meowapps', "Main Menu",
				array( $this, 'meowapps_hide_dashboard_callback' ),
				'meowapps_common_settings-menu', 'meowapps_common_settings' );
			add_settings_field( 'meowapps_hide_ads', "Ads",
				array( $this, 'meowapps_hide_ads_callback' ),
				'meowapps_common_settings-menu', 'meowapps_common_settings' );
			register_setting( 'meowapps_common_settings', 'meowapps_hide_meowapps' );
			register_setting( 'meowapps_common_settings', 'meowapps_hide_ads' );
		}

		function meowapps_hide_ads_callback() {
			$value = get_option( 'meowapps_hide_ads', null );
			$html = '<input type="checkbox" id="meowapps_hide_ads" name="meowapps_hide_ads" value="1" ' .
				checked( 1, get_option( 'meowapps_hide_ads' ), false ) . '/>';
	    $html .= __( '<label>Hide</label><br /><small>Doesn\'t display the ads.</small>', 'wp-retina-2x' );
	    echo $html;
		}

		function meowapps_hide_dashboard_callback() {
			$value = get_option( 'meowapps_hide_meowapps', null );
			$html = '<input type="checkbox" id="meowapps_hide_meowapps" name="meowapps_hide_meowapps" value="1" ' .
				checked( 1, get_option( 'meowapps_hide_meowapps' ), false ) . '/>';
	    $html .= __( '<label>Hide <b>Meow Apps</b> Menu</label><br /><small>Hide Meow Apps menu and all its components, for a nicer an faster WordPress admin UI. An option will be added in Settings > General to display it again.</small>', 'wp-retina-2x' );
	    echo $html;
		}

		function admin_menu_for_serialkey() {
			// SUBMENU > Settings > Pro Serial
			add_settings_section( $this->prefix . '_settings_serialkey', null, null, $this->prefix . '_settings_serialkey-menu' );
			add_settings_field( $this->prefix . '_pro_serial', "Serial Key",
				array( $this, 'admin_serialkey_callback' ),
				$this->prefix . '_settings_serialkey-menu', $this->prefix . '_settings_serialkey' );
			register_setting( $this->prefix . '_settings_serialkey', $this->prefix . '_pro_serial' );
		}

		function admin_serialkey_callback( $args ) {
	    $value = get_option( $this->prefix . '_pro_serial', null );
	    $html = '<input type="text" id="' . $this->prefix . '_pro_serial" name="' . $this->prefix . '_pro_serial" value="' . $value . '" />';
	    echo $html;
	  }

		function display_serialkey_box( $url = "https://meowapps.com/" ) {
			$status = get_option( $this->prefix . '_pro_status' );
			?>
			<div class="meow-box">
				<h3 class="<?php echo $this->is_pro() ? 'meow-bk-blue' : 'meow-bk-red'; ?>">Pro Version <?php echo $this->is_pro() ? '(enabled)' : '(disabled)'; ?></h3>
				<div class="inside">
					<form method="post" action="options.php">
						<?php if ( !empty( $status ) ): ?>
						<div class="pro_info <?php echo $this->is_pro() ? 'enabled' : 'disabled'; ?>">
							<?php echo get_option( $this->prefix . '_pro_status' ); ?>
						</div>
						<?php endif; ?>
						<?php settings_fields( $this->prefix . '_settings_serialkey' ); ?>
						<?php do_settings_sections( $this->prefix . '_settings_serialkey-menu' ); ?>
						<?php if ( !$this->is_pro() ): ?>
							<small class="description">Insert your serial key above. If you don't have one yet, you can get one <a target="_blank" href="<?php echo $url; ?>">here.</a></small>
						<?php endif; ?>
						<?php submit_button(); ?>
					</form>
				</div>
			</div>
			<?php
		}

		function serial_updated( $old_value, $new_value ) {
			if ( $old_value != $new_value ) {
				$this->validate_pro( $new_value );
			}
		}

		function is_pro() {
			$prefix = $this->prefix;
			$validated = get_transient( $prefix . '_validated' );
			$subscr_id = get_option( $prefix . '_pro_serial', "" );
			if ( $validated )
				return !empty( $subscr_id );
			if ( !empty( $subscr_id ) )
				return $this->validate_pro( $subscr_id );
			return false;
		}

		function validate_pro( $subscr_id ) {
			$prefix = $this->prefix;
			$item = $this->item;
			delete_option( $prefix . '_pro_serial', "" );
			update_option( $prefix . '_pro_status', __( '', 'meowapps' ) );
			set_transient( $prefix . '_validated', false, 0 );
			if ( empty( $subscr_id ) )
				return false;
			$bodyreq = array( 'subscr_id' => $subscr_id, 'item' => $item, 'url' => get_site_url() );
			$response = wp_remote_post( 'https://meowapps.com/wp-json/meow/v1/auth', array(
				'body' => $bodyreq,
				'user-agent' => "MeowApps",
				'sslverify' => false,
				'timeout' => 45,
				'method' => 'POST'
			));
			$body = is_array( $response ) ? $response['body'] : null;
			$post = @json_decode( $body );
			if ( !$post || ( property_exists( $post, 'code' ) ) ) {
				$status = __( "There was an error while validating the serial.<br />Please contact <a target='_blank' href='https://meowapps.com/contact/'>Meow Apps</a> and mention the following log: <br /><ul>" );
				$status .= "<li>Server IP: <b>" . gethostbyname( $_SERVER['SERVER_NAME'] ) . "</b></li>";
				$status .= "<li>Google GET: ";
				$r = wp_remote_get( 'http://google.com' );
				$status .= is_wp_error( $r ) ? print_r( $r, true ) : 'OK';
				$status .= "</li><li>MeowApps GET: ";
				$r = wp_remote_get( 'http://google.com' );
				$status .= is_wp_error( $r ) ? print_r( $r, true ) : 'OK';
				$status .= "</li><li>MeowApps POST: ";
				$status .= print_r( $response, true );
				$status .= "</li></ul>";
				error_log( print_r( $response, true ) );
				update_option( $prefix . '_pro_status', $status );
				return false;
			}
			if ( !$post->success ) {
				if ( $post->message_code == "NO_SUBSCRIPTION" )
					$status = __( "Your serial ('$subscr_id') does not seem right." );
				else if ( $post->message_code == "NOT_ACTIVE" )
					$status = __( "Your subscription is not active." );
				else if ( $post->message_code == "TOO_MANY_URLS" )
					$status = __( "Too many URLs are linked to your subscription." );
				else
					$status = "There is a problem with your subscription.";
				update_option( $prefix . '_pro_status', $status );
				return false;
			}
			set_transient( $prefix . '_validated', $subscr_id, 3600 * 24 * 100 );
			update_option( $prefix . '_pro_serial', $subscr_id );
			update_option( $prefix . '_pro_status', __( '', 'meowapps' ) );
			return true;
		}

		function check_install( $plugin ) {
			$pluginpath = get_home_path() . 'wp-content/plugins/' . $plugin;
			if ( !file_exists( $pluginpath ) ) {
				$url = wp_nonce_url( "update.php?action=install-plugin&plugin=$plugin", "install-plugin_$plugin" );
				return "<a href='$url'><small><span class='' style='float: right;'>install</span></small></a>";
			}
			$plugin_file = $plugin . '/' . $plugin . '.php';
			if ( is_plugin_active( $plugin_file ) )
				return "<small><span style='float: right; color: green;'><span class='dashicons dashicons-yes'></span></span></small>";
			else {
				$url = wp_nonce_url( self_admin_url( 'plugins.php?action=activate&plugin=' . $plugin_file ),
					'activate-plugin_' . $plugin_file );
				return '<small><span style="color: orange; float: right;">off
				(<a style="color: rgba(30,140,190,.8); text-decoration: none;" href="' .
					$url . '">enable</a>)</span></small>';
			}
		}

		function common_url( $file ) {
			die( "Meow Apps: The function common_url( \$file ) needs to be overriden." );
			// Normally, this should be used:
			// return plugin_dir_url( __FILE__ ) . ( '\/common\/' . $file );
		}

		function meowapps_logo_url() {
			return $this->common_url( 'img/meowapps.png' );
		}

		function plugins_loaded() {
			if ( isset( $_GET[ 'tool' ] ) && $_GET[ 'tool' ] == 'error_log' ) {
 				$sec = "5";
 				header("Refresh: $sec;");
			}
		}

		function admin_meow_apps() {

			echo '<div class="wrap meow-dashboard">';
			if ( isset( $_GET['tool'] ) && $_GET['tool'] == 'phpinfo' ) {
				echo "<a href=\"javascript:history.go(-1)\">< Go back</a><br /><br />";
				echo '<div id="phpinfo">';
				ob_start();
				phpinfo();
				$pinfo = ob_get_contents();
				ob_end_clean();
				$pinfo = preg_replace( '%^.*<body>(.*)</body>.*$%ms','$1', $pinfo );
				echo $pinfo;
				echo "</div>";
			}
			else if ( isset( $_GET['tool'] ) && $_GET['tool'] == 'error_log' ) {
				echo "<a href=\"javascript:history.go(-1)\">< Go back</a><br /><br />";
				echo '<div id="error_log">';
				echo "Now (auto-reload every 5 seconds):<br />[" . date( "d-M-Y H:i:s", time() ) . " UTC]<br /<br /><br />Errors (order by latest):";
				$errorpath = ini_get( 'error_log' );
				$errors = file_get_contents( $errorpath );
				$errors = explode( "\n", $errors );
				$errors = array_reverse( $errors );
				$errors = implode( "<br />", $errors );
				echo $errors;
				echo "</div>";

			}
			else {

				?>
				<?php $this->display_title(); ?>
				<p>
				<?php _e( 'Meow Apps is run by <a href="http://jordymeow.com">Jordy Meow</a>, a photographer and software developer based in Japan. When he realized that WordPress was an environment not so friendly to photographers, Meow Apps was born. It is a suite of plugins dedicate to make the blogging life of image lovers easy and pretty. Meow Apps also teams up with the best players in the community (other themes or plugins developers). For more information, please check <a href="http://meowapps.com" target="_blank">Meow Apps</a>.', 'meowapps' )
				?>
				</p>
				<div class="meow-row">
					<div class="meow-box meow-col meow-span_1_of_2 ">
						<h3 class=""><span class="dashicons dashicons-camera"></span> UI Plugins </h3>
						<ul class="">
							<li><b>WP/LR Sync</b> <?php echo $this->check_install( 'wplr-sync' ) ?><br />
								Bring synchronization from Lightroom to WordPress.</li>
							<li><b>Meow Lightbox</b> <?php echo $this->check_install( 'meow-lightbox' ) ?><br />
								Lightbox with EXIF information nicely displayed.</li>
							<li><b>Meow Gallery</b> <?php echo $this->check_install( 'meow-gallery' ) ?><br />
								Simple gallery to make your photos look better (Masonry and others).</li>
							<li><b>Audio Story for Images</b> <?php echo $this->check_install( 'audio-story-images' ) ?><br />
								Add audio to your images.</li>
						</ul>
					</div>
					<div class="meow-box meow-col meow-span_1_of_2">
						<h3 class=""><span class="dashicons dashicons-admin-tools"></span> System Plugins</h3>
						<ul class="">
							<li><b>Media File Renamer</b> <?php echo $this->check_install( 'media-file-renamer' ) ?><br />
								Nicer filenames and better SEO, automatically.</li>
							<li><b>Media Cleaner</b> <?php echo $this->check_install( 'media-cleaner' ) ?><br />
								Detect the files you are not using to clean your system.</li>
							<li><b>WP Retina 2x</b> <?php echo $this->check_install( 'wp-retina-2x' ) ?><br />
								Make your website perfect for retina devices.</li>
							<li><b>WP Category Permalink</b> <?php echo $this->check_install( 'wp-category-permalink' ) ?><br />
								Allows you to select a main category (or taxonomy) for nicer permalinks.</li>
						</ul>
					</div>
				</div>

				<div class="meow-row">
					<div class="meow-box meow-col meow-span_2_of_3">
						<h3><span class="dashicons dashicons-admin-tools"></span> Common</h3>
						<div class="inside">
							<form method="post" action="options.php">
								<?php settings_fields( 'meowapps_common_settings' ); ?>
								<?php do_settings_sections( 'meowapps_common_settings-menu' ); ?>
								<?php submit_button(); ?>
							</form>
						</div>
					</div>

					<div class="meow-box meow-col meow-span_1_of_3">
						<h3><span class="dashicons dashicons-admin-tools"></span> Debug</h3>
						<div class="inside">
							<ul>
								<li><a href="?page=meowapps-main-menu&amp;tool=error_log">Display Error Log</a></li>
								<li><a href="?page=meowapps-main-menu&amp;tool=phpinfo">Display PHP Info</a></li>
							</ul>
						</div>
					</div>
				</div>

				<?php

			}

			echo "<br /><small style='color: lightgray;'>Meow Admin " . Meow_Admin::$version . "</small></div>";
		}

		// HELPERS

		static function size_shortname( $name ) {
			$name = preg_split( '[_-]', $name );
			$short = strtoupper( substr( $name[0], 0, 1 ) );
			if ( count( $name ) > 1 )
				$short .= strtoupper( substr( $name[1], 0, 1 ) );
			return $short;
		}

	}

}

?>
