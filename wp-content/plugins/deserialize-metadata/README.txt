=== Deserialize Metadata ===
Contributors: jonathanstegall, minnpost
Tags: metadata, import
Requires at least: 4.5.3
Tested up to: 4.7
Stable tag: 0.0.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

When migrating from another system (i.e. Drupal), WordPress can require data that is currently serialized to be unserialized and stored in its own WordPress-specific tables/columns. This plugin can look for such data, and deserialize and store it, based on the plugin settings.

== Description ==

When migrating from another system (i.e. Drupal), WordPress can require data that is currently serialized to be unserialized and stored in its own WordPress-specific tables/columns. This plugin can look for such data, and deserialize and store it, based on the plugin settings.

This plugin handles one (configurable) imported field at a time, so all imported, serialized data needs to be stored in that single field. Each key in that field can be mapped to any column, and stored in either `wp_postmeta` or `wp_posts`, as shown below:

```
if ( $maps[$key]['wp_table'] === 'wp_postmeta' && $value != '' && $value != NULL ) {
    add_post_meta( $post_id, $maps[$key]['wp_column'], $value, $maps[$key]['unique'] );
} else if ( $maps[$key]['wp_table'] === 'wp_posts' && $value != '' && $value != NULL ) {
    $post = array(
        'ID' => $post_id,
        $maps[$key]['wp_column'] => $value
    );
    wp_update_post( $post );
}
```

The `wp_schedule_event` method is used to deserialize the data and place it into its appropriate fields, and it can run hourly, twice daily, or daily.

== Installation ==

1. Upload the `deserialize-metadata` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Fill out the settings on the Deserialize Metadata subpage of the Settings menu


