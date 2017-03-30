=== Merge duplicate terms ===
Contributors: zabatonni
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=zabatonni@gmail.com&currency_code=USD&item_name=Merge+duplicate+terms+by+Zabatonni
Tags: merge, duplicate, tags, categories, terms, taxonomy
Requires at least: 4.2
Tested up to: 4.4.1
Stable tag: 4.4.1
License: GPLv2 only
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Plugin for easy merging duplicate terms (tags, categories, ...)

== Description ==

Automatically finds duplicate terms (with same name) in each taxonomy, then moves posts from duplicates to its original terms and deletes duplicate terms.

You can choose which duplicates you want to merge and delete.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/zaba-merge-duplicate-terms` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Go to Tools->Merge terms and follow instructions

== Changelog ==

= 1.1 =
* Ability to choose taxonomies which you would like to use
* Increased max_execution_time if server allows it so it can run through long operations

= 1.0 =
* First release