<?php

/*
Plugin Name: AP Style Dates and Times
Plugin URI: http://www.rockmycar.net/ap-style-dates-and-times-plugin/
Description: Formats the date and time of your posts and comments into Associated Press Style.
Version: 2.1
Author: Tom Chambers
Author URI: http://www.rockmycar.net/
*/

/*
Copyright 2009-2010 Tom Chambers

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

// Set the local time zone based on the WordPress settings

$aplocaltime = get_option('gmt_offset');
putenv("TZ=".$aplocaltime);

// Post date formatting

function get_ap_date() {
	$today = get_option('ap_today');
	$captoday = get_option('ap_captoday');
	$useyear = get_option('ap_useyear');

	// Determine the month and set the AP Style abbreviation

	if (get_the_time('m')=='01') :
		$apmonth = 'Jan. ';
	elseif (get_the_time('m')=='02') :
		$apmonth = 'Feb. ';
	elseif (get_the_time('m')=='08') :
		$apmonth = 'Aug. ';
	elseif (get_the_time('m')=='09') :
		$apmonth = 'Sept. ';
	elseif (get_the_time('m')=='10') :
		$apmonth = 'Oct. ';
	elseif (get_the_time('m')=='11') :
		$apmonth = 'Nov. ';
	elseif (get_the_time('m')=='12') :
		$apmonth = 'Dec. ';
	else :
		$apmonth = (get_the_time('F'));
	endif;

	// Determine whether the date is within the current year and set it

	if (get_the_time('Y')!=date('Y')) :
			$apyear = ", ".get_the_time('Y');
	else :
		if ($useyear=="true") :
			$apyear = ", ".get_the_time('Y');
		else :
			$apyear = "";
		endif;
	endif;

	// Determine whether the date is the current date and set the final output

	if (get_the_time('F j Y')==date('F j Y')) :
		if ($today =="true") :
			if ($captoday == "true") :
				$apdate = "Today";
			else :
				$apdate = "today";
			endif;
		else :
			$apdate = $apmonth." ".get_the_time('j')."".$apyear;
		endif;
	else :
		$apdate = $apmonth." ".get_the_time('j')."".$apyear; 
	endif;

	return $apdate;
}

function ap_date() {
	echo get_ap_date();
}

// Post time formatting

function get_ap_time() {
	$capnoon = get_option('ap_capnoon');

	// Format am and pm to AP Style abbreviations

	if (get_the_time('a')=='am') :
		$meridian = 'a.m.';
	elseif (get_the_time('a')=='pm') :
		$meridian = 'p.m.';
	endif;

	// Reformat 12:00 and 00:00 to noon and midnight

	if (get_the_time('H:i')=='00:00') :
		if ($capnoon == "true") :
			$aptime = "Midnight";
		else :
			$aptime = "midnight";
		endif;

	elseif (get_the_time('H:i')=='12:00') :
		if ($capnoon == "true") :
			$aptime = "Noon";
		else :
			$aptime = "noon";
		endif;

	// Eliminate trailing zeroes from times at the top of the hour and set final output

	elseif (get_the_time('i')=='00') :
		$aptime = get_the_time('g')." ".$meridian;
	else :
		$aptime = get_the_time('g:i')." ".$meridian;
	endif;

	return $aptime;
}

function ap_time() {
	echo get_ap_time();
}

// Comment date formatting

function get_ap_comment_date() {
	$today = get_option('ap_today');
	$captoday = get_option('ap_captoday');
	$useyear = get_option('ap_useyear');
	
	// Determine the month and set the AP Style abbreviation

	if (get_comment_time('m')=='01') :
		$apcmonth = 'Jan. ';
	elseif (get_comment_time('m')=='02') :
		$apcmonth = 'Feb. ';
	elseif (get_comment_time('m')=='08') :
		$apcmonth = 'Aug. ';
	elseif (get_comment_time('m')=='09') :
		$apcmonth = 'Sept. ';
	elseif (get_comment_time('m')=='10') :
		$apcmonth = 'Oct. ';
	elseif (get_comment_time('m')=='11') :
		$apcmonth = 'Nov. ';
	elseif (get_comment_time('m')=='12') :
		$apcmonth = 'Dec. ';
	else :
		$apcmonth = (get_comment_time('F'));
	endif;

	// Determine whether the date is within the current year

	if (get_comment_time('Y')!=date('Y')) :
			$apcyear = ", ".get_comment_time('Y');
	else :
		if ($useyear=="true") :
			$apcyear = ", ".get_comment_time('Y');
		else :
			$apcyear = "";
		endif;
	endif;


	// Determine whether the date is the current date and set the final output

	if (get_comment_time('F j Y')==date('F j Y')) :
		if ($today == "true") :
			if ($captoday == "true") :
				$apcdate = "Today";
			else :
				$apcdate = "today";
			endif;
		else :
			$apcdate = $apcmonth." ".get_comment_time('j')."".$apcyear;
		endif;
	else :
		$apcdate = $apcmonth." ".get_comment_time('j')."".$apcyear; 
	endif;

	return $apcdate;
}

function ap_comment_date() {
	echo get_ap_comment_date();
}

// Comment time formatting

function get_ap_comment_time() {
	$capnoon = get_option('ap_capnoon');

	// Format am and pm to AP Style abbreviations

	if (get_comment_time('a')=='am') :
		$cmeridian = 'a.m.';
	elseif (get_comment_time('a')=='pm') :
		$cmeridian = 'p.m.';
	endif;

	// Reformat 12:00 and 00:00 to noon and midnight

	if (get_comment_time('H:i')=='00:00') :
		if ($capnoon == "true") :
			$apctime = "Midnight";
		else :
			$apctime = "midnight";
		endif;

	elseif (get_comment_time('H:i')=='12:00') :
		if ($capnoon == "true") :
			$apctime = "Noon";
		else :
			$apctime = "noon";
		endif;

	// Eliminate trailing zeroes from times at the top of the hour and set final output

	elseif (get_comment_time('i')=='00') :
		$apctime = get_comment_time('g')." ".$cmeridian;
	else :
		$apctime = get_comment_time('g:i')." ".$cmeridian;
	endif;

	return $apctime;
}

function ap_comment_time() {
	echo get_ap_comment_time();
}

// Modified date formatting

function get_ap_modified_date() {
	$today = get_option('ap_today');
	$captoday = get_option('ap_captoday');
	$useyear = get_option('ap_useyear');

	// Determine the month and set the AP Style abbreviation

	if (get_the_modified_date('m')=='01') :
		$apmodmonth = 'Jan. ';
	elseif (get_the_modified_date('m')=='02') :
		$apmodmonth = 'Feb. ';
	elseif (get_the_modified_date('m')=='08') :
		$apmodmonth = 'Aug. ';
	elseif (get_the_modified_date('m')=='09') :
		$apmodmonth = 'Sept. ';
	elseif (get_the_modified_date('m')=='10') :
		$apmodmonth = 'Oct. ';
	elseif (get_the_modified_date('m')=='11') :
		$apmodmonth = 'Nov. ';
	elseif (get_the_modified_date('m')=='12') :
		$apmodmonth = 'Dec. ';
	else :
		$apmodmonth = (get_the_modified_date('F'));
	endif;

	// Determine whether the date is within the current year
	
	if (get_the_modified_time('Y')!=date('Y')) :
			$apmodyear = ", ".get_the_modified_time('Y');
	else :
		if ($useyear=="true") :
			$apmodyear = ", ".get_the_modified_time('Y');
		else :
			$apmodyear = "";
		endif;
	endif;

	// Determine whether the date is the current date and set the final output

	if (get_the_modified_date('F j Y')==date('F j Y')) :
		if ($today == "true") :
			if ($captoday == "true") :
				$apmoddate = "Today";
			else :
				$apmoddate = "today";
			endif;
		else :
			$apmoddate = $apmodmonth." ".get_the_modified_date('j')."".$apmodyear;
		endif;
	else :
		$apmoddate = $apmodmonth." ".get_the_modified_date('j')."".$apmodyear; 
	endif;

	return $apmoddate;
}

function ap_modified_date() {
	get_ap_modified_date();
}

// Modified time formatting

function get_ap_modified_time() {
	$capnoon = get_option('ap_capnoon');

	// Format am and pm to AP Style abbreviations

	if (get_the_modified_time('a')=='am') :
		$modmeridian = 'a.m.';
	elseif (get_the_modified_time('a')=='pm') :
		$modmeridian = 'p.m.';
	endif;

	// Reformat 12:00 and 00:00 to noon and midnight

	if (get_the_modified_time('H:i')=='00:00') :
		if ($capnoon == "true") :
			$apmodtime = "Midnight";
		else :
			$apmodtime = "midnight";
		endif;

	elseif (get_the_modified_time('H:i')=='12:00') :
		if ($capnoon == "true") :
			$apmodtime = "Noon";
		else :
			$apmodtime = "noon";
		endif;

	// Eliminate trailing zeroes from times at the top of the hour and set final output

	elseif (get_the_modified_time('i')=='00') :
		$apmodtime = get_the_modified_time('g')." ".$modmeridian;
	else :
		$apmodtime = get_the_modified_time('g:i')." ".$modmeridian;
	endif;

	return ($apmodtime);
}

function ap_modified_time() {
	echo get_ap_modified_time();
}

// Set up plugin options

function set_apstyle_options() {
	add_option('ap_today','true','Use today');
	add_option('ap_captoday','false','Capitalize today');
	add_option('ap_capnoon','false','Capitalize noon and midnight');
	add_option('ap_useyear','false','Use the current year');
}

function unset_apstyle_options() {
	delete_option('ap_today');
	delete_option('ap_captoday');
	delete_option('ap_capnoon');
	delete_option('ap_useyear');
}

register_activation_hook(__FILE__,'set_apstyle_options');
register_deactivation_hook(__FILE__,'unset_apstyle_options');

// Build options page

function admin_apstyle_options(){
	
	// Start the page and build the sidebar
	
	?><div class="wrap"><h2>AP Style Dates and Times plugin options</h2>	
	<div id="apside" style="width:20%;float:right;padding-right:20px;">
		<table class="widefat">
		<thead><tr><th>Plugin information</th></tr></thead>
		<tbody>
			<tr><td>
				<p><strong>&bull; <a href="#options" title="Options">Plugin options</a></strong></p>
				<p><strong>&bull; <a href="#usage" title="Usage">How to use it</a></strong></p>
				<p><strong>&bull; <a href="http://www.rockmycar.net/ap-style-dates-and-times-plugin/" target="_blank" title="Plugin homepage">Plugin homepage</a></strong> <br />
					<em>If you have questions, comments or improvements, feel free to post them at the plugin page.</em></p>
			</td></tr>
		</tbody>
		</table>
		<p>&nbsp;</p>

	</div>
	<div style="width:75%;float:left;" >

		
	<?php
	
	// Manage form submission
	
	if ($_REQUEST['submit']) {
		update_apstyle_options();
	}
	print_apstyle_form();
	?></div></div><?php
}

function update_apstyle_options() {
	$ok = false;
	
	if ($_REQUEST['ap_today']) {
		update_option('ap_today',$_REQUEST['ap_today']);
		$ok = true;
	}
	
	if ($_REQUEST['ap_captoday']) {
		update_option('ap_captoday',$_REQUEST['ap_captoday']);
		$ok = true;
	}
	
	if ($_REQUEST['ap_capnoon']) {
		update_option('ap_capnoon',$_REQUEST['ap_capnoon']);
		$ok = true;
	}
	
	if ($_REQUEST['ap_useyear']) {
		update_option('ap_useyear',$_REQUEST['ap_useyear']);
		$ok = true;
	}
	
	if ($ok) {
		?><div id="message" class="updated fade"><p>Options saved.</p></div><?php
	}
	else {
		?><div id="message" class="error fade"><p>Failed to save options. Please try again.</p></div><?php
	}
}

// Build the rest of the options page

function print_apstyle_form() {
	
	// Get the current option settings
	
	$default_ap_today = get_option('ap_today');
	$default_ap_captoday = get_option('ap_captoday');
	$default_ap_capnoon = get_option('ap_capnoon');
	$default_ap_useyear = get_option('ap_useyear');
	?>
	<p><span class="description">This plugin changes the format of the dates and times of your posts and comments to match Associated Press Style. This is perfect for news and magazine sites running WordPress and for those blog writers with a penchant for style consistency.</span></p>

	<table class="widefat">
	<thead><tr><th>What it does</th></tr></thead>
	<tbody><tr><td><p>&bull; Adds periods to &#8220;am&#8221; and &#8220;pm&#8221; so they become &#8220;a.m.&#8221; and &#8220;p.m.&#8221; <br />
	&bull; Removes extraneous zeros when the time is at the top of the hour. &#8220;1:00 p.m.&#8221; becomes &#8220;1 p.m.&#8221; <br />
	&bull; Changes &#8220;12:00 a.m.&#8221; to &#8220;midnight&#8221; and &#8220;12:00 p.m.&#8221; to &#8220;noon.&#8221; <br />
	&bull; Correctly abbreviates months in the date to match AP Style rules. Those are: &#8220;Jan.&#8221; &#8220;Feb.&#8221; &#8220;March&#8221; &#8220;April&#8221; &#8220;May&#8221; &#8220;June&#8221; &#8220;July&#8221; &#8220;Aug.&#8221; &#8220;Sept.&#8221; &#8220;Oct.&#8221; &#8220;Nov.&#8221; &#8220;Dec.&#8221;</p></tbody></td></tr>
	</table>
	<p>&nbsp;</p>
	<table class="widefat" id="options">
	<thead><tr><th colspan="2">Options</th></tr></thead>
	<tbody>
		<tr>
			<td>
	<p><strong>Use &#8216;today&#8217; when the post date is the current date</strong>
			</td>
			<td>
			<p><span class="description">Though technically not an AP Style rule, many publications use the word &#8220;today&#8221; when it&#8217;s the current date.</span></p>
			<form method="post">
				<p style="line-height:24px;"><input type="radio" name="ap_today" value="true" id="todaytrue" <?php if ($default_ap_today == "true") { echo "checked=\"true\""; } ?> /><label for="todaytrue"> Use the word &#8220;today&#8221; when it&#8217;s the current date.</label><br />
				<input type="radio" name="ap_today" value="false" id="todayfalse" <?php if ($default_ap_today == "false") { echo "checked=\"true\""; } ?> /><label for="todayfalse"> Use the actual date.</label></p>
			</td>
		</tr>
		<tr>
			<td>
				<p><strong>If you&#8217;re using &#8216;today&#8217;, would you like it capitalized?</strong></p>
			</td>
			<td>
				<p><span class="description">Depending on where you place the date in your template, you may want the word capitalized.</span></p>
				<p style="line-height:24px;"><input type="radio" name="ap_captoday" value="true" id="captodaytrue" <?php if ($default_ap_captoday == "true") { echo "checked=\"true\""; } ?> /><label for="captodaytrue"> Capitalize the word &#8220;today&#8221; (i.e.: <strong>Today</strong>).</label><br />
				<input type="radio" name="ap_captoday" value="false" id="captodayfalse" <?php if ($default_ap_captoday == "false") { echo "checked=\"true\""; } ?> /><label for="captodayfalse"> Do not capitalize the word &#8220;today&#8221; (i.e.: <strong>today</strong>)</label></p>
			</td>
		</tr>
		<tr>
			<td>
				<p><strong>Display the year in the date if it matches the current year?</strong>
			</td>
			<td>
				<p><span class="description">According to AP Style, when using a date that is within the current year, the actual year is not needed in the date. However, you may want to include it in the interest of clarity.<span></p>
				<p style="line-height:24px;"><input type="radio" name="ap_useyear" value="true" id="yeartrue" <?php if ($default_ap_useyear == "true") { echo "checked=\"true\""; } ?> /><label for="yeartrue"> Print the year when the date is within the current year.</label><br />
				<input type="radio" name="ap_useyear" value="false" id="yearfalse" <?php if ($default_ap_useyear == "false") { echo "checked=\"true\""; } ?> /><label for="yearfalse"> Do not print the year if the date is within the current year.</label></p>
			</td>
		</tr>
		<tr>
			<td>
				<p><strong> Capitalize &#8216;noon&#8217; and &#8216;midnight&#8217; when printing the time?</strong>
			</td>
			<td>
				<p><span class="description">Depending on where you place the time in you template, you may want the words capitalized.</span></p>
				<p style="line-height:24px;"><input type="radio" name="ap_capnoon" value="true" id="capnoontrue" <?php if ($default_ap_capnoon == "true") { echo "checked=\"true\""; } ?> /><label for="capnoontrue"> Yes, capitalize &#8216;noon&#8217; and &#8216;midnight&#8217; (i.e.: <strong>Noon</strong> and <strong>Midnight</strong>).</label><br />
				<input type="radio" name="ap_capnoon" value="false" id="capnoonfalse" <?php if ($default_ap_capnoon == "false") { echo "checked=\"true\""; } ?> /><label for="capnoonfalse"> Do not capitalize the words &#8216;noon&#8217; and &#8216;midnight&#8217; (i.e.: <strong>noon</strong> and <strong>midnight</strong>).</label><br /></p>
		<input class="button-primary" type="submit" name="submit" value="Save Options" /><br /><br />
	</form>
			</td>
			</tr>
	</tbody>
	</table>
	<p>&nbsp;</p>
	<table class="widefat" id="usage">
	<thead><tr><th colspan="2">Usage</th></tr></thead>
	<tbody>
		<tr>
			<td colspan="2">
			<p><span class="description">To use this plugin, you have to edit your template files, replacing WordPress&#8217; default date and time tags with the new  ones listed below. You can use it on dates and times for posts, comments and post modified information.</span></p>
			</td>		
		</tr>
		<tr>
			<td width="25%">
				<p><strong>For post dates and times</strong></p>
			</td>
			<td>
				<p><strong>&lt;?php ap_time(); ?&gt;</strong> will print the post&#8217;s time in AP Style.</p>
				<p><strong>&lt;?php get_ap_time(); ?&gt;</strong> returns the post&#8217;s time in AP Style for use in PHP.</p>
				<p>Find the <code>&lt;?php the_time(); ?&gt;</code> tag in your template files (single.php, index.php, search.php, archives.php, page.php, etc.) and replace it with:</p>
				<p><code>&lt;?php if (function_exists('ap_time')) { ap_time(); } else { the_time(); } ?&gt;</code></p>
				<p><span class="description"><strong>Using the if statement will make sure the date is still printed if the plugin is deactivated.</strong></span></p>
				<p>Likewise, <strong>&lt;?php ap_date(); ?&gt;</strong> will print the post&#8217;s date in AP Style.</p>
				<p><strong>&lt;?php get_ap_date(); ?&gt;</strong> returns the post&#8217;s date in AP Style for use in PHP.</p>
				<p>Find the <code>&lt;?php the_date(); ?&gt;</code> tag in your template files and replace it with:</p>
				<p><code>&lt;?php if (function_exists('ap_date')) { ap_date(); } else { the_date(); } ?&gt;</code></p>
				<p><strong>Here&#8217;s how I use it:</strong></p>
				<p>I put the time before the date, so my code looks like this:</p>
				<p><code>&lt;?php if (function_exists('ap_time')) { ap_time(); } else { the_time(); } ?&gt; &lt;?php if (function_exists('ap_date')) { ap_date(); } else { the_date(); } ?&gt;</code></p>
				<p>That prints something like <strong>1:15 p.m. Sept. 23, 2008</strong> or <strong>noon Sept. 23</strong>, depending on the option settings.</p>
			</td>
		</tr>
		<tr>
			<td>
				<p><strong>For comment dates and times</strong></p>
			</td>
			<td>
				<p><strong>&lt;?php ap_comment_time(); ?&gt;</strong> will print the comment times in AP Style.</p>
				<p><strong>&lt;?php get_ap_comment_time(); ?&gt;</strong> returns the comment times in AP Style for use in PHP.</p>
				<p>Find the <code>&lt;?php comment_time(); ?&gt;</code> tag in your comments template file (comments.php) and replace it with: </p>
				<p><code>&lt;?php if (function_exists('ap_comment_time')) { ap_comment_time(); } else { comment_time(); } ?&gt;</code></p>
				<p><strong>&lt;?php ap_comment_date(); ?&gt;</strong> will print the comment dates in AP Style.</p>
				<p><strong>&lt;?php get_ap_comment_date(); ?&gt;</strong> returns the comment dates in AP Style for use in PHP.</p>
				<p>Find the <code>&lt;?php comment_date(); ?&gt;</code> tag in your comments template file and replace it with:</p>
				<p><code>&lt;?php if (function_exists('ap_comment_date')) { ap_comment_date(); } else { comment_date(); } ?&gt;</code></p>
			</td>
		</tr>
		<tr>
			<td>
				<p><strong>For modified dates and times</strong></p>
			</td>
			<td>
				<p><strong>&lt;?php ap_modified_time(); ?&gt;</strong> will print the post&#8217;s modified time in AP Style.</p>
				<p><strong>&lt;?php get_ap_modified_time(); ?&gt;</strong> returns the post&#8217;s modified time in AP Style for use in PHP.</p>
				<p>Find the <code>&lt;?php the_modified_time(); ?&gt;</code> tag in your template files (single.php, index.php, search.php, archives.php, page.php, etc.) and replace it with: </p>
				<p><code>&lt;?php if (function_exists('ap_modified_time')) { ap_modified_time(); } else { the_modified_time(); } ?&gt;</code></p>
				<p><strong>&lt;?php ap_modified_date(); ?&gt;</strong> will print the post&#8217;s modified date in AP Style.</p>
				<p><strong>&lt;?php get_ap_modified_date(); ?&gt;</strong> returns the post&#8217;s modified date in AP Style for use in PHP.</p>
				<p>Find the <code>&lt;?php the_modified_date(); ?&gt;</code> tag in your template files and replace it with:</p>
				<p><code>&lt;?php if (function_exists('ap_modified_date')) { ap_modified_date(); } else { the_modified_date(); } ?&gt;</code></p>
			</td>
		<tr>
	</tbody>
	</table>
	<p>&nbsp;</p>

	<?php
}

// Finish it up!

function modify_menu() {
	add_options_page(
		'AP Style Dates and Times plugin options',
		'AP Dates &amp; Times',
		'manage_options',
		__FILE__,
		'admin_apstyle_options'
		);
}

add_action('admin_menu','modify_menu');
?>