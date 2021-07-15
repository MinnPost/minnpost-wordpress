=== Republication Tracker Tool ===
Contributors: innlabs
Donate link: https://inn.org/donate
Tags: publishers, news
Requires at least: 4.4
Requires PHP: 5.3
Tested up to: 5.2.2
Stable tag: 1.0.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds a widget to allow readers to easily acquire Creative-Commons-licensed HTML of articles to facilitate embedding posts on external sites. Includes a tracking mechanism similar to ProPublica's PixelPing.

== Description ==

A plugin that allows users to add a widget to allow readers to easily acquire Creative-Commons-licensed HTML of articles to facilitate embedding posts on external sites. Includes a tracking mechanism similar to ProPublica's PixelPing. Built and maintained by [INN Labs](https://labs.inn.org/).

== Installation ==

1. Upload `plugin-name.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Add the widget to your per-post sidebars. It doesn't work outside of single post pages.

== Frequently Asked Questions == 

= How does the tracking mechanism work? = 

The tracking mechanism is similiar to ProPublica's [PixelPing](https://www.propublica.org/pixelping) tracking technology. 

In this plugin, the tracking is achieved through an image element included inside of the republishable content that collects data from the republishing site and sends that data to Google Analytics.

== Changelog ==

= 1.0.2 =

- Sets a default width for the site icon that is displayed at the bottom of republished articles. Previously it did not have a width set which was causing some sites to experience larger than expected images at the end of their republished articles.
- Added the new `republication_tracker_tool_allowed_tags_excerpt` filter which allows developers to choose what tags to allow and exclude from their shareable content. The only tags that are now excluded by default are `form` tags. Read more about it <a href="https://github.com/Automattic/republication-tracker-tool/blob/master/docs/removing-republish-button-from-categories.md" target="_blank">here</a>.
- Added the ability to toggle the Republication sharing widget on or off for individual posts.
- Added the new `hide_republication_widget` filter which allows developers to programatically hide the Republication sharing widget on specific posts, categories, tags, etc. Read more about it <a href="https://github.com/Automattic/republication-tracker-tool/blob/master/docs/removing-republish-button-from-categories.md" target="_blank">here</a>.

= 1.0.1 =

- Changes the way we fire the pixel. Instead of firing it at /wp-content/plugins/republication-tracker-tool/?query_params, we'll now fire it at site.com/*?query_params. This should be backwards compatible so sites experiencing the 403 issue in their republished posts should have the issue resolved once updated.
- Updates the modal_actions function to use .html() instead of .text() when copying the content into the sharable modal so that the pixel &'s don't get encoded into &amp; and cause issues if pasted into a CMS that doesn't automatically decode html entities.

= 1.0 =
* Initial release
