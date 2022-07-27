<?php
/**
 * Class to auto-insert snippets site-wide.
 *
 * @package wpcode
 */

/**
 * Class WPCode_Auto_Insert_Single.
 */
class WPCode_Auto_Insert_Site_Wide extends WPCode_Auto_Insert_Type {

	/**
	 * Load the available options and labels.
	 *
	 * @return void
	 */
	public function init() {
		$this->label     = __( 'Site wide', 'insert-headers-and-footers' );
		$this->locations = array(
			'site_wide_header' => __( 'Site Wide Header', 'insert-headers-and-footers' ),
			'site_wide_body'   => __( 'Site Wide Body', 'insert-headers-and-footers' ),
			'site_wide_footer' => __( 'Site Wide Footer', 'insert-headers-and-footers' ),
		);
	}

	/**
	 * Add hooks specific to this type.
	 *
	 * @return void
	 */
	public function hooks() {
		add_action( 'wp_head', array( $this, 'insert_header' ) );
		add_action( 'wp_footer', array( $this, 'insert_footer' ) );
		add_action( 'wp_body_open', array( $this, 'insert_body' ) );
	}

	/**
	 * Insert snippets in the header.
	 *
	 * @return void
	 */
	public function insert_header() {
		$snippets = $this->get_snippets_for_location( 'site_wide_header' );
		foreach ( $snippets as $snippet ) {
			echo wpcode()->execute->get_snippet_output( $snippet );
		}
	}

	/**
	 * Insert snippets in the footer.
	 *
	 * @return void
	 */
	public function insert_footer() {
		$snippets = $this->get_snippets_for_location( 'site_wide_footer' );
		foreach ( $snippets as $snippet ) {
			echo wpcode()->execute->get_snippet_output( $snippet );
		}
	}

	/**
	 * Insert snippets after the opening body tag.
	 *
	 * @return void
	 */
	public function insert_body() {
		$snippets = $this->get_snippets_for_location( 'site_wide_body' );
		foreach ( $snippets as $snippet ) {
			echo wpcode()->execute->get_snippet_output( $snippet );
		}
	}
}
