<?php
/**
 * Copyright (c) 2015 Khang Minh <contact@betterwp.net>
 * @license http://www.gnu.org/licenses/gpl.html GNU GENERAL PUBLIC LICENSE VERSION 3.0 OR LATER
 */

class BWP_Option_Page_V3
{
	/**
	 * The form
	 */
	protected $form;

	/**
	 * The form name
	 */
	protected $form_name;

	/**
	 * Tabs to build
	 */
	protected $form_tabs;

	/**
	 * Current tab
	 */
	protected $current_tab;

	/**
	 * This holds the form items, determining the position
	 */
	protected $form_items = array();

	/**
	 * This holds the name for each items (an item can have more than one fields)
	 */
	protected $form_item_names = array();

	/**
	 * This holds the form label
	 */
	protected $form_item_labels = array();

	/**
	 * This holds the form option aka data
	 */
	protected $form_options = array();
	protected $site_options = array();

	/**
	 * Actions associated with this form, in addition to the default submit
	 * action
	 *
	 * @var array
	 * @since rev 144
	 */
	protected $form_actions = array();

	/**
	 * The plugin that initializes this option page instance
	 *
	 * @var BWP_Framework_V3
	 */
	protected $plugin;

	/**
	 * @var BWP_WP_Bridge
	 */
	protected $bridge;

	/**
	 * Text domain
	 */
	protected $domain;

	/**
	 * Constructor
	 *
	 * @param string $form_name
	 * @param BWP_Framework_V3 $plugin
	 */
	public function __construct($form_name, BWP_Framework_V3 $plugin)
	{
		$this->form_name    = $form_name;
		$this->form_tabs    = $plugin->form_tabs;
		$this->site_options = $plugin->site_options;
		$this->domain       = $plugin->domain;

		$this->plugin = $plugin;
		$this->bridge = $plugin->get_bridge();

		if (sizeof($this->form_tabs) == 0)
			$this->form_tabs = array($this->bridge->t('Plugin Configurations', $this->domain));
	}

	/**
	 * Init the class
	 *
	 * @param array $form             form data to build the form for the current option page
	 * @param array $form_option_keys contains all option keys that should be handled by the current option page form
	 * @return $this
	 */
	public function init($form = array(), $form_option_keys = array())
	{
		$this->form             = $form;
		$this->form_items       = isset($form['items']) ? $form['items'] : array();
		$this->form_item_names  = isset($form['item_names']) ? $form['item_names'] : array();
		$this->form_item_labels = isset($form['item_labels']) ? $form['item_labels'] : array();

		// we only support option keys and not key and value pairs
		if (array_values($form_option_keys) !== $form_option_keys)
			throw new LogicException('$form_option_keys must contain keys only and no values');

		$this->form_options = $form_option_keys ? $this->plugin->get_options_by_keys($form_option_keys) : array();

		$this->form['formats'] = isset($this->form['formats'])
			? $this->form['formats']
			: array();

		return $this;
	}

	/**
	 * Set options to be used for the currently active form
	 *
	 * This allows setting arbitrary options that might not be associated with
	 * an option key.
	 *
	 * @param array $options
	 */
	public function set_form_options(array $options)
	{
		$this->form_options = $options;
	}

	/**
	 * Add a container for a specific field of the currently active form
	 *
	 * @param string $name name of the field
	 * @param string $container_data data of the container
	 * @param bool $only_if_exist only add container if $name should have it, default to true
	 */
	public function add_form_container($name, $container_data, $only_if_exist = true)
	{
		if (!isset($this->form['container']) || !is_array($this->form['container']))
			$this->form['container'] = array();

		if ($only_if_exist && !isset($this->form['container'][$name]))
			return;

		$this->form['container'][$name] = $container_data;
	}

	/**
	 * Add inline contents to a specific field of the currently active form
	 *
	 * @param string $name name of the field
	 * @param string $inline_data inline data to add
	 * @param bool $only_if_exist only add inline data if $name should have
	 *                            inline, default to true
	 */
	public function add_form_inline($name, $inline_data, $only_if_exist = true)
	{
		if (!isset($this->form['inline']) || !is_array($this->form['inline']))
			$this->form['inline'] = array();

		if ($only_if_exist && !isset($this->form['inline'][$name]))
			return;

		$this->form['inline'][$name] = $inline_data;
	}

	public function set_current_tab($current_tab = 0)
	{
		$this->current_tab = $current_tab;
	}

	public function kill_html_fields(&$form, $names)
	{
		$ids   = array();
		$names = (array) $names;

		foreach ($form['item_names'] as $key => $name)
		{
			if (in_array($name, $names))
				$ids[] = $key;
		}

		$in_keys = array(
			'items',
			'item_labels',
			'item_names'
		);

		foreach ($ids as $id)
		{
			foreach ($in_keys as $key)
				unset($form[$key][$id]);
		}
	}

	/**
	 * @param string $name
	 * @since rev 161
	 */
	protected function is_field_checkbox_or_radio($name)
	{
		$types = array('checkbox', 'checkbox_multi', 'radio');
		foreach ($types as $type)
		{
			if (!isset($this->form[$type]) || !is_array($this->form[$type]))
				continue;

			if (!isset($this->form[$type][$name]))
				continue;

			return true;
		}

		return false;
	}

	/**
	 * Generate HTML form
	 */
	public function generate_html_form()
	{
		$return_str  = '<div class="wrap bwp-wrap" style="padding-bottom: 20px;">' . "\n";
		$return_str .= '<h1 style="display: none;">' . $this->plugin->plugin_title . '</h1>' . "\n";
		echo $return_str;

		do_action('bwp_option_action_before_main');

		$return_str = '<div id="bwp-main">' . "\n";
		echo $return_str;

		do_action('bwp_option_action_before_tabs');

		$return_str = '';

		// @since rev 164 always show tabs, even when there's only one
		$count = 0;
		$return_str .= '<h2 class="nav-tab-wrapper bwp-option-page-tabs">' . "\n";
		foreach ($this->form_tabs as $title => $link)
		{
			$count++;

			$active      = $count == $this->current_tab ? ' nav-tab-active' : '';
			$return_str .= '<a class="nav-tab' . $active . '" href="' . $link . '">' . $title . '</a>' . "\n";
		}

		$return_str .= '</h2>' . "\n";

		$return_str .= apply_filters('bwp_option_before_form', '');
		echo $return_str;

		do_action('bwp_option_action_before_form');

		$return_str  = '';
		$return_str .= '<form class="bwp-option-page" name="' . $this->form_name . '" method="post" action="">'  . "\n";

		if (function_exists('wp_nonce_field'))
		{
			echo $return_str;

			wp_nonce_field($this->form_name);

			$return_str = '';
		}

		$return_str .= '<ul class="bwp-fields">' . "\n";

		// generate filled form
		if (isset($this->form_items) && is_array($this->form_items))
		{
			foreach ($this->form_items as $key => $type)
			{
				$name = !empty($this->form_item_names[$key])
					? $this->form_item_names[$key]
					: '';

				// this form item should not be shown
				if ($this->is_form_item_hidden($name)) {
					continue;
				}

				if (!empty($name) && !empty($this->form_item_labels[$key])
				) {
					$return_str .= '<li class="bwp-clear">'
						. $this->generate_html_fields($type, $name)
						. '</li>'
						. "\n";
				}
			}
		}

		$return_str .= '</ul>' . "\n";
		$return_str .= apply_filters('bwp_option_before_submit_button', '');

		echo $return_str;
		do_action('bwp_option_action_before_submit_button');

		$return_str  = '';
		$return_str .= apply_filters('bwp_option_submit_button',
			'<p class="submit"><input type="submit" class="button-primary" name="submit_'
			. $this->form_name . '" value="' . $this->bridge->t('Save Changes') . '" /></p>') . "\n";

		$return_str .= '</form>' . "\n";

		echo $return_str;
		do_action('bwp_option_action_after_form');

		$return_str  = '</div>' . "\n"; // end #bwp-main
		$return_str .= '</div>' . "\n"; // end .wrap

		echo $return_str;
	}

	/**
	 * Register a custom submit action
	 *
	 * @param string $action the POST action
	 * @param mixed $callback the callback to use for this action
	 * @since rev 144
	 */
	public function register_custom_submit_action($action, $callback = null)
	{
		if (!is_null($callback))
		{
			if (!is_callable($callback))
				throw new InvalidArgumentException(sprintf('callback used for action "%s" must be null or callable', $action));

			$this->bridge->add_filter('bwp_option_page_custom_action_' . $action, $callback, 10, 2);
		}

		$this->form_actions[] = $action;
	}

	/**
	 * Register multiple custom submit actions
	 *
	 * @param array $actions the POST actions
	 * @param mixed $callback the callback to use for all the actions, optional
	 * @since rev 155
	 */
	public function register_custom_submit_actions(array $actions, $callback = null)
	{
		foreach ($actions as $action)
			$this->register_custom_submit_action($action, $callback);
	}

	public function submit_html_form()
	{
		// basic security check
		$this->bridge->check_admin_referer($this->form_name);

		$options = $this->form_options;
		$option_formats = $this->form['formats'];

		foreach ($options as $name => &$option)
		{
			// if this form item is hidden, it should not be handled here
			if ($this->is_form_item_hidden($name))
				continue;

			if (isset($_POST[$name]))
			{
				// make sure options are in expected formats, only when option
				// values are not array, if array all values will be sanitized
				// but not formatted, plugin should take care of the formats
				// explicitly
				$option = !is_array($_POST[$name])
					? $this->format_field($name, $_POST[$name])
					: $this->sanitize($_POST[$name]);
			}

			if (!isset($_POST[$name]))
			{
				// unchecked single checkbox
				if (isset($this->form['checkbox'][$name]))
				{
					$option = '';
				}
				elseif (isset($this->form['checkbox_multi'][$name])
					|| isset($this->form['select_multi'][$name])
				) {
					// unchecked/unselected multi-checkboxes and multi-select
					$option = array();
				}
			}
		}

		// allow the current form to save its submitted data using a different
		// form name
		$form_name = $this->bridge->apply_filters('bwp_option_page_submit_form_name', $this->form_name);

		// save $_POST options for later use
		$post_options = $options;

		// allow filtering the options that are going to be updated
		$options = $this->bridge->apply_filters('bwp_option_page_submit_options', $options);

		// always refresh the options for the form, so that form fields will
		// correctly show user-submitted values, use original options from
		// $_POST if $options is not valid after being filtered
		$this->form_options = array_merge($this->form_options, $options ? $options : $post_options);

		// allow plugin to return false or non-array to not update any options at all
		if ($options === false || !is_array($options))
			return false;

		// @since rev 159 update some options only to allow splitting an
		// option key across multiple option pages. This should update site
		// options as well
		$this->plugin->update_some_options($form_name, $options);

		return true;
	}

	/**
	 * Handles all kinds of form actions, including the default submit action
	 *
	 * @since rev 144
	 */
	public function handle_form_actions()
	{
		// handle the default submit action
		if (isset($_POST['submit_' . $this->get_form_name()]))
		{
			// add a notice and allow redirection only when the form is
			// submitted successully
			if ($this->submit_html_form())
			{
				// allow plugin to choose to not redirect
				$redirect = $this->bridge->apply_filters('bwp_option_page_action_submitted', true);

				if ($redirect !== false)
				{
					$this->plugin->add_notice_flash($this->bridge->t('All options have been saved.', $this->domain));
					$this->plugin->safe_redirect();
				}
			}
		}
		else
		{
			foreach ($this->form_actions as $action)
			{
				if (isset($_POST[$action]))
				{
					// basic security check
					$this->bridge->check_admin_referer($this->form_name);

					$redirect = $this->bridge->apply_filters('bwp_option_page_custom_action_' . $action, true, $action);

					if ($redirect !== false)
						$this->plugin->safe_redirect();
				}
			}
		}
	}

	private function sanitize($value)
	{
		if (!is_array($value))
			return trim(stripslashes($value));

		$value = array_map('stripslashes', $value);
		$value = array_map('trim', $value);

		return $value;
	}

	protected function format_field($name, $value)
	{
		$format = isset($this->form['formats'][$name])
			? $this->form['formats'][$name]
			: '';

		$value = $this->sanitize($value);

		if (!empty($format))
		{
			if ('int' == $format)
			{
				// 'int' is understood as not a blank string and greater than 0
				if ('' === $value || 0 > $value)
					return $this->plugin->options_default[$name];

				return (int) $value;
			}
			elseif ('float' == $format)
				return (float) $value;
			elseif ('html' == $format)
				return stripslashes($this->bridge->wp_filter_post_kses($value));
		}
		else
			return strip_tags($value);
	}

	/**
	 * @param string $name
	 * @param array $attributes attributes to merge with
	 * @since rev 161
	 */
	protected function generate_field_help_attributes($name, array &$attributes = array())
	{
		$help_data = isset($this->form['helps'][$name])
			&& is_array($this->form['helps'][$name])
			? $this->form['helps'][$name] : array();

		if (! $help_data)
			return;

		$help_defaults = array(
			'type'      => 'hover',
			'target'    => 'self', // add attributes to the current field
			'title'     => null,
			'content'   => null,
			'placement' => 'auto top',
			'size'      => 'auto'
		);

		$size_map = array(
			'auto'   => null,
			'full'   => 'bwp-popover-full',
			'large'  => 'bwp-popover-lg',
			'medium' => 'bwp-popover-md',
			'small'  => 'bwp-popover-sm'
		);

		$help_data = array_merge($help_defaults, $help_data);
		$help_class = '';
		$help_attributes = array();

		switch ($help_data['type'])
		{
			case 'focus':
			case 'hover':
			case 'switch':
				$help_class = 'bwp-popover-' . $help_data['type'];
				break;
		}

		$help_attributes = array(
			'class'              => $help_class,
			'title'              => $help_data['title'],
			'data-content'       => $help_data['content'],
			'data-placement'     => $help_data['placement'],
			'data-popover-class' => $size_map[$help_data['size']]
		);

		// need to add a new icon to after the field to hold the help attributes
		if ($help_data['target'] == 'icon' || $help_data['type'] == 'link')
		{
			$post_html = $this->generate_html_field('help', array(
				'url'        => $help_data['type'] == 'link' ? $help_data['content'] : false,
				'attributes' => $help_attributes
			), 'help_' . $name);

			$this->form['post'][$name] = !empty($this->form['post'][$name])
				? $this->form['post'][$name] . ' ' . $post_html
				: $post_html;

			return;
		}
		elseif ($help_data['type'] == 'block')
		{
			$post_html = $help_data['content']
				? '<span class="bwp-form-help-block">' . $help_data['content'] . '</span>'
				: '';

			// need to add a block of text after the field to hold the help contents
			$this->form['post'][$name] = !empty($this->form['post'][$name])
				? $this->form['post'][$name] . ' ' . $post_html
				: $post_html;

			return;
		}

		// add attributes to the current field, merging with any existing
		// attributes if found
		foreach ($help_attributes as $attribute_name => $attribute)
		{
			if (! $attribute)
				continue;

			// append help attribute
			$attributes[$attribute_name] = !empty($attributes[$attribute_name])
				? $attributes[$attribute_name] . ' ' . $attribute
				: $attribute;
		}
	}

	/**
	 * @since rev 161
	 */
	protected function generate_attribute_string(array $attributes)
	{
		foreach ($attributes as $attribute_name => &$attribute)
		{
			if (! $attribute)
				continue;

			$attribute = esc_html($attribute_name) . '="' . esc_attr($attribute) . '"';
		}

		$attributes = implode(' ', $attributes);
		$attributes = !empty($attributes) ? $attributes . ' ' : '';

		return $attributes;
	}

	/**
	 * Get attributes set for a field.
	 *
	 * Should always return attributes with a "class" key
	 *
	 * @param string $name
	 * @return array
	 *
	 * @since rev 165
	 */
	protected function get_field_attributes($name)
	{
		$attributes = isset($this->form['attributes'][$name])
			&& is_array($this->form['attributes'][$name])
			? $this->form['attributes'][$name] : array();

		$attributes['class'] = isset($attributes['class']) ? $attributes['class'] : '';

		return $attributes;
	}

	/**
	 * @since rev 161
	 */
	protected function generate_field_attributes($name)
	{
		$attributes = $this->get_field_attributes($name);

		// populate help attributes for fields that are not checkbox/radiobox
		// as their help attributes should be added later on to their labels
		if (! $this->is_field_checkbox_or_radio($name))
			$this->generate_field_help_attributes($name, $attributes);

		return $this->generate_attribute_string($attributes);
	}

	/**
	 * @since rev 163
	 */
	protected function is_compound_field($name)
	{
		if (isset($this->form['inline_fields'][$name])
			&& is_array($this->form['inline_fields'][$name])
		) {
			return true;
		}

		return !empty($this->form['post'][$name]);
	}

	/**
	 * Generate HTML field
	 */
	protected function generate_html_field($type = '', $data = array(), $name = '', $in_section = false)
	{
		$pre_html_field  = '';
		$post_html_field = '';

		$checked  = 'checked="checked" ';
		$selected = 'selected="selected" ';

		$value = isset($this->form_options[$name])
			? $this->form_options[$name]
			: '';

		$value = isset($data['value']) ? $data['value'] : $value;

		if ('checkbox' == $type)
		{
			$value = current(array_values($data));
			$value = $value ? $value : 'yes';
		}

		$value = !empty($this->domain) && ('textarea' == $type || 'input' == $type)
			? $this->bridge->t($value, $this->domain)
			: $value;

		if (is_array($value))
		{
			foreach ($value as &$v)
				$v = is_array($v) ? array_map('esc_attr', $v) : esc_attr($v);
		}
		else
		{
			$value = 'textarea' == $type
				? esc_html($value)
				: esc_attr($value);
		}

		$array_replace = array();
		$array_search  = array(
			'type',
			'text',
			'size',
			'name',
			'value',
			'cols',
			'rows',
			'label',
			'disabled',
			'pre',
			'post',
			'attributes',
			'label_attributes'
		);

		$return_html   = '';

		$attributes = $this->generate_field_attributes($name);
		$label_attributes = '';

		$br = $this->is_compound_field($name) || $type == 'textarea' ? '' : "<br />\n";

		$pre  = !empty($data['pre']) ? $data['pre'] : '';
		$post = !empty($data['post']) ? $data['post'] : '';

		$param = empty($this->form['params'][$name])
			? false : $this->form['params'][$name];

		$name_attr = esc_attr($name);

		switch ($type)
		{
			case 'heading':
			case 'heading4':
				$html_field = '%s';
			break;

			case 'input':
				$data['label'] = !empty($data['label']) ? ' <em>' . $data['label'] . '</em>' : '';
				$html_field = !$in_section
					? '%pre%<input %attributes%%disabled% size="%size%" type="text" '
						. 'id="' . $name_attr . '" '
						. 'name="' . $name_attr . '" '
						. 'value="' . $value . '" />%label%'
					: '<label for="' . $name_attr . '">%pre%<input %attributes%%disabled% size="%size%" type="text" '
						. 'id="' . $name_attr . '" '
						. 'name="' . $name_attr . '" '
						. 'value="' . $value . '" />%label%</label>';

				$post_html_field = '%post%';
			break;

			case 'select':
			case 'select_multi':
				$pre_html_field = 'select_multi' == $type
					? '%pre%<select %attributes%id="' . $name_attr . '" name="' . $name_attr . '[]" multiple>' . "\n"
					: '%pre%<select %attributes%id="' . $name_attr . '" name="' . $name_attr . '">' . "\n";

				$html_field = '<option %selected%value="%value%">%option%</option>';

				$post_html_field = '</select>%post%' . $br;
			break;

			case 'checkbox':
				$html_field = '<label %label_attributes%for="%name%">'
					. '<input %attributes%%checked%type="checkbox" id="%name%" name="%name%" value="yes" /> %label%</label>';

				$post_html_field = '%post%';
			break;

			case 'checkbox_multi':
				$html_field = '<label %label_attributes%for="%name%-%value%">'
					. '<input %attributes%%checked%type="checkbox" id="%name%-%value%" name="%name%[]" value="%value%" /> %label%</label>';

				$post_html_field = '%post%';
			break;

			case 'radio':
				$html_field = '<label %label_attributes%>' . '<input %attributes%%checked%type="radio" '
					. 'name="' . $name_attr . '" value="%value%" /> %label%</label>';

				$post_html_field = '%post%';
			break;

			case 'textarea':
				$html_field = '%pre%<textarea %attributes%%disabled% '
					. 'id="' . $name_attr . '" '
					. 'name="' . $name_attr . '" cols="%cols%" rows="%rows%">'
					. $value . '</textarea>';

				$post_html_field = '%post%';
			break;

			// @since rev 161 add a help field
			case 'help':
				$html_field_class = 'bwp-field-help';
				$html_field_inner = '&nbsp;<span %attributes%>(?)</span>';

				// use nice font icon for WP 3.8+
				if ($this->plugin->get_current_wp_version('3.8'))
				{
					$html_field_class = 'dashicons dashicons-editor-help bwp-field-help';
					$html_field_inner = '&nbsp;<span %attributes%></span>';
				}

				// use explicitly set attributes for this field when needed
				if (empty($data['url']))
				{
					$attributes = isset($data['attributes']) && is_array($data['attributes'])
						? $data['attributes']
						: array('class' => '');

					$attributes['class'] .= ' ' . $html_field_class;
					$attributes = $this->generate_attribute_string($attributes);

					$html_field = $html_field_inner;
				}
				else
				{
					$attributes = $this->generate_attribute_string(array(
						'class' => $html_field_class
					));

					$html_field = '<a class="bwp-field-help-link" target="_blank" '
						. 'title="' . __('View more info in a separate tab', $this->domain) . '" '
						. 'href="' . esc_url($data['url']) . '">'
						. $html_field_inner
						. '</a>';
				}
			break;

			// @since rev 165 add button field
			case 'button':
				$data['type'] = empty($data['type']) ? 'button' : $data['type'];
				$data['text'] = empty($data['text']) ? '' : $data['text'];

				// set default button classes
				$btn_class = !empty($data['is_primary']) ? 'button-primary' : 'button-secondary';
				$attributes = $this->get_field_attributes($name);

				$attributes['class'] = $btn_class . ' ' . $attributes['class'];
				$attributes = $this->generate_attribute_string($attributes);

				$html_field = '%pre%<button %attributes%%disabled% '
					. 'id="' . $name_attr . '" '
					. 'name="' . $name_attr . '" type="%type%">'
					. '%text%'
					. '</button>';

				$post_html_field = '%post%';
				break;
		}

		if (!isset($data))
			return;

		if (strpos($type, 'heading') === 0 && !is_array($data))
		{
			$return_html .= sprintf($html_field, $data);
		}
		elseif ($type == 'radio'
			|| $type == 'checkbox' || $type == 'checkbox_multi'
			|| $type == 'select' || $type == 'select_multi'
		) {
			// generate label attributes for checkbox/radiobox if any
			if (strpos($type, 'select') === false)
			{
				$label_attributes = array();
				$this->generate_field_help_attributes($name, $label_attributes);
				$label_attributes = $this->generate_attribute_string($label_attributes);

				// generating label attributes might add some post HTML, so we
				// need to reassign br here
				$br = $this->is_compound_field($name) ? '' : "<br />\n";
			}

			foreach ($data as $key => $value)
			{
				if ($type == 'checkbox')
				{
					// handle checkbox a little bit differently
					if ($this->form_options[$name] == 'yes')
					{
						$return_html .= str_replace(
							array('%value%', '%name%', '%label%', '%checked%', '%attributes%', '%label_attributes%'),
							array($value, $name_attr, $key, $checked, $attributes, $label_attributes),
							$html_field
						);

						$return_html .= apply_filters('bwp_option_after_' . $type . '_' . $name . '_checked', '', $value, $param);
						$return_html .= $br;
					}
					else
					{
						$return_html .= str_replace(
							array('%value%', '%name%', '%label%', '%checked%', '%attributes%', '%label_attributes%'),
							array($value, $name_attr, $key, '', $attributes, $label_attributes),
							$html_field
						);

						$return_html .= apply_filters('bwp_option_after_' . $type . '_' . $name, '', $value, $param);
						$return_html .= $br;
					}
				}
				elseif ($type == 'checkbox_multi')
				{
					// handle a multi checkbox differently
					if (isset($this->form_options[$name])
						&& is_array($this->form_options[$name])
						&& in_array($value, $this->form_options[$name])
					) {
						$return_html .= str_replace(
							array('%value%', '%name%', '%label%', '%checked%', '%attributes%', '%label_attributes%'),
							array($value, $name_attr, $key, $checked, $attributes, $label_attributes),
							$html_field
						);

						$return_html .= apply_filters('bwp_option_after_' . $type . '_' . $name . '_checked', '', $value, $param);
						$return_html .= $br;
					}
					else
					{
						$return_html .= str_replace(
							array('%value%', '%name%', '%label%', '%checked%', '%attributes%', '%label_attributes%'),
							array($value, $name_attr, $key, '', $attributes, $label_attributes),
							$html_field
						);

						$return_html .= apply_filters('bwp_option_after_' . $type . '_' . $name, '', $value, $param);
						$return_html .= $br;
					}
				}
				elseif (isset($this->form_options[$name])
					&& ($this->form_options[$name] == $value
						|| (is_array($this->form_options[$name])
							&& in_array($value, $this->form_options[$name])))
				) {
					$item_br = $type == 'select' || $type == 'select_multi' ? "\n" : $br;

					$return_html .= str_replace(
						array('%value%', '%name%', '%label%', '%option%', '%checked%', '%selected%', '%pre%', '%post%'),
						array($value, $name_attr, $key, $key, $checked, $selected, $pre, $post),
						$html_field
					) . $item_br;
				}
				else
				{
					$item_br = $type == 'select' || $type == 'select_multi' ? "\n" : $br;

					$return_html .= str_replace(
						array('%value%', '%name%', '%label%', '%option%', '%checked%', '%selected%', '%pre%', '%post%'),
						array($value, $name_attr, $key, $key, '', '', $pre, $post),
						$html_field
					) . $item_br;
				}
			}
		}
		else
		{
			foreach ($array_search as &$keyword)
			{
				$array_replace[$keyword] = '';

				if ($keyword == 'attributes')
					$array_replace[$keyword] = $attributes;
				elseif (!empty($data[$keyword]))
					$array_replace[$keyword] = $data[$keyword];

				$keyword = '%' . $keyword . '%';
			}

			$return_html = str_replace($array_search, $array_replace, $html_field) . $br;
		}

		// inline fields
		$inline_html = '';
		if (isset($this->form['inline_fields'][$name]) && is_array($this->form['inline_fields'][$name]))
		{
			foreach ($this->form['inline_fields'][$name] as $field => $field_type)
			{
				if (isset($this->form[$field_type][$field]))
					$inline_html = ' ' . $this->generate_html_field($field_type, $this->form[$field_type][$field], $field, $in_section);
			}
		}

		// html before field
		$pre = !empty($this->form['pre'][$name])
			? ' ' . $this->form['pre'][$name]
			: $pre;

		// html after field
		$post = !empty($this->form['post'][$name])
			? ' ' . $this->form['post'][$name]
			: $post;

		// support for custom html attributes
		$pre_html_field = str_replace('%attributes%', $attributes, $pre_html_field);

		return str_replace('%pre%', $pre, $pre_html_field) . $return_html . str_replace('%post%', $post, $post_html_field) . $inline_html;
	}

	/**
	 * Generate HTML fields
	 */
	protected function generate_html_fields($type, $name)
	{
		$item_label  = '';
		$return_html = '';

		$item_key = array_keys($this->form_item_names, $name);

		$input_class = strpos($type, 'heading') === 0
			? 'bwp-option-page-heading-desc'
			: 'bwp-option-page-inputs';

		// an inline item can hold any HTML markup, example is to display some
		// kinds of button right be low the label
		$inline = '';

		if (isset($this->form['inline']) && is_array($this->form['inline'])
			&& array_key_exists($name, $this->form['inline'])
		) {
			$inline = empty($this->form['inline'][$name]) ? '' : $this->form['inline'][$name];
		}

		$inline .= "\n";

		switch ($type)
		{
			case 'section':
				if (!isset($this->form[$name]) || !is_array($this->form[$name]))
					return;

				$item_label = $this->form_item_labels[$item_key[0]];

				$item_label_html = '<span class="bwp-opton-page-label">'
					. $item_label
					. $inline
					. '</span>';

				foreach ($this->form[$name] as $section_field)
				{
					$section_item_type = $section_field[0];
					$section_item_name = $section_field['name'];

					if (isset($this->form[$section_item_type]))
					{
						$return_html .= $this->generate_html_field(
							$section_item_type,
							$this->form[$section_item_type][$section_item_name],
							$section_item_name, true
						);
					}
				}
			break;

			default:
				if (!isset($this->form[$type][$name])
					|| (strpos($type, 'heading') !== 0 && !is_array($this->form[$type][$name])))
					return;

				$item_label = $this->form_item_labels[$item_key[0]];

				$item_label_html = $type != 'checkbox' && $type != 'checkbox_multi' && $type != 'radio'
					? '<label class="bwp-opton-page-label type-' . $type . '" for="' . $name . '">'
						. $item_label
						. $inline
						. '</label>'
					: '<span class="bwp-opton-page-label type-' . $type . '">'
						. $item_label
						. $inline
						. '</span>';

				$heading_id = strtolower(str_replace(array(' ', '_'), '-', $item_label));

				if (strpos($type, 'heading') === 0)
				{
					$heading_tag = $type == 'heading4' ? 'h4' : 'h3';
					$item_label_html = '<' . $heading_tag . ' id="' . esc_attr($heading_id) . '">' . $item_label . '</' . $heading_tag . '>' . $inline;
				}

				if (isset($this->form[$type]))
					$return_html = $this->generate_html_field($type, $this->form[$type][$name], $name);
			break;
		}

		// a container can hold some result executed by customized script,
		// such as displaying something when user press the submit button
		$containers = '';

		if (isset($this->form['container'])
			&& is_array($this->form['container'])
			&& array_key_exists($name, $this->form['container'])
		) {
			// @since rev 165 allow setting container settings too
			$container_array = (array) $this->form['container'][$name];

			$container_settings = array(
				'need_wrapper'    => true,
				'wrapper_classes' => array('bwp-clear')
			);

			if (isset($container_array['_settings']) && is_array($container_array['_settings']))
			{
				$container_settings = array_merge(
					$container_settings,
					$container_array['_settings']
				);
			}

			// remove the settings because it's not actual container contents
			unset($container_array['_settings']);

			foreach ($container_array as $container)
			{
				if ($container_settings['need_wrapper'])
				{
					$container_template =  empty($container)
						? '<div style="display: none;"><!-- --></div>'
						: '<div class="'
							. implode(' ', $container_settings['wrapper_classes'])
							. '">'
							. '%s'
							. '</div>'
							. "\n";
				}
				else
				{
					$container_template = '%s';
				}

				$containers .= sprintf($container_template, $container);
			}
		}

		$pure_return = trim(strip_tags($return_html));

		if (empty($pure_return) && strpos($type, 'heading') === 0)
		{
			return $item_label_html . $containers;
		}
		else
		{
			return $item_label_html . '<p class="' . $input_class . '">'
				. $return_html . '</p>'
				. $containers;
		}
	}

	protected function is_form_item_hidden($name)
	{
		if (isset($this->form['env'])
			&& array_key_exists($name, $this->form['env'])
			&& $this->form['env'][$name] == 'multisite'
			&& !BWP_Framework_V3::is_multisite()
		) {
			// hide multisite field if not in multisite environment
			return true;
		}

		if (isset($this->form['php'])
			&& array_key_exists($name, $this->form['php'])
			&& !BWP_Version::get_current_php_version_id($this->form['php'][$name])
		) {
			// hide field if the current PHP version requirement is not satisfied
			return true;
		}

		if (isset($this->form['role'])
			&& array_key_exists($name, $this->form['role'])
			&& $this->form['role'][$name] == 'superadmin'
			&& (!BWP_Framework_V3::is_site_admin() || !BWP_Framework_V3::is_on_main_blog())
		) {
			// hide site-admin-only settings if not a site admin or not on
			// main blog
			return true;
		}

		/* if (isset($this->form['callback']) */
		/* 	&& array_key_exists($name, $this->form['callback']) */
		/* 	&& is_callable($this->form['callback'][$name]) */
		/* 	&& !call_user_func($this->form['callback'][$name], $name) */
		/* ) { */
		/* 	// a condition not satisfied, hide the field */
		/* 	return true; */
		/* } */

		if (in_array($name, $this->site_options)
			&& (!BWP_Framework_V3::is_site_admin() || !BWP_Framework_V3::is_on_main_blog())
		) {
			// hide site-admin-only settings if not a site admin or not on
			// main blog
			return true;
		}

		if (isset($this->form['blog'])
			&& array_key_exists($name, $this->form['blog'])
			&& BWP_Framework_V3::is_multisite()
		) {
			if ($this->form['blog'][$name] == 'main' && !BWP_Framework_V3::is_on_main_blog())
			{
				// this field should be on main blog only
				return true;
			}
			elseif ($this->form['blog'][$name] == 'sub' && BWP_Framework_V3::is_on_main_blog())
			{
				// this field should be on sub blogs only
				return true;
			}
		}

		return false;
	}

	public function get_form_name()
	{
		return $this->form_name;
	}

	public function get_form()
	{
		return $this->form;
	}
}
