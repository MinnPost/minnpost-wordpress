=== Plugin Name ===
Contributors: StephenCronin
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=sjc@scratch99.com&currency_code=&amount=&return=&item_name=WP-RemoveWidgetTitles
Tags: widget, widget titles, hide, remove, usability
Requires at least: 2.6.0
Tested up to: 4.8
Stable tag: 1.0
Removes the title from any widget that has a title starting with the "!" character.

== Description ==
The [Remove Widget Titles](http://scratch99.com/products/remove-widget-titles/) plugin removes the title from any widget that has a title starting with the "!" character.

= Why Use It? =
This allows you to give widgets a title in the backend for convenience - so you can quickly see which widget is which, rather than having to open them to work out what they are for - without having to show the title on the front end.

= How To Use (once plugin is installed) =
If you have a widget for which you do not want the title to appear on the front end of your site, simply add the "!" character to the start of the widget title (in Appearance -> Widgets).

= Compatibility =
* This plugin requires WordPress 2.6 or above.
* I am not currently aware of any compatibility issues with any other WordPress plugins.

= Similar Plugins =
There is another plugin called [Hide Widget Title](http://wordpress.org/extend/plugins/hide-widget-title/), but that leaves the widget title in the page source and hides it using CSS (adding another http request which slows load time slightly). Remove Widget Titles actually removes the widget title from the HTML. The plugin is only 7 lines of code, so it is extremely light and has minimal impact on performance.

= Support =
This plugin is officially not supported (due to my time constraints), but if you leave a comment on the plugin's home page or [contact me](http://www.scratch99.com/contact/), I'll try to help if I can.

= Disclaimer =
This plugin is released under the [GPL licence](http://www.gnu.org/copyleft/gpl.html). I do not accept any responsibility for any damages or losses, direct or indirect, that may arise from using the plugin or these instructions. This software is provided as is, with absolutely no warranty. Please refer to the full version of the GPL license for more information.

= Acknowledgements =
This plugin was originally written for [QPS Media](http://twitter.com/#!/qpsmedia), who have allowed me to release it to the WordPress community and maintain ownership of the plugin.

== Installation ==
1. Download the plugin file and unzip it.
1. Upload the `remove-widget-titles` folder to the `wp-content/plugins/` folder.
1. Activate the Remove Widget Titles plugin within WordPress.

Alternatively, you can install the plugin automatically through the WordPress Admin interface by going to Plugins -> Add New and searching for Remove Widget Titles.

== Changelog ==

= 1.0 (23 November 2011) =
* Initial Release
