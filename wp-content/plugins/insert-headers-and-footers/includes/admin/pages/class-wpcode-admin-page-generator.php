<?php
/**
 * The admin page for the snippet generator.
 *
 * @package WPCode
 */

/**
 * Class WPCode_Admin_Page_Generator.
 */
class WPCode_Admin_Page_Generator extends WPCode_Admin_Page {

	/**
	 * The page slug.
	 *
	 * @var string
	 */
	public $page_slug = 'wpcode-generator';

	/**
	 * Generator to show, if any.
	 *
	 * @var bool|string
	 */
	public $generator = false;

	/**
	 * Available generators.
	 *
	 * @var WPCode_Generator_Type[]
	 */
	public $generators;

	/**
	 * Set the code type for the editor on this page.
	 *
	 * @var string
	 */
	public $code_type = 'php';

	/**
	 * Set the header title based on what is displayed.
	 *
	 * @var string
	 */
	public $header_title;

	/**
	 * Call this just to set the page title translatable.
	 */
	public function __construct() {
		$this->page_title   = __( 'Generator', 'insert-headers-and-footers' );
		$this->header_title = $this->page_title;
		parent::__construct();
	}

	/**
	 * Page-specific hooks & logic.
	 *
	 * @return void
	 */
	public function page_hooks() {
		$this->generators = wpcode()->generator->get_all_generators();
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		// Let's see if we should display a generator.
		if ( isset( $_GET['generator'] ) ) {
			$generator = sanitize_text_field( wp_unslash( $_GET['generator'] ) );
			if ( array_key_exists( $generator, $this->generators ) ) {
				$this->generator = $generator;
			}
		}
		// phpcs:enable WordPress.Security.NonceVerification.Recommended
		if ( $this->generator ) {
			// Translators: gets replace with the generator name.
			$this->header_title = sprintf( __( '%s Generator', 'insert-headers-and-footers' ), $this->generators[ $this->generator ]->get_title() );
		}
	}

	/**
	 * Output the content of the page.
	 *
	 * @return void
	 */
	public function output_content() {
		if ( $this->generator ) {
			$this->show_generator();
		} else {
			$this->show_generators_list();
		}
	}

	/**
	 * Show the list of generators with categories.
	 *
	 * @return void
	 */
	public function show_generators_list() {
		$categories = wpcode()->generator->get_categories();
		?>
		<div class="wpcode-items-metabox wpcode-metabox">
			<?php $this->get_items_list_sidebar( $categories, __( 'All Generators', 'insert-headers-and-footers' ), __( 'Search Generators' ) ); ?>
			<div class="wpcode-items-list">
				<ul class="wpcode-items-list-category">
					<?php
					foreach ( $this->generators as $generator ) {
						$url = add_query_arg(
							array(
								'page'      => $this->page_slug,
								'generator' => $generator->get_name(),
							),
							admin_url( 'admin.php' )
						);
						$this->get_list_item( $generator->get_name(), $generator->get_title(), $generator->get_description(), $url, __( 'Generate', 'insert-headers-and-footers' ), $generator->get_categories() );
					}
					?>
				</ul>
			</div>
		</div>
		<?php
	}

	/**
	 * Show the generator based on the param.
	 *
	 * @return void
	 */
	public function show_generator() {
		$generator = $this->generators[ $this->generator ];
		$tabs      = $generator->get_tabs();
		?>
		<form id="wpcode_generator_form">
			<div class="wpcode-items-metabox wpcode-metabox">
				<div class="wpcode-items-sidebar">
					<ul class="wpcode-items-categories-list wpcode-items-tabs">
						<?php
						$selected = key( $tabs );
						foreach ( $tabs as $tab_id => $tab ) {
							$class = $tab_id === $selected ? 'wpcode-active' : '';
							?>
							<li>
								<button type="button" class="<?php echo esc_attr( $class ); ?>" data-category="<?php echo esc_attr( $tab_id ); ?>"><?php echo esc_html( $tab['label'] ); ?></button>
							</li>
						<?php } ?>
					</ul>
				</div>
				<div class="wpcode-items-list">
					<?php
					foreach ( $tabs as $tab_id => $tab ) {
						$style = $selected === $tab_id ? '' : 'display:none;';
						?>
						<div class="wpcode-form-tab" data-tab="<?php echo esc_attr( $tab_id ); ?>" style="<?php echo esc_attr( $style ); ?>">
							<?php $generator->render_tab( $tab_id ); ?>
						</div>
					<?php } ?>
					<div class="wpcode-generator-actions">
						<?php wp_nonce_field( 'wpcode_generate', 'nonce', false ); ?>
						<input type="hidden" name="type" value="<?php echo esc_attr( $this->generator ); ?>"/>
						<input type="hidden" name="action" value="wpcode_generate_snippet"/>
						<button type="submit" class="wpcode-button wpcode-button-secondary" id="wpcode-generator-update-code"><?php esc_html_e( 'Update code', 'insert-headers-and-footers' ); ?></button>
					</div>
				</div>
			</div>
		</form>
		<div class="wpcode-generator-preview">
			<div class="wpcode-generator-preview-header">
				<h2><?php esc_html_e( 'Code Preview', 'insert-headers-and-footers' ); ?></h2>
				<button type="button" class="wpcode-button" id="wpcode-generator-use-snippet"><?php esc_html_e( 'Use Snippet', 'insert-headers-and-footers' ); ?></button>
				<button class="wpcode-button wpcode-button-icon wpcode-button-secondary wpcode-copy-target" data-target="#wpcode_generator_code_preview" type="button">
					<span class="wpcode-default-icon"><?php wpcode_icon( 'copy', 16, 16 ); ?></span><span class="wpcode-success-icon"><?php wpcode_icon( 'check', 16, 13 ); ?></span> <?php echo esc_html_x( 'Copy Code', 'Copy to clipboard', 'insert-headers-and-footers' ); ?>
				</button>
			</div>
			<textarea id="wpcode_generator_code_preview"><?php echo $generator->get_snippet_code(); ?></textarea>
		</div>
		<span class="wpcode-loading-spinner" id="wpcode-generator-spinner"></span>
		<script type="text/template" id="wpcode-generator-repeater-row">
			<?php $this->repeater_group_template(); ?>
		</script>
		<?php
	}

	/**
	 * The bottom part of the header.
	 *
	 * @return void
	 */
	public function output_header_bottom() {
		?>
		<div class="wpcode-column">
			<h1><?php echo esc_html( $this->header_title ); ?></h1>
		</div>
		<?php
	}

	/**
	 * The template for the repeater row.
	 *
	 * @return void
	 */
	public function repeater_group_template() {
		?>
		<div class="wpcode-repeater-group">
			<button type="button" class="wpcode-button wpcode-button-secondary wpcode-remove-row"><?php esc_html_e( 'Remove Row', 'insert-headers-and-footers' ); ?></button>
		</div>
		<?php
	}

	/**
	 * Add page-specific scripts.
	 *
	 * @return void
	 */
	public function page_scripts() {
		if ( ! $this->generator ) {
			return;
		}
		$settings = $this->load_code_mirror();

		$settings['codemirror']['readOnly'] = 'nocursor';
		wp_add_inline_script( 'code-editor', sprintf( 'jQuery( function() { window.wpcode_editor = wp.codeEditor.initialize( "wpcode_generator_code_preview", %s ); } );', wp_json_encode( $settings ) ) );

		wp_enqueue_script( 'jquery-ui-autocomplete' );
	}
}
