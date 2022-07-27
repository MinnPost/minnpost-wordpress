<?php
/**
 * Central handler of auto-inserting snippets.
 * Loads the different types and processes them.
 *
 * @package WPCode
 */

/**
 * Class WPCode_Auto_Insert.
 */
class WPCode_Auto_Insert {

	/**
	 * The auto-insert types.
	 *
	 * @var array
	 */
	public $types = array();

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->hooks();
	}

	/**
	 * Add hooks.
	 *
	 * @return void
	 */
	private function hooks() {
		add_action( 'plugins_loaded', array( $this, 'load_types' ), 1 );
	}

	/**
	 * Load and initialize the different types of auto-insert types.
	 *
	 * @return void
	 */
	public function load_types() {
		require_once WPCODE_PLUGIN_PATH . 'includes/auto-insert/class-wpcode-auto-insert-type.php';
		require_once WPCODE_PLUGIN_PATH . 'includes/auto-insert/class-wpcode-auto-insert-everywhere.php';
		require_once WPCODE_PLUGIN_PATH . 'includes/auto-insert/class-wpcode-auto-insert-site-wide.php';
		require_once WPCODE_PLUGIN_PATH . 'includes/auto-insert/class-wpcode-auto-insert-single.php';
		require_once WPCODE_PLUGIN_PATH . 'includes/auto-insert/class-wpcode-auto-insert-archive.php';

		$this->types[] = new WPCode_Auto_Insert_Everywhere();
		$this->types[] = new WPCode_Auto_Insert_Site_Wide();
		$this->types[] = new WPCode_Auto_Insert_Single();
		$this->types[] = new WPCode_Auto_Insert_Archive();
	}

	/**
	 * Get the types of auto-insert options.
	 *
	 * @return WPCode_Auto_Insert_Type[]
	 */
	public function get_types() {
		return $this->types;
	}

	/**
	 * Get a location label from the class not the term.
	 *
	 * @param string $location The location slug/name.
	 *
	 * @return string
	 */
	public function get_location_label( $location ) {
		foreach ( $this->types as $type ) {
			/**
			 * Added for convenience.
			 *
			 * @var WPCode_Auto_Insert_Type $type
			 */
			if ( isset( $type->locations[ $location ] ) ) {
				return $type->locations[ $location ];
			}
		}

		return '';
	}
}
