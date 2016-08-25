<?php
/*
Plugin Name: MinnPost Salesforce
Plugin URI: 
Description: 
Version: 0.0.1
Author: Jonathan Stegall
Author URI: http://code.minnpost.com
License: GPL2+
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: minnpost-salesforce
*/

// Start up the plugin
class Minnpost_Salesforce {

	/**
	* @var string
	*/
	private $version;

	public $salesforce;

	/**
	 * This is our constructor
	 *
	 * @return void
	 */
	public function __construct() {
		$this->version = '0.0.1';
		add_action( 'admin_init', array( &$this, 'salesforce' ) );
		$this->init();
	}

	public function salesforce() {
		//get the base class
		if ( ! function_exists( 'is_plugin_active' ) ) {
     		require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
     	}
		if ( is_plugin_active('salesforce-rest-api/salesforce-rest-api.php') ) {
			require_once plugin_dir_path( __FILE__ ) . '../salesforce-rest-api/salesforce-rest-api.php';
			$salesforce = Salesforce_Rest_API::get_instance();
			$this->salesforce = $salesforce;
			return $this->salesforce;
		}
	}

	/**
	* start
	*
	* @throws \Exception
	*/
    private function init() {
    	add_filter( 'salesforce_rest_api_find_object_match', array( &$this, 'find_object_match' ), 10, 2 );
    	add_filter( 'salesforce_rest_api_push_object_allowed', array( &$this, 'push_not_allowed' ), 10, 5 );
    }

    public function push_not_allowed( $push_allowed, $object_type, $object, $sf_sync_trigger, $mapping ) {
    	if ( $object_type === 'user' && $object['ID'] === 1 ) { // do not add user 1 to salesforce
			return FALSE;
		}
    }

    /**
	* Find an object match between a WordPress object and a Salesforce object
	* This is designed to find out if there is already a map based on the available WordPress data
	*
	* @param string $salesforce_id
	*	Unique identifier for the Salesforce object
	* @param array $wordpress_object
	*	Array of the wordpress object's data
	*
	* @return array $salesforce_id
	*	Unique identifier for the Salesforce object
	*
	*/
	public function find_object_match( $salesforce_id, $wordpress_object ) {

		if ( is_object( $this->salesforce ) ) {
			$salesforce_api = $this->salesforce->salesforce['sfapi'];
		} else {
			$salesforce = $this->salesforce();
			$salesforce_api = $salesforce->salesforce['sfapi'];
		}
		
		if ( is_object( $salesforce_api ) ) {

			// we want to see if the user's email address exists as a primary on any contact and use that contact if so
			$mail = $wordpress_object['user_email'];
			$query = "SELECT Primary_Contact__c FROM Email__c WHERE Email_Address__c = '$mail'";
			$result = $salesforce_api->query( $query );

			if ( $result['data']['totalSize'] === 1 ) {
				$salesforce_id = $result['data']['records'][0]['Primary_Contact__c'];
			} else if ( $result['data']['totalSize'] > 1 ) {
				error_log('Salesforce has ' . $result['data']['totalSize'] . ' matches for this email. Try to log all of them: ' . print_r($result['data']['records'], true));
			}
		} else {
			error_log('object for sf api does not exist');
		}

		return $salesforce_id;
	}


/// end class
}
// Instantiate our class
$Minnpost_Salesforce = new Minnpost_Salesforce();