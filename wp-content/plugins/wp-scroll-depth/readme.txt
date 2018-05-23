=== WP Scroll Depth ===
Contributors: firebrandllc, lonkoenig
Tags: Scroll Depth, Analytics, Events, Scroll
Requires at least: 3.2.1
Tested up to: 4.9.6
Stable tag: 1.4.2
License: MIT
License URI: http://opensource.org/licenses/MIT

Add user scrolling events to your Google Analytics simply by installing this plugin.

== Description ==
WP Scroll Depth is a simple WordPress plugin that loads and calls the Scroll Depth jQuery script.

Google Analytics doesn't capture user scrolling information by default. This could be very important information if you have tall, scrolling, pages. Scroll Depth sends this information as Google Analytics events.  
*Update:* Google Tag Manager now includes Scroll Depth and Element Visibility triggers.

From the Scroll Depth website:
>Scroll Depth is a small Google Analytics plugin that allows you to measure how far down the page your users are scrolling. It monitors the 25%, 50%, 75%, and 100% scroll points, sending a Google Analytics Event at each one.
>You can also track when specific elements on the page are scrolled into view. On a blog, for example, you could send a Scroll Depth event whenever the user reaches the end of a post.
>The plugin supports Universal Analytics, Classic Google Analytics, and Google Tag Manager.

More information about Scroll Depth can be found in [the Scroll Depth documentation](http://scrolldepth.parsnip.io/).

### Features:
* Simple plugin - you don't need to modify your theme to load the JavaScript
* Implements basic scrolling events (25%, 50%, 75%, 100%) with no additional configuration
* Admin Panel to configure all available options

### Requirements:
* Google Analytics
* jQuery 1.7 or higher

Since this plugin sends Google Analytics Events, you must be running Google Analytics to see any results.

While jQuery is probably already available in your theme, in the unlikely event that it isn't, you can use a plugin like [WP jQuery Plus](https://wordpress.org/plugins/wp-jquery-plus/) to add it to your site.


== Installation ==
###Install from WordPress.org

1. Log into your website administrator panel
1. Go to Plugins page and select "Add New"
1. Search for "WP Scroll Depth"
1. Click "Install Now" on the SearchCloak entry
1. Click Activate Plugin

###Install via ftp

1. Download the plugin zip file using the button above
1. Log into your website administrator panel
1. Go to Plugins page and select "Add New"
1. Click "Upload"
1. Choose your recently downloaded zip file
1. Click the Install Now button
1. Click Activate Plugin


== Frequently Asked Questions ==

= How can I tell if WP Scroll Depth is working? =
###First: Confirm the code is being added to your pages:
Load a page and view source. Look for "scrolldepth." You should find:

1. The line in the `<head>` section where scrolldepth.min.js is loaded.  
1. A script block in the `<head>` section where `jQuery.scrollDepth();` is being called.

If you don't find the code there, then the plugin hasn't loaded. Check the usual suspects about [malfunctioning plugins](https://yoast.com/plugin-not-working/).

###Second: Make sure the code is executing:
Open the JavaScript/debugging console in your browser and load a page from your site.
If there are no errors and the code is being loaded, then it's probably working. Time to test.
If there's an error or conflict, please post in the support forums.

###Third: Confirm events are being sent.
Open your Google Analytics and go to the Reporting tab for your site. 
Click on Real-time and then Events.
Open another window or browser with your site.
Scroll! Scroll like the wind! 

You should see events with a Category of "Scroll Depth."
If you don't see these events, make sure your Google Analytics is properly set up and configured.


== Screenshots ==
1. The WP Scroll Depth admin panel.
1. Google Analytics Real-Time Events.
1. Google Analytics page scroll percentages.

== Changelog ==

= 1.4.2 =
- Tested with WordPress 4.9.6
- Added privacy policy data collection information

= 1.4.1 =
- Tested with WordPress 4.9
- Added notes about Google Tag Manager

= 1.4.0 =
- Tested with WordPress 4.8
- Update scrolldepth library to version 1.0 (2016 12 17)

= 1.3.4 =
Updated "Tested up to" version

= 1.3.3 =
- Removed default value for gaGlobal

= 1.3.2 =
- Fix version number in plugin

= 1.3 =
- Reformat readme.txt
- Try to force new version number

= 1.2.1 =
- Fix readme.txt
- Add missing screenshots
- Add missing test in scrolldepth library

= 1.2 =
Updated scrolldepth library to version 0.9 (2015 11 19)
Added new options:
- gtmOverride
- gaGlobal
- eventHandler
Tested with WordPress 4.4

= 1.1 =
Updated scrolldepth library to version 0.7.1 (2014 12 19)
Tested with WordPress 4.1

= 1.0 =
Initial commit
