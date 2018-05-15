<?php
/*
Plugin Name: WP Scroll Depth
Plugin URI: http://firebrand.net/
Description: Load the jQuery plugin, "Scroll Depth" by Rob Flaherty. This creates scrolling events for Google Analytics.
Version: 1.4.1
Author: Lon Koenig (Firebrand LLC)
Author URI: http://firebrand.net/
License: MIT
*/

/*  
Copyright (c) 2015-2017 Firebrand LLC

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
*/

// No need to run if on an admin page

//global (yes... make this an object someday...
	$wp_scroll_depth_vals = array(
		'option_fields' => array(
			'minHeight' => array(
				'name' => 'Minimum Scroll Height',
				'note' => 'This lets you disable scroll tracking for documents that don\'t meet a height requirement. "Only track scroll events for documents taller than 2000px." <br />The default setting is 0 (all pages are tracked). <br />This is an integer count in pixels',
				'type' => 'numeric', 
				'default' => '0'),
				
			'elements' => array(
				'name' => 'Elements to track', 
				'note' => 'This lets you record scroll events for specific elements on the page. "Track when the footer is scrolled into view." . <br />
							You may track multiple elements; just separated them by commas. Do not use quotes. <br />Example: <em>#colophon</em>',
				'type' => 'list',
				'default' => ''
			),
			
			'percentage' => array( 
				'name' => 'Percentage',
				'note' => 'If you want to turn off scroll percentage tracking and track only the DOM elements you specify in the elements option, set this to false. <br />The default setting is true.',
				'type' => 'bool', 
				'default'=> 'true',
			),
			
			'userTiming' => array(
				'name' => 'Send Timing Events',
				'note' => 'You can turn off User Timing events by setting this to false. <br />The default setting is true.',
				'type' => 'bool',
				'default' => 'true',
			),
			
			'pixelDepth' => array( 
				'name' => 'Pixel Depth',
				'note' => 'You can turn off Pixel Depth events by setting this to false. <br />The default setting is true.',
				'type' => 'bool', 
				'default' => 'false'
			),
			
			'nonInteraction' => array( 
				'name' => 'nonInteraction',
				'note' => 'By default, Scroll Depth events use nonInteraction=true, which means that scroll events will not impact your bounce rate. Change this option to false if you consider scrolling an activity that negates a bounce. Read more about <a href="http://analytics.blogspot.com/2011/10/non-interaction-events-wait-what.html">non-interactive events</a>.',
				'type' => 'bool', 
				'default' => 'false'
			),
			
			'gtmOverride' => array( 
				'name' => 'gtmOverride',
				'note' => 'By default, if Scroll Depth detects Google Tag Manager it assumes that you&rsquo;re using it for your GA implementation. If you&rsquo;re using GTM but not using it for GA then you can set this option to true and Scroll Depth will ignore GTM.',
				'type' => 'bool', 
				'default' => 'false'
			),
			
			'gaGlobal' => array( 
				'name' => 'gaGlobal',
				'note' => 'Use this option if you&rsquo;re using Universal Analytics and have changed the global object name from the default "ga". Note: Scroll Depth automatically supports the common custom object name, "__gaTracker".',
				'type' => 'string', 
				'default' => ''
			),
			
			'eventHandler' => array( 
				'name' => 'Event Handler',
				'note' => 'If you&rsquo;d like to use Scroll Depth with something other than Google Analytics you can use this option to define a callback.<br />
				<em>Note:</em> If you use this option it will override the default event handler and events will not be sent to Google Analytics.<br />
				Check the <a href="http://scrolldepth.parsnip.io/">docs</a> for more information.<br />
				<em>Example:</em>
				<code style="white-space: pre;">
function(data) {
    console.log(data.eventLabel)
}
</code>

  ',
				'type' => 'textarea', 
				'default' => ''
			),
			
		),
		'pageslug' => 'wp-scrolldepth-admin',
		'menuslug' => 'wp-scrolldepth-admin-m1',
		'admin_group' => 'wp_scroll_depth_group',
		'section_id' => 'wp_scroll_depth_section',
		'version' => '1.4.0'
	);
	
if ( !is_admin() ) {
	function wp_scroll_depth_head(){
	/* -------------------------------
	Adds the code to initialize scrollDepth function to the <head> section.
	
	Inputs: 
		none
		
	Globals: 
		global $wp_scroll_depth_vals - all our global settings, including names of parameters
	
	Outputs:
		no return values
		echoes JavaScript
	*/
	global $wp_scroll_depth_vals;
	echo "<script>\n\tjQuery( document ).ready(function(){\n\t\tjQuery.scrollDepth({\n";
	foreach ($wp_scroll_depth_vals['option_fields'] as $option_name => $option_attributes){
		$declaration = "\t\t\t" . $option_name . ': ';
		switch ($wp_scroll_depth_vals['option_fields'][$option_name]['type']){
			
			case 'list':

				$v = preg_replace(
					'/\,\s*/', // pattern
					"', '", // replacement
					 get_option( $option_name, $wp_scroll_depth_vals['option_fields'][$option_name]['default']), // subject
					-1 // limit
				);
				echo  $declaration . "['" . $v . "']" . ",\n";
			break;
			
			default:
				$v = get_option( $option_name, $wp_scroll_depth_vals['option_fields'][$option_name]['default']);
				if (!empty($v)){
					echo $declaration . $v .  ",\n";
				}	
		}
			
	}
	echo "\t});\n});\n</script>\n";
	}

	add_action("wp_head", "wp_scroll_depth_head");



	function wp_scroll_depth_scripts() {
	/* -------------------------------
	Enqueues the scrollDepth library.
	Requires jQuery.
	
	Inputs: none
	Globals: none
	Outputs: no return values

	*/	
		$script_url = plugins_url( 'js/jquery-scrolldepth/jquery.scrolldepth.min.js', __FILE__ );
		wp_enqueue_script("jquery.scrolldepth", $script_url, array("jquery"));
	}

	add_action("wp_enqueue_scripts", "wp_scroll_depth_scripts");

} else { // only need to load admin if we're looking at admin panel

	require_once(plugin_dir_path( __FILE__ ) . 'admin.php'); // back-end admin panels

}
