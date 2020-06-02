**Originally forked from the [AP Style Dates and Times plugin](https://wordpress.org/plugins/ap-style-dates-and-times/) on Wordpress.org.**

#AP Style Dates and Times
Contributors: Daniel J.Schneider, Tom Chambers
Tags: date, time, format, AP Style
Requires at least: 2.5
Tested up to: 3.8
Stable tag: 2.2

Formats the date and time of your posts and comments into Associated Press Style.

##Description

This plugin changes the format of the dates and times of your posts and comments to match Associated Press Style. This is perfect for news and magazine sites running WordPress and for those blog writers with a penchant for style consistency.

###Here's what it does:

- Adds periods to "am" and "pm" so they become "a.m." and "p.m."
- Removes extraneous zeros when the time is at the top of the hour. "1:00 p.m." becomes "1 p.m."
- Changes "12:00 a.m." to "midnight" and "12:00 p.m." to "noon."
- Correctly abbreviates months in the date to match AP Style rules. Those are: "Jan." "Feb." "March" "April" "May" "June" "July" "Aug." "Sept." "Oct." "Nov." "Dec."

###Optional features:

- If the post year is the same as the current year, you can set it to not print the year in the date output to adhere to AP Style rules.
- If the date of the post is the same as the current date, you can set it too print "today" instead of the date. Technically that's not an AP Style rule, but a common newspaper style nonetheless.
- You can have it capitalize the words "today," "noon" and "midnight."

Therefore, if your post was published at 1:13 in the afternoon on September 22, 2007, this plugin will make the time look like "1:13 p.m." and date look like "Sept. 22, 2007"

##Installation

1. Download plugin
2. Unzip & upload the `ap-style-dates-and-times` folder to `/wp-content/plugins/`
3. Activate the plugin in the plugins admin menu
4. Select your options on the plugin admin page

##Changelog

###2.2
- ADDED: get_ version of functions to return dates for use in PHP.
- REMOVED: references to donations
- FIX: Options page design tweaks
- FIX: Resurrected plugin

###2.1
- FIX: Typos in comment and updated date code.

###2.0
- ADDED: Ability to use the plugin for modified dates and times.
- ADDED: Plugin options admin page.
- REMOVED: Need to pass parameters with plugin tags. If you're upgrading from 1.1, the plugin will ignore the parameters set in your template tags and use those you set on the plugin options page.
- ADDED: Option to print the current year or not.
- FIX: Plugin now checks for the WordPress UTC offset to set the current timezone when calculating whether the post was posted on the current date.

###1.1
- FIX: Made it so the plugin checked the year when calculating whether the post or comment date is the current date.
- ADDED: Option to not use "today" when the date is the current date.
- ADDED: Option to capitalize words "Noon" "Midnight" and "Today".

###1.0
- Original plugin released

##Usage

To use this plugin, you have to edit your template files, replacing WordPress' default date and time tags with the new ones listed below. You can use it on dates and times for posts, comments and post modified information.

####For post dates and times

The tag `<?php ap_time(); ?>` will print the post's time in AP Style.

Find the `<?php the_time(); ?>` tag in your template files (single.php, index.php, search.php, archives.php, page.php, etc.) and replace it with:

`<?php if (function_exists('ap_time')) { ap_time(); } else { the_time(); } ?>`

#####Using the if statement will make sure the date is still printed if the plugin is deactivated.

Likewise, `<?php ap_date(); ?>` will print the post's date in AP Style.

Find the `<?php the_date(); ?>` tag in your template files and replace it with:

`<?php if (function_exists('ap_date')) { ap_date(); } else { the_date(); } ?>`

####Here's how I use it:

I put the time before the date, so my code looks like this:

`<?php if (function_exists('ap_time')) { ap_time(); } else { the_time(); } ?>` `<?php if (function_exists('ap_date')) { ap_date(); } else { the_date(); } ?>`

That prints something like 1:15 p.m. Sept. 23, 2008 or noon Sept. 23, depending on the option settings.

####For comment dates and times

The tag `<?php ap_comment_time(); ?>` will print the comment times in AP Style.

Find the `<?php comment_time(); ?>` tag in your comments template file (comments.php) and replace it with:

`<?php if (function_exists('ap_comment_time')) { ap_comment_time(); } else { comment_time(); } ?>`

The tag `<?php ap_comment_date(); ?>` will print the comment dates in AP Style.

Find the `<?php comment_date(); ?>` tag in your comments template file and replace it with:

`<?php if (function_exists('ap_comment_date')) { ap_comment_date(); } else { comment_date(); } ?>`

####For modified dates and times

The tag `<?php ap_modified_time(); ?>` will print the post's modified time in AP Style.

Find the `<?php the_modified_time(); ?>` tag in your template files (single.php, index.php, search.php, archives.php, page.php, etc.) and replace it with:

`<?php if (function_exists('ap_modified_time')) { ap_modified_time(); } else { the_modified_time(); } ?>`

The tag `<?php ap_modified_date(); ?>` will print the post's modified date in AP Style.

Find the `<?php the_modified_date(); ?>` tag in your template files and replace it with:

`<?php if (function_exists('ap_modified_date')) { ap_modified_date(); } else { the_modified_date(); } ?>`

##Questions, comments, suggestions
This is a pretty simple plugin, but if you find anything wrong with it, please let me know.  Also, if you have suggestions to improve it or comments, don't hesitate to let them be known.

