=== Deserialize Metadata ===
Contributors: jonathanstegall, minnpost
Donate link: http://code.minnpost.com/
Tags: metadata, import
Requires at least: 4.5.3
Tested up to: 4.5.3
Stable tag: 0.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

When migrating from another system (ie Drupal, in this case), it can be necessary to store imported, serialized data unserialized and in its own database specified tables/columns. This plugin can look for this data, and handle it based on its $config array, each time it is activated.

== Installation ==

1. Upload the `deserialize-metadata` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress