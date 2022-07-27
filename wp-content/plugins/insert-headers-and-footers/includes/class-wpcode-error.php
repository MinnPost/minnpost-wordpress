<?php
/**
 * This class handles PHP errors, keeping tabs of errors thrown
 * and the messages displayed back to the user.
 *
 * @package wpcode
 */

/**
 * WPCode_Error class.
 */
class WPCode_Error {

	/**
	 * An array of errors already caught.
	 *
	 * @var array
	 */
	private $errors = array();

	/**
	 * The error object caught when running the code.
	 *
	 * @param ParseError|Exception|Error|array $error The caught error.
	 *
	 * @return void
	 */
	public function add_error( $error ) {
		$this->errors[] = $error;
	}

	/**
	 * Check if an error has been recorded.
	 *
	 * @return bool
	 */
	public function has_error() {
		return ! empty( $this->errors );
	}

	/**
	 * Empty the errors record, useful if you want to
	 * make sure the last error was thrown by your code.
	 *
	 * @return void
	 */
	public function clear_errors() {
		$this->errors = array();
	}

	private function store_error() {

	}

	/**
	 * Get the last error message.
	 *
	 * @return string
	 */
	public function get_last_error_message() {
		if ( empty( $this->errors ) ) {
			return '';
		}
		$last_error = end( $this->errors );

		if ( method_exists( $last_error, 'getMessage' ) ) {
			return $last_error->getMessage();
		}

		if ( is_array( $last_error ) && isset( $last_error['message'] ) ) {
			return $last_error['message'];
		}

		return '';
	}
}
