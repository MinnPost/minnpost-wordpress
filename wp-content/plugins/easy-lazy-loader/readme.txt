=== Easy Lazy Loader ===
Contributors: iprodev
Donate link: https://iprodev.com/easy-lazy-loader
Tags: lazy load, images, videos, audios, iframes, front-end optimization
Requires at least: 3.2
Tested up to: 4.9
Stable tag: 1.1.2
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl.html

Lazy load images, videos, iframes and audios to improve page load times and server bandwidth. Degrades gracefully for non-js users.

== Description ==

Lazy load images, videos, iframes and audios to improve page load times. Uses jQuery.sonar to only load an element when it's visible in the viewport.

**Easy Lazy Loader** is the most fully featured, incredibly easy to set up lazy load plugin for *WordPress*. Use the plugins admin settings to easily define what elements are lazy loaded and when they become visible in the users browser.

You can also lazy load other images, iframes, videos and audios in your theme, by using a simple filter.

Non-javascript visitors gets the original element in noscript.

Compatible with the <a href="https://wordpress.org/plugins/ricg-responsive-images/">RICG Responsive Images</a> plugin for responsive images.

= IMAGE LAZY LOAD =
Images are the number one element that slows page load and increases bandwidth use. **Easy Lazy Loader** works with the responsive images feature introduced in WordPress 4.4.

= VIDEO LAZY LOAD =
**Easy Lazy Loader** supports all WordPress video Embeds including Youtube, Vimeo and HTML5 video - for a full list see the [WordPress Codex Embeds](http://codex.wordpress.org/Embeds) list. The WordPress embed method of copying and pasting the video url into posts and pages content area is fully supported.

= AUDIO LAZY LOAD =
**Easy Lazy Loader** supports all WordPress audio Embeds including SoundCloud, Spotify and HTML5 audio - for a full list see the [WordPress Codex Embeds](http://codex.wordpress.org/Embeds) list. The WordPress embed method of copying and pasting the audio url into posts and pages content area is fully supported.

= iFRAME LAZY LOAD =
**Easy Lazy Loader** has built in support for content that is added by iframe from any source in content and widgets examples

* WordPress embedded media
* Facebook Like boxes with profiles, Like buttons, Recommend
* Google+ Profile
* Google Maps

= PLUGIN COMPATIBILITY =
* Work with any WordPress theme that follows the WordPress Theme Codex
* Fully compatible with WPTouch plugin
* Fully compatible with MobilePress plugin
* Fully compatible with WP-Print plugin
* Fully compatible with Opera Mini browser
* Will not conflict with any plugin that has lazy load built in
* Plugin Developers **Easy Lazy Loader** filter allows them to let lazy load apply to their plugin
* Tested 100% compatible with WP Super Cache and W3 Total Cache plugins
* Tested 100% compatible with Amazon Cloudfront
* Fully compatible with CDN architecture.

= FEATURES =
* Lazy load images, iframes, videos, and audios.
* Custom image placeholder.
* Low-res preview image placeholder.
* Color preview placeholder.
* Skip classes to ignore some elements by class name.
* Full support of jQueryMobile framework
* WordPress Multi site ready.
* Backend support for RTL display.
* Translation ready

= Localization =
* Persian (fa_IR) - [Hemn Chawroka](https://www.iprodev.com/author/admin/) (plugin author)

== Installation ==
1. Download and unzip plugin
2. Upload the 'easy-lazy-loader' folder to the '/wp-content/plugins/' directory,
3. Activate the plugin through the 'Plugins' menu in WordPress.

== Frequently Asked Questions ==

= Whoa, this plugin is using JavaScript. What about visitors without JS? =
No worries. They get the original element in a noscript element. No Lazy Loading for them, though.

= I'm using a CDN. Will this plugin interfere? =
Lazy loading works just fine. The images will still load from your CDN.

= How do I change the placeholder image =

`
add_filter( 'ell_placeholder_url', 'my_custom_placeholder_image' );
function my_custom_placeholder_image( $image ) {
	return 'http://url/to/image';
}
`

= How do I lazy load other images in my theme? =

If you have images output in custom templates or want to lazy load other images in your theme, you may pass the HTML through a filter:

`<?php
$html = '<img src="myimage.jpg" alt="">';
$html = apply_filters( 'easy_lazy_loader_html', $html );
echo $html;
?>`

Or, you can add an attribute called "data-lazy-src" and "data-lazy-type" with the source of the image URL and set the actual image URL to a transparent 1x1 pixel.

== Changelog ==

= 1.1.2 =

* **Fixed:** some bugs and stability improvements.

= 1.1.1 =

* **Optimized:** attachment identifier, now identify images from their URL better.

= 1.1.0 =

* **Optimized:** attachment identifier, now identify images from their URL.
* **Optimized:** placeholders to preserve their ratio as possible.
* **Fixed:** some bugs and stability improvements.

= 1.0.0 =

* Initial working version
