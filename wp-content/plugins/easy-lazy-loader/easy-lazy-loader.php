<?php
/*
Plugin Name: Easy Lazy Loader
Version: 1.1.2
Plugin URI: https://www.iprodev.com/go/easy-lazy-loader/
Author: iProDev
Author URI: https://www.iprodev.com/
Description: Lazy load images, iframes, videos and audios to improve page load times.
Text Domain: easy-lazy-loader
Domain Path: /languages
License: GPL3
*/

if ( ! class_exists( 'EasyLazyLoader' ) ) {


	class EasyLazyLoader {
		const version = '1.1.2';

		public $MAIN;
		public $PATH;
		public $BASE;
		public $SLUG = "easylazyloader";
		private $DEFAULT_OPTIONS = array(
			'apply_to_content' => true,
			'apply_to_text_widgets' => true,
			'apply_to_post_thumbnails' => true,
			'apply_to_gravatars' => true,
			'lazy_load_images' => true,
			'lazy_load_iframes' => true,
			'lazy_load_videos' => true,
			'lazy_load_audios' => true,
			'skip_classes' => 'no-lazy, wgextra-img',
			'placeholder_type' => 'default',
			'placeholder_url' => '',
			'placeholder_image_size' => 'tiny-lazy',
			'default_image_placeholder_color' => '#571845',
			'default_iframe_placeholder_color' => '#D3D2D1',
			'default_video_placeholder_color' => '#D50D1A',
			'default_audio_placeholder_color' => '#0099FF',
			'threshold' => 100,
			'debug_mode' => false,
			'delete_data' => true
		);

		/**
		 * The Easy Lazy Loader constructor function
		 *
		 * @param   string   $file  The plugin file path
		 * @return  object          Returns all Easy Lazy Loader public methods and properties.
		 */
		function __construct( $file ) {
			$this->MAIN = $file;
			$this->BASE = plugin_basename( $file );
			$this->PATH = str_replace( DIRECTORY_SEPARATOR, '/', dirname( $file ) );
			$this->OPTIONS   = $this->DEFAULT_OPTIONS;

			if ( $options = get_option( "{$this->SLUG}_options" ) ) {
				$this->OPTIONS = array_replace_recursive( $this->DEFAULT_OPTIONS, $options );
			}

			/**
			 * Add all hooks
			 */
			register_activation_hook( $file, array(
				 $this,
				'activate' 
			) );
			register_deactivation_hook( $file, array(
				 $this,
				'uninstall' 
			) );

			if ( is_admin() ) {
				add_action( 'admin_menu', array(
					 $this,
					'admin_menu' 
				) );
				add_action( 'wp_ajax_' . $this->SLUG, array(
					 $this,
					'ajax_actions' 
				) );

				add_action( 'admin_enqueue_scripts', array(
					 $this,
					'admin_head' 
				) );

				add_filter( 'plugin_action_links', array(
					 $this,
					'action_links' 
				), 10, 2 );
				add_filter( 'plugin_row_meta', array(
					 $this,
					'register_plugin_links' 
				), 10, 2 );
			} else {
				add_action( 'wp', array(
					$this, 'init'
				), 99 ); // run this as late as possible
			}

			require_once 'includes/cron.class.php';

			// Add cron if its not there
			new iProDevNotify( $file );

			$sizes = $this->get_image_sizes();
			if ( !isset( $sizes[$this->DEFAULT_OPTIONS['placeholder_image_size']] ) ) {
			 	add_image_size( $this->DEFAULT_OPTIONS['placeholder_image_size'], 30, 30 );
			}
		}

		/**
		 * Activating handler.
		 * @return void
		 */
		public function activate() {
			/* install the default options */
			if ( !get_option( "{$this->SLUG}_options" ) ) {
				add_option( "{$this->SLUG}_options", $this->DEFAULT_OPTIONS, '', 'yes' );
			}
		}

		/**
		 * Uninstalling handler.
		 * @return void
		 */
		public function uninstall() {
			/* delete plugin options */
			if ( $this->OPTIONS['delete_data'] ) {
				delete_site_option( "{$this->SLUG}_options" );
				delete_option( "{$this->SLUG}_options" );
			}

			//Clear iProDevNotify
			iProDevNotify::clear_schedule_cron( __FILE__ );
		}

		/**
		 * Add menu and submenu.
		 * @return void
		 */
		public function admin_menu() {
			add_options_page( __( 'Easy Lazy Loader', 'easy-lazy-loader' ), __( 'Easy Lazy Loader', 'easy-lazy-loader' ), 'manage_options', $this->SLUG, array(
				 $this,
				'page_init' 
			) );
		}

		/**
		 * Add action links on plugin page in to Plugin Name block
		 * @param  $links array() action links
		 * @param  $file  string  relative path to pugin "easy-lazy-loader/easy-lazy-loader.php"
		 * @return $links array() action links
		 */
		public function action_links( $links, $file ) {
			if ( $file == $this->BASE ) {
				$settings_link = "<a href=\"options-general.php?page={$this->SLUG}\">" . __( 'Settings', 'easy-lazy-loader' ) . "</a>";
				array_unshift( $links, $settings_link );
			}

			return $links;
		}
		
		/**
		 * Add action links on plugin page in to Plugin Description block
		 * @param  $links array() action links
		 * @param  $file  string  relative path to pugin "easy-lazy-loader/easy-lazy-loader.php"
		 * @return $links array() action links
		 */
		public function register_plugin_links( $links, $file ) {
			if ( $file == $this->BASE ) {
				$links[] = "<a href=\"options-general.php?page={$this->SLUG}\">" . __( 'Settings', 'easy-lazy-loader' ) . "</a>";
			}
			return $links;
		}

		/**
		 * Page contents initialize.
		 *
		 * @return  void
		 */
		public function page_init() {
			echo '<div class="wrap" id="easylazyloader">';
			echo '<h2>' . __( "Easy Lazy Loader Settings", 'easy-lazy-loader' ) . '</h2>';
			echo '<div><div id="post-body">';

			$display_add_options = $message = $error = $result = '';

			$options = $this->OPTIONS;
			$sizes = $this->get_image_sizes();
			$possible_sizes_names = apply_filters( 'image_size_names_choose', array(
				'thumbnail'       => __('Thumbnail'),
				'medium'          => __('Medium'),
				'large'           => __('Large')
			) );
	?>
			<div id="easylazyloader-settings-notice" class="easylazyloader-yellow-box" style="display:none">
				<strong><?php _e( "Notice:", 'easy-lazy-loader' ); ?></strong> <?php _e( "The plugin's settings have been changed. In order to save them please don't forget to click the 'Save Changes' button.", 'easy-lazy-loader' ); ?>
			</div>

			<div class="easylazyloader-box">
				<div class="box-title"><h3><?php _e( 'Settings', 'easy-lazy-loader' ); ?></h3></div>
				<div class="inside">
					
					<form id="easylazyloader_form" method="post" action="">
						<input type="hidden" name="easylazyloader_task" value="settings">
						<table class="form-table">
							<tr valign="top">
								<th scope="row"><label for="apply_to_content"><?php _e( "Apply to content", 'easy-lazy-loader' ); ?></label></th>
								<td>
									<div class="onoffswitch">
										<input type="checkbox" name="apply_to_content" class="onoffswitch-checkbox" id="apply_to_content"<?php checked( $options['apply_to_content'], true ); ?>>
										<label class="onoffswitch-label" for="apply_to_content">
											<div class="onoffswitch-inner">
												<div class="onoffswitch-active">ON</div>
												<div class="onoffswitch-inactive">OFF</div>
											</div>
											<div class="onoffswitch-switch"></div>
										</label>
									</div>
								</td>

								<th scope="row"><label for="apply_to_text_widgets"><?php _e( "Apply to text widgets", 'easy-lazy-loader' ); ?></label></th>
								<td>
									<div class="onoffswitch">
										<input type="checkbox" name="apply_to_text_widgets" class="onoffswitch-checkbox" id="apply_to_text_widgets"<?php checked( $options['apply_to_text_widgets'], true ); ?>>
										<label class="onoffswitch-label" for="apply_to_text_widgets">
											<div class="onoffswitch-inner">
												<div class="onoffswitch-active">ON</div>
												<div class="onoffswitch-inactive">OFF</div>
											</div>
											<div class="onoffswitch-switch"></div>
										</label>
									</div>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row"><label for="apply_to_post_thumbnails"><?php _e( "Apply to post thumbnails", 'easy-lazy-loader' ); ?></label></th>
								<td>
									<div class="onoffswitch">
										<input type="checkbox" name="apply_to_post_thumbnails" class="onoffswitch-checkbox" id="apply_to_post_thumbnails"<?php checked( $options['apply_to_post_thumbnails'], true ); ?>>
										<label class="onoffswitch-label" for="apply_to_post_thumbnails">
											<div class="onoffswitch-inner">
												<div class="onoffswitch-active">ON</div>
												<div class="onoffswitch-inactive">OFF</div>
											</div>
											<div class="onoffswitch-switch"></div>
										</label>
									</div>
								</td>

								<th scope="row"><label for="apply_to_gravatars"><?php _e( "Apply to gravatars", 'easy-lazy-loader' ); ?></label></th>
								<td>
									<div class="onoffswitch">
										<input type="checkbox" name="apply_to_gravatars" class="onoffswitch-checkbox" id="apply_to_gravatars"<?php checked( $options['apply_to_gravatars'], true ); ?>>
										<label class="onoffswitch-label" for="apply_to_gravatars">
											<div class="onoffswitch-inner">
												<div class="onoffswitch-active">ON</div>
												<div class="onoffswitch-inactive">OFF</div>
											</div>
											<div class="onoffswitch-switch"></div>
										</label>
									</div>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row"><label for="lazy_load_images"><?php _e( "Lazy load images", 'easy-lazy-loader' ); ?></label></th>
								<td>
									<div class="onoffswitch">
										<input type="checkbox" name="lazy_load_images" class="onoffswitch-checkbox" id="lazy_load_images"<?php checked( $options['lazy_load_images'], true ); ?>>
										<label class="onoffswitch-label" for="lazy_load_images">
											<div class="onoffswitch-inner">
												<div class="onoffswitch-active">ON</div>
												<div class="onoffswitch-inactive">OFF</div>
											</div>
											<div class="onoffswitch-switch"></div>
										</label>
									</div>
								</td>

								<th scope="row"><label for="lazy_load_iframes"><?php _e( "Lazy load iframes", 'easy-lazy-loader' ); ?></label></th>
								<td>
									<div class="onoffswitch">
										<input type="checkbox" name="lazy_load_iframes" class="onoffswitch-checkbox" id="lazy_load_iframes"<?php checked( $options['lazy_load_iframes'], true ); ?>>
										<label class="onoffswitch-label" for="lazy_load_iframes">
											<div class="onoffswitch-inner">
												<div class="onoffswitch-active">ON</div>
												<div class="onoffswitch-inactive">OFF</div>
											</div>
											<div class="onoffswitch-switch"></div>
										</label>
									</div>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row"><label for="lazy_load_videos"><?php _e( "Lazy load videos", 'easy-lazy-loader' ); ?></label></th>
								<td>
									<div class="onoffswitch">
										<input type="checkbox" name="lazy_load_videos" class="onoffswitch-checkbox" id="lazy_load_videos"<?php checked( $options['lazy_load_videos'], true ); ?>>
										<label class="onoffswitch-label" for="lazy_load_videos">
											<div class="onoffswitch-inner">
												<div class="onoffswitch-active">ON</div>
												<div class="onoffswitch-inactive">OFF</div>
											</div>
											<div class="onoffswitch-switch"></div>
										</label>
									</div>
								</td>

								<th scope="row"><label for="lazy_load_audios"><?php _e( "Lazy load audios", 'easy-lazy-loader' ); ?></label></th>
								<td>
									<div class="onoffswitch">
										<input type="checkbox" name="lazy_load_audios" class="onoffswitch-checkbox" id="lazy_load_audios"<?php checked( $options['lazy_load_audios'], true ); ?>>
										<label class="onoffswitch-label" for="lazy_load_audios">
											<div class="onoffswitch-inner">
												<div class="onoffswitch-active">ON</div>
												<div class="onoffswitch-inactive">OFF</div>
											</div>
											<div class="onoffswitch-switch"></div>
										</label>
									</div>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row"><label for="skip_classes"><?php _e( "Skip classes", 'easy-lazy-loader' ); ?></label></th>
								<td colspan="3">
									<input type="text" name="skip_classes" id="skip_classes" value="<?php echo esc_attr( $options['skip_classes'] ); ?>"/>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row"><label><?php _e( "Image Placeholder Type", 'easy-lazy-loader' ); ?></label></th>
								<td colspan="3">
									<div class="switch-field">
										<input type="radio" id="placeholder_type-default" name="placeholder_type" value="default"<?php checked( $options['placeholder_type'], 'default' ); ?> />
										<label for="placeholder_type-default"><?php _e( 'Default', 'easy-lazy-loader' ); ?></label>
										<input type="radio" id="placeholder_type-image" name="placeholder_type" value="image"<?php checked( $options['placeholder_type'], 'image' ); ?> />
										<label for="placeholder_type-image"><?php _e( 'Image', 'easy-lazy-loader' ); ?></label>
										<input type="radio" id="placeholder_type-lowres" name="placeholder_type" value="lowres"<?php checked( $options['placeholder_type'], 'lowres' ); ?> />
										<label for="placeholder_type-lowres"><?php _e( 'Low-res preview image', 'easy-lazy-loader' ); ?></label>
										<input type="radio" id="placeholder_type-color" name="placeholder_type" value="color"<?php checked( $options['placeholder_type'], 'color' ); if ( !class_exists( 'WordPress_Gallery_Extra' ) ) { echo ' disabled="disabled"'; } ?> />
										<label for="placeholder_type-color"<?php if ( !class_exists( 'WordPress_Gallery_Extra' ) ) { echo ' class="disabled" title="' . esc_attr__( "This option is not available because it's need WordPress Gallery Extra plugin.", 'easy-lazy-loader' ) . '"'; } ?>><?php _e( 'Color', 'easy-lazy-loader' ); ?></label>
									</div>
									<p class="description"><b><?php _e( "Notice:", 'easy-lazy-loader' ); ?></b> <?php printf( __( 'If you want to use "Color" placeholder type you need to <a target="_blank" rel="noreferrer noopener" href="%s">Download and activate WordPress Gallery Extra</a> to enable this feature.', 'easy-lazy-loader' ), esc_url( "https://goo.gl/Kw5dtx" ) ); ?></p>
								</td>
							</tr>
							<tr class="field" rel="placeholder_url" valign="top">
								<th scope="row"><label for="placeholder_url"><?php _e( "Placeholder Image URL", 'easy-lazy-loader' ); ?></label></th>
								<td colspan="3">
									<input type="text" name="placeholder_url" id="placeholder_url" value="<?php echo esc_attr( $options['placeholder_url'] ); ?>"/>
									<a id="placeholder_url_media" class="button"><?php _e( 'Select image', 'easy-lazy-loader' ); ?></a><br />
									<p class="description"><?php _e( "Leave blank for default.", 'easy-lazy-loader' ); ?></p>
								</td>
							</tr>
							<tr class="field" rel="placeholder_image_size" valign="top">
								<th scope="row"><label for="placeholder_image_size"><?php _e( "Placeholder Image Size", 'easy-lazy-loader' ); ?></label></th>
								<td colspan="3">
									<div class="select-style">
										<select name="placeholder_image_size" id="placeholder_image_size">
<?php
			foreach ( $sizes as $key => $size ) {
				$size_name = isset( $possible_sizes_names[$key] ) ? $possible_sizes_names[$key] : $key;
?>
											<option value="<?php echo $key; ?>"<?php selected( $options['placeholder_image_size'], $key ); ?>><?php echo $size_name; ?></option>
<?php
			}
?>
										</select>
									</div><br />
									<p class="description"><?php _e( "Leave blank for default.", 'easy-lazy-loader' ); ?></p>
								</td>
							</tr>
							<tr class="field" rel="default_image_placeholder_color" valign="top">
								<th scope="row"><label for="default_image_placeholder_color"><?php _e( "Default Images Placeholder Color", 'easy-lazy-loader' ); ?></label></th>
								<td colspan="3">
									<input type="text" class="color-field" name="default_image_placeholder_color" id="default_image_placeholder_color" value="<?php echo esc_attr( $options['default_image_placeholder_color'] ); ?>"/><br />
									<p class="description"><?php _e( "Default placeholder color for images when placeholder color not available.", 'easy-lazy-loader' ); ?></p>
								</td>
							</tr>
							<tr class="field" rel="default_iframe_placeholder_color" valign="top">
								<th scope="row"><label for="default_iframe_placeholder_color"><?php _e( "Default iframes Placeholder Color", 'easy-lazy-loader' ); ?></label></th>
								<td colspan="3">
									<input type="text" class="color-field" name="default_iframe_placeholder_color" id="default_iframe_placeholder_color" value="<?php echo esc_attr( $options['default_iframe_placeholder_color'] ); ?>"/><br />
									<p class="description"><?php _e( "Default placeholder color for iframes.", 'easy-lazy-loader' ); ?></p>
								</td>
							</tr>
							<tr class="field" rel="default_video_placeholder_color" valign="top">
								<th scope="row"><label for="default_video_placeholder_color"><?php _e( "Default Videos Placeholder Color", 'easy-lazy-loader' ); ?></label></th>
								<td colspan="3">
									<input type="text" class="color-field" name="default_video_placeholder_color" id="default_video_placeholder_color" value="<?php echo esc_attr( $options['default_video_placeholder_color'] ); ?>"/><br />
									<p class="description"><?php _e( "Default placeholder color for videos.", 'easy-lazy-loader' ); ?></p>
								</td>
							</tr>
							<tr class="field" rel="default_audio_placeholder_color" valign="top">
								<th scope="row"><label for="default_audio_placeholder_color"><?php _e( "Default Audios Placeholder Color", 'easy-lazy-loader' ); ?></label></th>
								<td colspan="3">
									<input type="text" class="color-field" name="default_audio_placeholder_color" id="default_audio_placeholder_color" value="<?php echo esc_attr( $options['default_audio_placeholder_color'] ); ?>"/><br />
									<p class="description"><?php _e( "Default placeholder color for audios.", 'easy-lazy-loader' ); ?></p>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row"><label for="threshold"><?php _e( "Threshold", 'easy-lazy-loader' ); ?></label></th>
								<td colspan="3">
									<input type="number" name="threshold" id="threshold" value="<?php echo esc_attr( $options['threshold'] ); ?>"/><br />
									<p class="description"><?php _e( "How close to the viewport the element should be when we load it. In pixels. Example: 100", 'easy-lazy-loader' ); ?></p>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row"><label for="debug_mode"><?php _e( "Debug Mode", 'easy-lazy-loader' ); ?></label></th>
								<td colspan="3">
									<div class="onoffswitch">
										<input type="checkbox" name="debug_mode" class="onoffswitch-checkbox" id="debug_mode"<?php checked( $options['debug_mode'], true ); ?>>
										<label class="onoffswitch-label" for="debug_mode">
											<div class="onoffswitch-inner">
												<div class="onoffswitch-active">ON</div>
												<div class="onoffswitch-inactive">OFF</div>
											</div>
											<div class="onoffswitch-switch"></div>
										</label>
									</div>
									<p class="description"><?php _e( "This option allows to use un-minified css and javascripts for developers or in order to debug an issue.", 'easy-lazy-loader' ); ?></p>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row"><label for="delete_data"><?php _e( "Delete Data", 'easy-lazy-loader' ); ?></label></th>
								<td colspan="3">
									<div class="onoffswitch">
										<input type="checkbox" name="delete_data" class="onoffswitch-checkbox" id="delete_data"<?php checked( $options['delete_data'], true ); ?>>
										<label class="onoffswitch-label" for="delete_data">
											<div class="onoffswitch-inner">
												<div class="onoffswitch-active">ON</div>
												<div class="onoffswitch-inactive">OFF</div>
											</div>
											<div class="onoffswitch-switch"></div>
										</label>
									</div>
									<p class="description"><?php _e( 'Delete all the data from the database. Even if you delete "Easy Lazy Loader" from the plugin page, all the data stored in the database will be still available, so you won\'t lose your settings. But if you want to permanently delete "Easy Lazy Loader" and all the data stored in your database, before deactivating and deleting the plugin switch on this field.', 'easy-lazy-loader' ); ?></p>
								</td>
							</tr>
						</table>
						<p class="submit">
							<input type="submit" id="settings-form-submit" class="button-primary" value="<?php _e( 'Save Changes', 'easy-lazy-loader' ); ?>" />
							<input type="hidden" name="easylazyloader_form_submit" value="submit" />
							<?php wp_nonce_field( plugin_basename( __FILE__ ), 'easylazyloader_save_settings' ); ?>
							<span class="circle-loader"><span class="checkmark draw"></span></span>
							<span class="easylazyloader_ajax_message">Error</span>
						</p>
					</form>
				</div><!-- end of inside -->
			</div><!-- end of postbox -->

			<?php
			echo '</div></div>'; //<!-- end of #poststuff and #post-body -->
			echo '</div>'; //<!--  end of .wrap #easylazyloader -->
		}
		
		/**
		 * Function to add plugin scripts
		 * @return void
		 */
		public function admin_head() {
			if ( isset( $_REQUEST['page'] ) && 'easylazyloader' == $_REQUEST['page'] ) {
				wp_enqueue_media();
				wp_enqueue_style( 'wp-color-picker' ); 
				wp_enqueue_style( 'easylazyloader_stylesheet', plugins_url( 'css/style.css', __FILE__ ), null, self::version );
				wp_enqueue_script( 'easylazyloader_script', plugins_url( 'js/script.js', __FILE__ ), array(
					'jquery',
					'wp-color-picker'
				), self::version );
			}
		}

		/**
		 * Initialize the setup
		 */
		public function init() {

			/* We do not touch the feeds and previews */
			if ( is_feed() || is_preview() || is_admin() ) {
				return;
			}

			/**
			 * Filter to let plugins decide whether the plugin should run for this request or not
			 *
			 * @param bool $enabled Whether the plugin should run for this request
			 */
			$enabled = apply_filters( 'easylazyloader_is_enabled', true );

			if ( $enabled && !self::is_wptouch() && !self::is_mobilepress() && !self::is_wpprint() && !self::is_operamini() ) {
				add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

				$this->setup_filtering();
			}
		}

		public function enqueue_scripts() {
			wp_enqueue_script( 'jquery-sonar', self::get_url( $this->OPTIONS['debug_mode'] ? 'js/jquery.sonar.js' : 'js/jquery.sonar.min.js' ), array( 'jquery' ), '3.0.0', true );
			wp_enqueue_script( $this->SLUG,  self::get_url( $this->OPTIONS['debug_mode'] ? 'js/easy-lazy-loader.js' : 'js/easy-lazy-loader.min.js' ), array( 'jquery', 'jquery-sonar' ), self::version, true );

			wp_localize_script( $this->SLUG, $this->SLUG, array(
				'threshold' => $this->OPTIONS['threshold']
			) );
		}

		/**
		 * Set up filtering for certain content
		 */
		protected function setup_filtering() {

			if ( ! is_admin() ) {

				if ( $this->OPTIONS['apply_to_content'] ) {
					add_filter( 'the_content', array( $this, 'filter' ), 200 );
				}

				if ( $this->OPTIONS['apply_to_text_widgets'] ) {
					add_filter( 'widget_text', array( $this, 'filter' ), 200 );
				}

				if ( $this->OPTIONS['apply_to_post_thumbnails'] ) {
					add_filter( 'post_thumbnail_html', array( $this, 'filter' ), 200 );
				}

				if ( $this->OPTIONS['apply_to_gravatars'] ) {
					add_filter( 'get_avatar', array( $this, 'filter' ), 200 );
				}

				add_filter( 'easy_lazy_loader_html', array( $this, 'filter' ) );

			}

		}

		/**
		 * Filter HTML content. Replace supported content with placeholders.
		 *
		 * @param string $content The HTML string to filter
		 * @return string The filtered HTML string
		 */
		public function filter( $content ) {

			// Last chance to bail out before running the filter
			$run_filter = apply_filters( 'easy_lazy_loader_run_filter', true );
			if ( ! $run_filter ) {
				return $content;
			}

			/**
			 * Filter the content
			 *
			 * @param string $content The HTML string to filter
			 */
			$content = $this->filter_elements( $content );

			return $content;
		}

		/**
		 * Replace elements with placeholders in the content
		 *
		 * @param string $content The HTML to do the filtering on
		 * @return string The HTML with the elements replaced
		 */
		public function filter_elements( $content ) {
			$match_content = $this->get_content_haystack( $content );

			if ( $this->OPTIONS['lazy_load_images'] ) {
				$content = $this->filter_images( $content, $match_content );
			}

			if ( $this->OPTIONS['lazy_load_iframes'] ) {
				$content = $this->filter_iframes( $content, $match_content );
			}

			if ( $this->OPTIONS['lazy_load_videos'] ) {
				$content = $this->filter_videos( $content, $match_content );
			}

			if ( $this->OPTIONS['lazy_load_audios'] ) {
				$content = $this->filter_audios( $content, $match_content );
			}

			return $content;
		}

		/**
		 * Replace images with placeholders in the content
		 *
		 * @param string $content The HTML to do the filtering on
		 * @param string $match_content
		 * @return string The HTML with the images replaced
		 */
		public function filter_images( $content, $match_content ) {
			$placeholder_type = $this->OPTIONS['placeholder_type'];
			$placeholder_url = "";

			if ( 'image' === $placeholder_type ) {
				$placeholder_url = $this->OPTIONS['placeholder_url'];
			}

			if ( ! strlen( $placeholder_url ) ) {
				$placeholder_url = 'data:image/gif;base64,R0lGODdhAQABAPAAAP///wAAACwAAAAAAQABAEACAkQBADs=';
			}

			$matches = array();
			preg_match_all( '/<img[\s\r\n]+.*?>/is', $match_content, $matches );
			
			$search = array();
			$replace = array();

			foreach ( $matches[0] as $imgHTML ) {
				preg_match( '/ width=["|\']([^("|\')]+)["|\']/i', $imgHTML, $width_match );
				preg_match( '/ height=["|\']([^("|\')]+)["|\']/i', $imgHTML, $height_match );
				$width = !empty( $width_match ) && isset( $width_match[1] ) ? intval( $width_match[1] ) : '2';
				$height = !empty( $height_match ) && isset( $height_match[1] ) ? intval( $height_match[1] ) : '1';
				
				// don't to the replacement if the image is a data-uri
				if ( ! preg_match( "/src=['\"]data:image/is", $imgHTML ) ) {
					
					$placeholder_url_used = $placeholder_url;
					if ( in_array( $placeholder_type, array( 'lowres', 'color', 'default' ) ) ) {
						$img_id = false;
						if( preg_match( '/class=["\'].*?wp-image-([0-9]*)/is', $imgHTML, $id_matches ) )
							$img_id = intval( $id_matches[1] );
						else if ( preg_match( '/src=(["\'])(.*?)["\']/is', $imgHTML, $src_matches ) )
							$img_id = $this->get_attachment_id_from_src( $src_matches[2] );

						// use low res preview image as placeholder if applicable
						if ( 'lowres' === $placeholder_type && $img_id !== false ) {
							$tiny_img_data  = wp_get_attachment_image_src( $img_id, $this->OPTIONS['placeholder_image_size'] );
							$tiny_url = $tiny_img_data[0];
							$placeholder_url_used = $tiny_url;
						}
						// use color as placeholder if applicable
						else if ( 'color' === $placeholder_type && class_exists( 'WordPress_Gallery_Extra' ) ) {
							$placeholder_color = "";

							if( $img_id !== false ) {
								$placeholder_color = get_post_meta( $img_id, '_wgextra_dominant_color', true );
							}

							if ( !$placeholder_color ) {
								$placeholder_color = $this->OPTIONS['default_image_placeholder_color'];
							}

							$placeholder_url_used = self::create_placeholder( $placeholder_color, (int) $width, (int) $height, 'Image loading...' );
						}
						// use transparent as placeholder if applicable
						else if ( 'default' === $placeholder_type && !is_string( $width ) && !is_string( $height ) ) {
							if ( isset( $placeholder_color ) ) {
								$placeholder_url_used = self::create_placeholder( $placeholder_color, (int) $width, (int) $height );
							}
						}
					}

					$placeholder_url_used = apply_filters( 'ell_placeholder_url', $placeholder_url_used, 'image' );

					// replace the src and add the data-src attribute
					$replaceHTML = preg_replace( '/<img(.*?)src=/is', '<img$1src="' . esc_attr( $placeholder_url_used ) . '" data-lazy-type="image" data-lazy-src=', $imgHTML );
					
					// also replace the srcset (responsive images)
					$replaceHTML = str_replace( 'srcset', 'data-lazy-srcset', $replaceHTML );
					
					// add the lazy class to the img element
					if ( preg_match( '/class=["\']/i', $replaceHTML ) ) {
						$replaceHTML = preg_replace( '/class=(["\'])(.*?)["\']/is', 'class=$1lazy lazy-hidden $2$1', $replaceHTML );
					} else {
						$replaceHTML = preg_replace( '/<img/is', '<img class="lazy lazy-hidden"', $replaceHTML );
					}
					
					$replaceHTML .= '<noscript>' . $imgHTML . '</noscript>';
					
					array_push( $search, $imgHTML );
					array_push( $replace, $replaceHTML );
				}
			}

			$content = str_replace( $search, $replace, $content );

			return $content;

		}

		/**
		 * Replace iframes with placeholders in the content
		 *
		 * @param string $content The HTML to do the filtering on
		 * @param string $match_content
		 * @return string The HTML with the iframes replaced
		 */
		public function filter_iframes( $content, $match_content ) {
			$placeholder_type = $this->OPTIONS['placeholder_type'];
			$placeholder_url = "";

			if ( 'image' === $placeholder_type ) {
				$placeholder_url = $this->OPTIONS['placeholder_url'];
			}

			if ( ! strlen( $placeholder_url ) ) {
				$placeholder_url = 'data:image/gif;base64,R0lGODlhAgABAIAAAP///wAAACH5BAEAAAAALAAAAAACAAEAAAICBAoAOw==';
			}

			$matches = array();
			preg_match_all( '|<iframe\s+.*?</iframe>|si', $match_content, $matches );
			
			$search = array();
			$replace = array();
			
			foreach ( $matches[0] as $iframeHTML ) {

				// Don't mess with the Gravity Forms ajax iframe
				if ( strpos( $iframeHTML, 'gform_ajax_frame' ) ) {
					continue;
				}

				preg_match( '/width=["|\']([^("|\')]+)["|\']/i', $iframeHTML, $width_match );
				preg_match( '/height=["|\']([^("|\')]+)["|\']/i', $iframeHTML, $height_match );
				$width = !empty( $width_match ) && isset( $width_match[1] ) ? intval( $width_match[1] ) : '720';
				$height = !empty( $height_match ) && isset( $height_match[1] ) ? intval( $height_match[1] ) : '405';

				if ( 'color' === $placeholder_type && class_exists( 'WordPress_Gallery_Extra' ) ) {
					$placeholder_url = self::create_placeholder( $this->OPTIONS['default_iframe_placeholder_color'], (int) $width, (int) $height, "iframe loading..." );
				} else if ( 'default' === $placeholder_type && !is_string( $width ) && !is_string( $height ) ) {
					$placeholder_url = self::create_placeholder( null, (int) $width, (int) $height );
				}

				$placeholder_url = apply_filters( 'ell_placeholder_url', $placeholder_url, 'iframe' );

				$replaceHTML = '<img src="' . esc_attr( $placeholder_url ) . '" width="' . $width . '" height="' . $height . '" class="lazy lazy-hidden" data-lazy-type="iframe" data-lazy-src="' . esc_attr( $iframeHTML ) . '" alt="">';
				
				$replaceHTML .= '<noscript>' . $iframeHTML . '</noscript>';

				//error_log('replace html is ' . $replaceHTML);
				
				array_push( $search, $iframeHTML );
				array_push( $replace, $replaceHTML );
			}
			
			$content = str_replace( $search, $replace, $content );

			return $content;

		}

		/**
		 * Replace videos with placeholders in the content
		 *
		 * @param string $content The HTML to do the filtering on
		 * @param string $match_content
		 * @return string The HTML with the videos replaced
		 */
		public function filter_videos( $content, $match_content ) {
			$placeholder_type = $this->OPTIONS['placeholder_type'];
			$placeholder_url = "";

			if ( 'image' === $placeholder_type ) {
				$placeholder_url = $this->OPTIONS['placeholder_url'];
			}

			if ( ! strlen( $placeholder_url ) ) {
				$placeholder_url = 'data:image/gif;base64,R0lGODlhAgABAIAAAP///wAAACH5BAEAAAAALAAAAAACAAEAAAICBAoAOw==';
			}

			$matches = array();
			preg_match_all( '|<video\s+.*?</video>|si', $match_content, $matches );
			
			$search = array();
			$replace = array();
			
			foreach ( $matches[0] as $videoHTML ) {
				preg_match( '/width=["|\']([^("|\')]+)["|\']/i', $videoHTML, $width_match );
				preg_match( '/height=["|\']([^("|\')]+)["|\']/i', $videoHTML, $height_match );
				$width = !empty( $width_match ) && isset( $width_match[1] ) ? intval( $width_match[1] ) : '720';
				$height = !empty( $height_match ) && isset( $height_match[1] ) ? intval( $height_match[1] ) : '405';

				if ( 'color' === $placeholder_type && class_exists( 'WordPress_Gallery_Extra' ) ) {
					$placeholder_url = self::create_placeholder( $this->OPTIONS['default_video_placeholder_color'], (int) $width, (int) $height, "Video loading..." );
				} else if ( 'default' === $placeholder_type && !is_string( $width ) && !is_string( $height ) ) {
					$placeholder_url = self::create_placeholder( null, (int) $width, (int) $height );
				}

				$placeholder_url = apply_filters( 'ell_placeholder_url', $placeholder_url, 'video' );

				$replaceHTML = '<img src="' . esc_attr( $placeholder_url ) . '" width="' . $width . '" height="' . $height . '" class="lazy lazy-hidden" data-lazy-type="video" data-lazy-src="' . esc_attr( $videoHTML ) . '" alt="">';
				
				$replaceHTML .= '<noscript>' . $videoHTML . '</noscript>';
				
				array_push( $search, $videoHTML );
				array_push( $replace, $replaceHTML );
			}
			
			$content = str_replace( $search, $replace, $content );

			return $content;

		}

		/**
		 * Replace audios with placeholders in the content
		 *
		 * @param string $content The HTML to do the filtering on
		 * @param string $match_content
		 * @return string The HTML with the audios replaced
		 */
		public function filter_audios( $content, $match_content ) {
			$placeholder_type = $this->OPTIONS['placeholder_type'];
			$placeholder_url = "";

			if ( 'image' === $placeholder_type ) {
				$placeholder_url = $this->OPTIONS['placeholder_url'];
			} else if ( 'color' === $placeholder_type && class_exists( 'WordPress_Gallery_Extra' ) ) {
				$placeholder_url = self::create_placeholder( $this->OPTIONS['default_audio_placeholder_color'], 720, 32, "Audio loading..." );
			}

			if ( ! strlen( $placeholder_url ) ) {
				$placeholder_url = 'data:image/gif;base64,R0lGODlhGAABAIAAAP///wAAACH5BAEAAAAALAAAAAAYAAEAAAIEhI+pVwA7';
			}

			$placeholder_url = apply_filters( 'ell_placeholder_url', $placeholder_url, 'audio' );

			$matches = array();
			preg_match_all( '|<audio\s+.*?</audio>|si', $match_content, $matches );
			
			$search = array();
			$replace = array();
			
			foreach ( $matches[0] as $audioHTML ) {

				$replaceHTML = '<img src="' . esc_attr( $placeholder_url ) . '" width="720" height="32" class="lazy lazy-hidden" data-lazy-type="audio" data-lazy-src="' . esc_attr( $audioHTML ) . '" alt="">';
				
				$replaceHTML .= '<noscript>' . $audioHTML . '</noscript>';
				
				array_push( $search, $audioHTML );
				array_push( $replace, $replaceHTML );
			}
			
			$content = str_replace( $search, $replace, $content );

			return $content;

		}

		/**
		 * Remove elements we don’t want to filter from the HTML string
		 *
		 * We’re reducing the haystack by removing the hay we know we don’t want to look for needles in
		 *
		 * @param string $content The HTML string
		 * @return string The HTML string without the unwanted elements
		 */
		protected function get_content_haystack( $content ) {
			$content = $this->remove_noscript( $content );
			$content = $this->remove_skip_classes_elements( $content );

			return $content;
		}

		/**
		 * Remove <noscript> elements from HTML string
		 *
		 * @author sigginet
		 * @param string $content The HTML string
		 * @return string The HTML string without <noscript> elements
		 */
		public function remove_noscript( $content ) {
			return preg_replace( '/<noscript.*?(\/noscript>)/i', '', $content );
		}

		/**
		 * Remove HTML elements with certain classnames (or IDs) from HTML string
		 *
		 * @param string $content The HTML string
		 * @return string The HTML string without the unwanted elements
		 */
		public function remove_skip_classes_elements( $content ) {

			$skip_classes = array();

			$skip_classes_str = $this->OPTIONS['skip_classes'];
			
			if ( strlen( trim( $skip_classes_str ) ) ) {
				$skip_classes = array_map( 'trim', explode( ',', $skip_classes_str ) );
			}

			if ( !in_array( 'lazy', $skip_classes ) ) {
				$skip_classes[] = 'lazy';
			}

			/**
			 * Filter the class names to skip
			 *
			 * @param array $skip_classes The current classes to skip
			 */
			$skip_classes = apply_filters( 'ell_skip_classes', $skip_classes );

			/*
			http://stackoverflow.com/questions/1732348/regex-match-open-tags-except-xhtml-self-contained-tags/1732454#1732454
			We can’t do this, but we still do it.
			*/
			$skip_classes_quoted = array_map( 'preg_quote', $skip_classes );
			$skip_classes_ORed = implode( '|', $skip_classes_quoted );

			$regex = '/<\s*\w*\s*class\s*=\s*[\'"](|.*\s)' . $skip_classes_ORed . '(|\s.*)[\'"].*>/isU';

			return preg_replace( $regex, '', $content );
		}

		/**
		 * Get size information for all currently-registered image sizes.
		 *
		 * @global $_wp_additional_image_sizes
		 * @uses   get_intermediate_image_sizes()
		 * @return array $sizes Data for all currently-registered image sizes.
		 */
		public function get_image_sizes() {
			global $_wp_additional_image_sizes;

			$sizes = array();

			foreach ( get_intermediate_image_sizes() as $_size ) {
				if ( in_array( $_size, array('thumbnail', 'medium', 'medium_large', 'large') ) ) {
					$sizes[ $_size ]['width']  = get_option( "{$_size}_size_w" );
					$sizes[ $_size ]['height'] = get_option( "{$_size}_size_h" );
					$sizes[ $_size ]['crop']   = (bool) get_option( "{$_size}_crop" );
				} elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
					$sizes[ $_size ] = array(
						'width'  => $_wp_additional_image_sizes[ $_size ]['width'],
						'height' => $_wp_additional_image_sizes[ $_size ]['height'],
						'crop'   => $_wp_additional_image_sizes[ $_size ]['crop'],
					);
				}
			}

			return $sizes;
		}

		/**
		 * Create color placeholder.
		 *
		 * @param string $background The image background color
		 * @return string The placeholder datauri
		 */
		public static function create_placeholder( $background = '#FFF', $width = 1, $height = 1, $text = "" ) {
			if ( $width >= 30 && $height >= 30 ) {
				$new_dimensions = wp_constrain_dimensions( $width, $height, 30, 30 );
				$width = $new_dimensions[0];
				$height = $new_dimensions[1];
			}
			else {
				if ( $width >= $height ) {
					$width = $ratio = round( $width / $height );
					$height = 1;
				} else {
					$height = $ratio = round( $height / $width );
					$width = 1;
				}
			}

			// Create a new image instance
			$image = imagecreatetruecolor( $width, $height );

			if ( $background ) {
				// Parse color and fill bg
				$color = self::hex2rgb( $background );
				$back = imagecolorallocate( $image, $color['r'], $color['g'], $color['b'] );

				// Make the background
				imagefilledrectangle( $image, 0, 0, $width, $height, $back );
			} else {
				$black = imagecolorallocate( $image, 0, 0, 0 );
				imagecolortransparent( $image, $black );
			}

			/*if ( $text ) {
				$font_size = 15;
				$text_width = imagefontwidth( $font_size ) * strlen( $text );
				$text_height = imagefontheight( $font_size );

				if ( $width >= $text_width && $height >= $text_height ) {
					$brightness = (299 * $color['r'] + 587 * $color['g'] + 114 * $color['b']) / 1000;
					$text_color = $brightness > 127 ? array( 0, 0, 0 ) : array( 255, 255, 255 );
					$text_color = $brightness > 127 ? array( 0, 0, 0 ) : array( 255, 255, 255 );
					$textcolor = imagecolorallocate( $image, $text_color[0], $text_color[1], $text_color[2] );
					imagestring( $image, $font_size, ( $width / 2 ) - ( $text_width / 2 ), ( $height / 2 ) - ( $text_height / 2 ), $text, $textcolor );
				}
			}*/

			ob_start();

			imagegif( $image );

			$data = ob_get_contents();
			ob_end_clean();

			imagedestroy( $image );
			return "data:image/gif;base64," . base64_encode( $data );
		}

		public static function get_url( $path = '' ) {
			return plugins_url( ltrim( $path, '/' ), __FILE__ );
		}

		public static function hex2rgb( $hex ) {
			$hex = str_replace( "#", "", $hex );

			if( strlen( $hex ) == 3 ) {
				$r = hexdec( substr( $hex, 0, 1 ).substr( $hex, 0, 1 ) );
				$g = hexdec( substr( $hex, 1, 1 ).substr( $hex, 1, 1 ) );
				$b = hexdec( substr( $hex, 2, 1 ).substr( $hex, 2, 1 ) );
			} else {
				$r = hexdec( substr( $hex, 0, 2 ) );
				$g = hexdec( substr( $hex, 2, 2 ) );
				$b = hexdec( substr( $hex, 4, 2 ) );
			}
			$rgb = array( "r" => $r, "g" => $g, "b" => $b );

			return $rgb; // returns an array with the rgb values
		}

		public function get_attachment_id_from_src( $image_src ) {
			global $wpdb;

			$uploads_directory = wp_upload_dir();
			$image_src = preg_replace( "/-[0-9]+x[0-9]+./i", '.', $image_src );
			$image_src = str_replace( $uploads_directory['baseurl'] . "/", "", $image_src );

			$query = "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_value='{$image_src}'";
			$id    = $wpdb->get_var( $query );

			return $id;
		}

		public static function is_wptouch() {
			if ( function_exists( 'bnc_wptouch_is_mobile' ) && bnc_wptouch_is_mobile() ) {
				return true;
			}

			global $wptouch_pro;

			if ( defined( 'WPTOUCH_VERSION' ) || is_object( $wptouch_pro ) ) {

				if ( $wptouch_pro->showing_mobile_theme ) {
					return true;
				}
			}

			return false;
		}

		public static function is_mobilepress() {

			if ( function_exists( 'mopr_get_option' ) && WP_CONTENT_DIR . mopr_get_option( 'mobile_theme_root', 1 ) == get_theme_root() ) {
				return true;
			}

			return false;
		}

		public static function is_operamini() {
			return isset( $_SERVER['HTTP_USER_AGENT'] ) && false !== strpos( $_SERVER['HTTP_USER_AGENT'], 'Opera Mini' );
		}

		public static function is_wpprint() {

			if ( 1 == intval( get_query_var( 'print' ) ) || 1 == intval( get_query_var( 'printpage' ) ) ) {
				return true;
			}

			return false;
		}

		/**
		 * Register ajax actions.
		 *
		 * @return  {void}
		 */
		public function ajax_actions() {
			$result = array();
			$p      = @$_POST;

			$task = @$p['easylazyloader_task'];

			unset( $p['easylazyloader_task'] );

			// check for rights
			if ( !current_user_can( "manage_options" ) || !$task || !check_ajax_referer( plugin_basename( __FILE__ ), 'easylazyloader_save_settings', false ) ) {
				$result = array(
					 'status' => 403,
					'message' => __( 'You are not allowed to change Easy Lazy Loader settings.', 'easy-lazy-loader' ) 
				);
			} else {
				$options = $this->DEFAULT_OPTIONS;
				$message = '';
				$error   = array();

				if ( $task == "settings" ) {
					/* Update settings */
					$options['apply_to_content']         = isset( $p['apply_to_content'] );
					$options['apply_to_text_widgets']    = isset( $p['apply_to_text_widgets'] );
					$options['apply_to_post_thumbnails'] = isset( $p['apply_to_post_thumbnails'] );
					$options['apply_to_gravatars']       = isset( $p['apply_to_gravatars'] );
					$options['lazy_load_images']         = isset( $p['lazy_load_images'] );
					$options['lazy_load_iframes']        = isset( $p['lazy_load_iframes'] );
					$options['lazy_load_videos']         = isset( $p['lazy_load_videos'] );
					$options['lazy_load_audios']         = isset( $p['lazy_load_audios'] );
					$options['disable_on_wptouch']       = isset( $p['disable_on_wptouch'] );
					$options['disable_on_mobilepress']   = isset( $p['disable_on_mobilepress'] );
					$options['debug_mode']               = isset( $p['debug_mode'] );
					$options['delete_data']              = isset( $p['delete_data'] );

					/* Set value from "Skip Classes" option */
					if( isset( $p['skip_classes'] ) )
						$options['skip_classes'] = sanitize_text_field( wp_unslash( $p['skip_classes'] ) );

					/* Set value from "Placeholder Type" option */
					$options['placeholder_type'] = $p['placeholder_type'];

					/* Set value from "Placeholder URl" option */
					if( isset( $p['placeholder_url'] ) )
						$options['placeholder_url'] = sanitize_text_field( $p['placeholder_url'] );

					/* Set value from "Placeholder Image Size" option */
					$options['placeholder_image_size'] = $p['placeholder_image_size'];

					/* Check value from "Default Images Placeholder Color" option */
					if ( !preg_match( '/^#[a-f0-9]{6}$/i', $p['default_image_placeholder_color'] ) ) {
						$error[] = "<li>" . sprintf( __( "Please enter a valid color in the '%s' field.", 'easy-lazy-loader' ), __( "Default Images Placeholder Color", 'easy-lazy-loader' ) ) . "</li>";
					} else {
						$options['default_image_placeholder_color'] = $p['default_image_placeholder_color'];
					}

					/* Check value from "Default iframes Placeholder Color" option */
					if ( !preg_match( '/^#[a-f0-9]{6}$/i', $p['default_iframe_placeholder_color'] ) ) {
						$error[] = "<li>" . sprintf( __( "Please enter a valid color in the '%s' field.", 'easy-lazy-loader' ), __( "Default iframes Placeholder Color", 'easy-lazy-loader' ) ) . "</li>";
					} else {
						$options['default_iframe_placeholder_color'] = $p['default_iframe_placeholder_color'];
					}

					/* Check value from "Default Videos Placeholder Color" option */
					if ( !preg_match( '/^#[a-f0-9]{6}$/i', $p['default_video_placeholder_color'] ) ) {
						$error[] = "<li>" . sprintf( __( "Please enter a valid color in the '%s' field.", 'easy-lazy-loader' ), __( "Default Videos Placeholder Color", 'easy-lazy-loader' ) ) . "</li>";
					} else {
						$options['default_video_placeholder_color'] = $p['default_video_placeholder_color'];
					}

					/* Check value from "Default Audios Placeholder Color" option */
					if ( !preg_match( '/^#[a-f0-9]{6}$/i', $p['default_audio_placeholder_color'] ) ) {
						$error[] = "<li>" . sprintf( __( "Please enter a valid color in the '%s' field.", 'easy-lazy-loader' ), __( "Default Audios Placeholder Color", 'easy-lazy-loader' ) ) . "</li>";
					} else {
						$options['default_audio_placeholder_color'] = $p['default_audio_placeholder_color'];
					}

					/* Check value from "Threshold" option */
					if ( !is_numeric( $p['threshold'] ) ) {
						$error[] = "<li>" . sprintf( __( "Please enter a valid value in the '%s' field.", 'easy-lazy-loader' ), __( "Threshold", 'easy-lazy-loader' ) ) . "</li>";
					} else {
						$options['threshold'] = floatval( $p['threshold'] );
					}

					/* Update settings in the database */
					if ( empty( $error ) ) {
						update_option( "{$this->SLUG}_options", $options );
						$message = __( "Settings saved.", 'easy-lazy-loader' );
					} else {
						$message = __( "Settings are not saved.", 'easy-lazy-loader' );
					}

					$result = array(
						 'status' => empty( $error ) ? 200 : 403,
						'error' => $error,
						'message' => $message 
					);
				}
				
				else
					$result = array(
						 'status' => 400,
						'message' => __( "Bad Request", 'easy-lazy-loader' ) 
					);
			}
			
			wp_die( json_encode( $result ) );
		}
	}

	// Run EasyLazyLoader
	$EasyLazyLoader = new EasyLazyLoader( __FILE__ );

	//Load Translation files
	if( !function_exists( 'EasyLazyLoader_i18n' )) {
		add_action( 'plugins_loaded', 'EasyLazyLoader_i18n' );

		function EasyLazyLoader_i18n() {
			$path = path_join( dirname( plugin_basename( __FILE__ ) ), 'languages/' );
			load_plugin_textdomain( 'easy-lazy-loader', false, $path );
		}
	}

}
