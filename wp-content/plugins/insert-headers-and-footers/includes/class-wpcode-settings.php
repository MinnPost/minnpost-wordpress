<?php
/**
 * Handles all the WPCode settings.
 *
 * @package WPCode
 */

/**
 * Class WPCode_Settings.
 */
class WPCode_Settings {

	/**
	 * The key used for storing settings in the db.
	 *
	 * @var string
	 */
	private $settings_key = 'wpcode_settings';

	/**
	 * Options as they are loaded from the db.
	 *
	 * @var array
	 * @see WPCode_Settings::get_options
	 */
	private $options;

	/**
	 * Get an option by name with an optional default value.
	 *
	 * @param string $option The option name.
	 * @param mixed  $default The default value (optional).
	 *
	 * @return mixed
	 * @see get_option
	 */
	public function get_option( $option, $default = false ) {
		$options = $this->get_options();
		if ( isset( $options[ $option ] ) ) {
			return $options[ $option ];
		}

		return $default;
	}

	/**
	 * Get all the options as they are stored in the db.
	 *
	 * @return array
	 */
	public function get_options() {
		if ( ! isset( $this->options ) ) {
			$this->options = get_option( $this->settings_key, array() );
		}

		return $this->options;
	}

	/**
	 * Update an option in the settings object.
	 *
	 * @param string $option The option name.
	 * @param mixed  $value The new value.
	 *
	 * @return void
	 */
	public function update_option( $option, $value ) {
		if ( empty( $value ) ) {
			$this->delete_option( $option );

			return;
		}
		if ( isset( $this->options[ $option ] ) && $this->options[ $option ] === $value ) {
			return;
		}
		$this->options[ $option ] = $value;

		$this->save_options();
	}

	/**
	 * Delete an option by its name.
	 *
	 * @param string $option The option name.
	 *
	 * @return void
	 */
	public function delete_option( $option ) {
		// If there's nothing to delete, do nothing.
		if ( isset( $this->options[ $option ] ) ) {
			unset( $this->options[ $option ] );
			$this->save_options();
		}
	}

	/**
	 * Save the current options object to the db.
	 *
	 * @return void
	 */
	private function save_options() {
		update_option( $this->settings_key, (array) $this->options );
	}

	/**
	 * Use an array to update multiple settings at once.
	 *
	 * @param array $options The new options array.
	 *
	 * @return void
	 */
	public function bulk_update_options( $options ) {
		$this->options = array_merge( $this->get_options(), $options );

		$this->save_options();
	}
}
