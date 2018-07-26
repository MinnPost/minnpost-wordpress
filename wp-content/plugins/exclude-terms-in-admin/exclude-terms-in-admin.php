<?php
/*
Plugin Name: Exclude Terms in Admin
Description: Exclude specified terms from the edit and new post screens
Version: 0.0.1
Author: Jonathan Stegall
Author URI: https://code.minnpost.com
Text Domain: exclude-terms-admin
License: GPL2+
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

class Exclude_Terms_Admin {

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
			self::$instance = new Exclude_Terms_Admin();
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
		$this->slug    = 'exclude-terms-admin';

		$this->see_hidden_capability = 'see_hidden_terms';
		$this->hidden_terms          = "'3-minute Egg', '612 Authentic', 'Arts Arena', 'Braublog', 'Business Agenda', 'Christian Science Monitor', 'Cityscape', 'D.C. Dispatches', 'Driving Change', 'Effective Democracy', 'Global Post', 'Learning Curve', 'Listing Slightly', 'Max About Town', 'MedCity News', 'Minnesota Blog Cabin', 'Minnesota History', 'Minnov8', 'MinnPOTUS', 'Next Degree', 'Pollen', 'Rural Dispatches', 'Rural Minnesota', 'Salon', 'Scientific Agenda', 'The Intelligencer', 'The Line', 'Thirty Two Magazine', 'Two Cities', 'Verse or Worse', 'View Finder', 'Weekend Best Bets', 'Young Professionals Network'";

		$this->add_actions();
	}

	public function add_actions() {
		add_filter( 'list_terms_exclusions', array( $this, 'list_terms_exclusions' ), 10, 2 );
	}

	/**
	* Exclude terms from post edit screen
	*
	* @return string $exclusions
	*/
	function list_terms_exclusions( $exclusions, $args ) {
		global $pagenow;
		if ( in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) && ! current_user_can( $this->see_hidden_capability ) ) {
			$exclusions .= " AND t.name NOT IN ( $this->hidden_terms )";
		}
		return $exclusions;
	}
}

// Instantiate our class
$exclude_terms_admin = Exclude_Terms_Admin::get_instance();
