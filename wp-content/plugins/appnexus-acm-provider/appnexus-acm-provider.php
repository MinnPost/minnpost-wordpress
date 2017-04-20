<?php
/*
Plugin Name: Appnexus ACM Provider
Plugin URI: 
Description: 
Version: 0.0.1
Author: Jonathan Stegall
Author URI: http://code.minnpost.com
License: GPL2+
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: appnexus_acm_provider
*/

class Appnexus_Async_ACM_Provider extends ACM_Provider {
	//public $crawler_user_agent = 'Mediapartners-Google';

	public function __construct() {

		// Default ad zones for DFP Async
		$this->ad_tag_ids = array(
			array(
				'tag'       => 'Top',
				'url_vars'  => array(
					'tag'       => 'Top',
				),
				'enable_ui_mapping' => true,
			),
			array(
				'tag'       => 'Right1',
				'url_vars'  => array(
					'tag'       => 'Right1',
				),
				'enable_ui_mapping' => true,
			),
			array(
				'tag'       => 'x01',
				'url_vars'  => array(
					'tag'       => 'x01',
				),
				'enable_ui_mapping' => true,
			),
			array(
				'tag'       => 'x02',
				'url_vars'  => array(
					'tag'       => 'x02',
				),
				'enable_ui_mapping' => true,
			),
			array(
				'tag'       => 'x03',
				'url_vars'  => array(
					'tag'       => 'x03',
				),
				'enable_ui_mapping' => true,
			),
			array(
				'tag'       => 'x04',
				'url_vars'  => array(
					'tag'       => 'x04',
				),
				'enable_ui_mapping' => true,
			),
			array(
				'tag'       => 'x05',
				'url_vars'  => array(
					'tag'       => 'x05',
				),
				'enable_ui_mapping' => true,
			),
			array(
				'tag'       => 'x06',
				'url_vars'  => array(
					'tag'       => 'x06',
				),
				'enable_ui_mapping' => true,
			),
			array(
				'tag'       => 'x07',
				'url_vars'  => array(
					'tag'       => 'x07',
				),
				'enable_ui_mapping' => true,
			),
			array(
				'tag'       => 'x08',
				'url_vars'  => array(
					'tag'       => 'x08',
				),
				'enable_ui_mapping' => true,
			),
			array(
				'tag'       => 'x09',
				'url_vars'  => array(
					'tag'       => 'x09',
				),
				'enable_ui_mapping' => true,
			),
			array(
				'tag'       => 'x10',
				'url_vars'  => array(
					'tag'       => 'x10',
				),
				'enable_ui_mapping' => true,
			),
			array(
				'tag'       => 'Middle',
				'url_vars'  => array(
					'tag'       => 'Middle',
				),
				'enable_ui_mapping' => true,
			),
			array(
				'tag'       => 'Middle3',
				'url_vars'  => array(
					'tag'       => 'Middle3',
				),
				'enable_ui_mapping' => true,
			),
			array(
				'tag'       => 'BottomRight',
				'url_vars'  => array(
					'tag'       => 'BottomRight',
				),
				'enable_ui_mapping' => true,
			),
			array(
				'tag'       => 'TopRight',
				'url_vars'  => array(
					'tag'       => 'TopRight',
				),
				'enable_ui_mapping' => true,
			),
			array(
				'tag'       => 'TopLeft',
				'url_vars'  => array(
					'tag'       => 'TopLeft',
				),
				'enable_ui_mapping' => true,
			),

			// An extra, special tag to make sure the <head> gets the output we need it to
			array(
				'tag'           => 'appnexus_head',
				'url_vars'      => array(),
			),

		);

		// Default fields for AppNexus
		$this->ad_code_args = array(
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

		add_filter( 'acm_ad_code_args', array( $this, 'filter_ad_code_args' ) );
		add_filter( 'acm_output_html', array( $this, 'filter_output_html' ), 10, 2 );

		add_filter( 'acm_display_ad_codes_without_conditionals', '__return_true' );

		add_action( 'wp_head', array( $this, 'action_wp_head' ) );

		parent::__construct();
	}

	/**
	 * Register the 'tag's available for mapping in the UI
	 */
	public function filter_ad_code_args( $ad_code_args ) {
		global $ad_code_manager;

		foreach ( $ad_code_args as $tag => $ad_code_arg ) {

			if ( 'tag' != $ad_code_arg['key'] )
				continue;

			// Get all of the tags that are registered, and provide them as options
			foreach ( (array)$ad_code_manager->ad_tag_ids as $ad_tag ) {
				if ( isset( $ad_tag['enable_ui_mapping'] ) && $ad_tag['enable_ui_mapping'] )
					$ad_code_args[$tag]['options'][$ad_tag['tag']] = $ad_tag['tag'];
			}

		}
		return $ad_code_args;
	}


	/**
	 * Filter the output HTML to automagically produce the <script> we need
	 */
	public function filter_output_html( $output_html, $tag_id ) {
		global $ad_code_manager;

		$ad_tags = $ad_code_manager->ad_tag_ids;
		$output_script = '';
		switch ( $tag_id ) {
			case 'appnexus_head':
				$tags = array();
				foreach ( (array) $ad_tags as $tag ) {
					if ( $tag['tag'] !== 'appnexus_head' ) {
						$matching_ad_code = $ad_code_manager->get_matching_ad_code( $tag['tag'] );
						if ( ! empty( $matching_ad_code ) ) {
							array_push( $tags, $tag['tag'] );
						}
					}
				}
				$output_script = "
				<!-- OAS HEADER SETUP begin -->
				<script type=\"text/javascript\">
				  /* <![CDATA[ */
				  // Configuration
				  var OAS_url = 'https://oasc17.247realmedia.com/';
				  var OAS_sitepage = 'MP' + window.location.pathname;
				  var OAS_listpos = '" . implode( ',', $tags ) . "';
				  var OAS_query = '';
				  var OAS_target = '_top';
				  
				  var OAS_rns = (Math.random() + \"\").substring(2, 11);
				  document.write('<scr' + 'ipt type=\"text/javascript\" src=\"' + OAS_url + 'adstream_mjx.ads/' + OAS_sitepage + '/1' + OAS_rns + '@' + OAS_listpos + '?' + OAS_query + '\">' + '<\/script>');
				  
				  function OAS_AD(pos) {
				    if (typeof OAS_RICH != 'undefined') {
				      OAS_RICH(pos);
				    }
				  }
				  /* ]]> */
				</script>  
				<!-- OAS HEADER SETUP end --> 
				";
				break;
			default:
				$matching_ad_code = $ad_code_manager->get_matching_ad_code( $tag_id );
				if ( ! empty( $matching_ad_code ) ) {
					$output_script = '
						<div class="minnpost-ads-ad minnpost-ads-ad-'. $tag_id . '">
							<script type="text/javascript">OAS_AD("'. $tag_id .'");</script>
						</div>
					';
				}
		}

		return $output_script;

	}

	/**
	 * Add the initialization code in the head
	 */
	public function action_wp_head() {
		do_action( 'acm_tag', 'appnexus_head' );
	}

	/**
	 * Allow ad sizes to be defined as arrays or as basic width x height.
	 * The purpose of this is to solve for flex units, where multiple ad
	 * sizes may be required to load in the same ad unit.
	 */
	public function parse_ad_tag_sizes( $url_vars ) {
		if ( empty( $url_vars ) ) 
			return;

		$unit_sizes_output = '';
		if ( ! empty( $url_vars['sizes'] ) ) {
			foreach( $url_vars['sizes'] as $unit_size ) {
				$unit_sizes_output[] = array(
					(int) $unit_size['width'],
					(int) $unit_size['height'],
				);
			}			
		} else { // fallback for old style width x height
			$unit_sizes_output = array(
				(int) $url_vars['width'],
				(int) $url_vars['height'],
			);
		}
		return $unit_sizes_output;
	}

}

class Appnexus_ACM_WP_List_Table extends ACM_WP_List_Table {
	function __construct() {
		parent::__construct( array(
				'singular'=> 'appnexus_acm_wp_list_table', //Singular label
				'plural' => 'appnexus_acm_wp_list_table', //plural label, also this well be one of the table css class
				'ajax' => true
			) );
	}

	/**
	 * @return array The columns that shall be used
	 */
	function filter_columns() {
		return array(
			'cb'             => '<input type="checkbox" />',
			'id'             => __( 'ID', 'ad-code-manager' ),
			'tag'            => __( 'Tag', 'ad-code-manager' ),
			'tag_id'         => __( 'Tag ID', 'ad-code-manager' ),
			'tag_name'       => __( 'Tag Name', 'ad-code-manager' ),
			'priority'       => __( 'Priority', 'ad-code-manager' ),
			'operator'       => __( 'Logical Operator', 'ad-code-manager' ),
			'conditionals'   => __( 'Conditionals', 'ad-code-manager' ),
		);
	}

	/**
	 * This is nuts and bolts of table representation
	 */
	function get_columns() {
		add_filter( 'acm_list_table_columns', array( $this, 'filter_columns' ) );
		return parent::get_columns();
	}

	/**
	 * Output the tag cell in the list table
	 */
	function column_tag( $item ) {
		$output = isset( $item['tag'] ) ? esc_html( $item['tag'] ) : esc_html( $item['url_vars']['tag'] );
		$output .= $this->row_actions_output( $item );
		return $output;
	}


}
