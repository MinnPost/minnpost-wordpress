<?php
// admin page:
// ======================================================
if ( is_admin() ){ // admin actions
	// http://codex.wordpress.org/Adding_Administration_Menus
	add_action('admin_init', 'wp_scroll_depth_options_init' );
	add_action( 'admin_menu', 'wp_scroll_depth_admin_menu' );
}
function wp_scroll_depth_options_init(){
/*
	set up our globals
*/
	global $wp_scroll_depth_vals;

     add_settings_section( // section must always come first
        $wp_scroll_depth_vals['section_id'], // id (string) (required) String for use in the 'id' attribute of tags. 
        'scrollDepth() parameters', // title (string) (required) Title of the section. 
        'wp_scroll_depth_section_heading', // callback  (string) (required) Function that fills the section with the desired content. The function should echo its output. 
        $wp_scroll_depth_vals['pageslug'] // page
        );


    // Register the fields:

    foreach ($wp_scroll_depth_vals['option_fields'] as $id=>$field_options){
                
        add_settings_field( 
            $id, // id
            $field_options['name'], // title
            'wp_scroll_depth_field_callback', // callback (display)
            $wp_scroll_depth_vals['pageslug'] , // page (must match add_theme_page() or add_options_page in our case)
            $wp_scroll_depth_vals['section_id'], // section
            array(
            	'id' => $id,
            	'name' => $field_options['name'],
            	'note' => $field_options['note'],
            	'type' => $field_options['type'],
            	'default' => $field_options['default']
            ) // args 
        );
        //register_setting('plugnamepadm', $id, 'plugname_setting_sanitize');
        register_setting(
        	$wp_scroll_depth_vals['admin_group'], //option_group
        	$id, //option_name 
        	'wp_scroll_depth_setting_sanitize' //sanitize callback
        );
    }

}

function wp_scroll_depth_section_heading($args){

// section header stuff
//	echo 'section heading.';
/*
  echo "<p>id: $args[id]</p>\n";             // id: eg_setting_section
  echo "<p>title: $args[title]</p>\n";       // title: Example settings section in reading
  echo "<p>callback: $args[callback]</p>\n"; // callback: eg_setting_section_callback_function
  */
}

function wp_scroll_depth_setting_sanitize($input){
	global $wp_scroll_depth_vals;
	//$input['text_string'] =  wp_filter_nohtml_kses($input['text_string']);	

	return $input; // return validated input
}

function wp_scroll_depth_field_callback($args){
	global $wp_scroll_depth_vals;
	$textwidth = 40;
	$option_value = get_option($args['id'], $args['default']);
	if ( 'numeric' == $args['type'] ){$textwidth = 6;}
    switch ( $args['type'] ) {
		case 'bool':
			echo '<select id="' . $args['id'] . '" name="' . $args['id'] . '">';
			if ( 'true' == $option_value ){
				echo '<option value="true" selected>true</option>';
				echo '<option value="false">false</option>';
			} else {
				echo '<option value="true">true</option>';
				echo '<option value="false" selected>false</option>';
			}
			echo '</select>';
	
		break;
	
		case 'textarea':
		    echo '<textarea id="' . $args['id'] . '" '
			.'name="' . $args['id'] . '" '
			.'cols="' . $textwidth . '" rows="6" '
			.'>' . $option_value .  '</textarea>';
			
		break;
		
		default:
		    echo '<input id="' . $args['id'] . '" '
			.'name="' . $args['id'] . '" '
			.'size="' . $textwidth . '" type="text" '
			.'value="' . $option_value . '" ' // $this_option
			.' />';
    }
    
	echo "<br />";
	echo '<p>' . $args['note'] . '</p>';
}



function wp_scroll_depth_admin_menu(){
	global $wp_scroll_depth_vals;

	add_options_page(
		'Scroll Depth Options', // page_title
		'ScrollDepth', // menu_title
		'manage_options', // capability
		$wp_scroll_depth_vals['menuslug'] , // menu_slug
		'wp_scroll_depth_options_display' // function
	);
}

function wp_scroll_depth_options_display() {
	global $wp_scroll_depth_vals;
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	echo '<div><h2>' .'WP - Scroll Depth Options' . '</h2>';
	echo '<div class="wrap">';
	echo '<p>The WordPress Scroll Depth plugin uses the <a href="http://scrolldepth.parsnip.io/">jQuery Scroll Depth plugin</a> by Rob Flaherty</p>';
	echo '<p>You can pass parementers to jQuery.scrollDepth() by selecting options below:</p>';
	echo '</div>';

	echo '<form name="wp_scroll_depthadmin" method="post" action="options.php">';

	settings_fields($wp_scroll_depth_vals['admin_group'] );
	do_settings_sections($wp_scroll_depth_vals['pageslug'] );
	echo "<br />\n";
	submit_button(__('Save Changes'));
	echo '</form>';
	echo '</div>';

	
}