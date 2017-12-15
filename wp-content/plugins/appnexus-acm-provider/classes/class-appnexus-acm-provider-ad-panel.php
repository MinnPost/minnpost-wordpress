<?php
/**
 * Class file for the Appnexus_ACM_Provider_Ad_Panel class.
 *
 * @file
 */

if ( ! class_exists( 'Appnexus_ACM_Provider' ) ) {
	die();
}

/**
 * Create default WordPress admin functionality to configure the plugin.
 */
class Appnexus_ACM_Provider_Ad_Panel {

	protected $option_prefix;
	protected $slug;
	protected $version;
	protected $ad_code_manager;

	/**
	* Constructor which sets up admin pages
	*
	* @param string $option_prefix
	* @param string $slug
	* @param string $version
	* @param object $ad_code_manager
	* @throws \Exception
	*/
	public function __construct( $option_prefix, $version, $slug, $ad_code_manager ) {

		$this->option_prefix = $option_prefix;
		$this->version = $version;
		$this->slug = $slug;
		$this->ad_code_manager = $ad_code_manager;

		// Default ad zones for Appnexus
		$this->ad_tag_ids = $this->ad_tag_ids();

		// Default fields for AppNexus
		$this->ad_code_args = $this->ad_code_args();

	}

	/**
	 * Register the tags available for mapping in the UI
	 */
	public function filter_ad_code_args( $ad_code_args ) {
		$ad_code_manager = $this->ad_code_manager;

		foreach ( $ad_code_args as $tag => $ad_code_arg ) {

			if ( 'tag' !== $ad_code_arg['key'] ) {
				continue;
			}

			// Get all of the tags that are registered, and provide them as options
			foreach ( (array) $ad_code_manager->ad_tag_ids as $ad_tag ) {
				if ( isset( $ad_tag['enable_ui_mapping'] ) && $ad_tag['enable_ui_mapping'] ) {
					$ad_code_args[ $tag ]['options'][ $ad_tag['tag'] ] = $ad_tag['tag'];
				}
			}
		}
		return $ad_code_args;
	}

	/**
	 * Register the tag ids based on the admin settings
	 */
	public function ad_tag_ids() {
		$tag_list = explode( ', ', get_option( $this->option_prefix . 'tag_list', '' ) );

		$ad_tag_ids = array();
		foreach ( $tag_list as $tag ) {
			$ad_tag_ids[] = array(
				'tag'       => $tag,
				'url_vars'  => array(
					'tag'       => $tag,
				),
				'enable_ui_mapping' => true,
			);
		}

		$tag_type = get_option( $this->option_prefix . 'ad_tag_type', '' );
		if ( 'mjx' === $tag_type ) {
			$ad_tag_ids[] = array(
				'tag'           => 'appnexus_head',
				'url_vars'      => array(),
			);
		}

		return $ad_tag_ids;
	}

	/**
	 * Register the tag arguments
	 */
	public function ad_code_args() {
		$ad_code_args = array(
			array(
				'key'       => 'tag',
				'label'     => __( 'Tag', 'ad-code-manager' ),
				'editable'  => true,
				'required'  => true,
				'type'      => 'select',
				'options'   => array(
					// This is added later, through 'acm_ad_code_args' filter
				),
			),
			array(
				'key'       => 'tag_id',
				'label'     => __( 'Tag ID', 'ad-code-manager' ),
				'editable'  => true,
				'required'  => true,
			),
			array(
				'key'       => 'tag_name',
				'label'     => __( 'Tag Name', 'ad-code-manager' ),
				'editable'  => true,
				'required'  => true,
			),
		);
		return $ad_code_args;
	}

}
