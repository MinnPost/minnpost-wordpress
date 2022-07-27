<?php
/**
 * Execute universal snippets and return their output.
 * This type handles both HTML and PHP at the same time in the same way
 * you can write both in a .php file.
 *
 * @package wpcode
 */

/**
 * WPCode_Snippet_Execute_Universal class.
 */
class WPCode_Snippet_Execute_Universal extends WPCode_Snippet_Execute_Type {

	/**
	 * The snippet type, Universal for this one.
	 *
	 * @var string
	 */
	public $type = 'universal';

	/**
	 * Grab snippet code and return its output.
	 *
	 * @return string
	 */
	protected function prepare_snippet_output() {

		$code = $this->get_snippet_code();

		// Wrap code with PHP tags, so it gets executed correctly.
		return wpcode()->execute->safe_execute_php( '?>' . $code . '<?php ', $this->snippet );
	}
}
