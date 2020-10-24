=== Plugin Name ===
Contributors: Ian Outterside
Tags: "amazon", "pa-api", "paapi", "paapi5.0", "the-events-calendar", "product-import", "product", "import", "isbn"
Requires at least: 3.0.1
Tested up to: 5.5
Stable tag: 5.5
License: MIT
License URI: https://opensource.org/licenses/MIT

A WordPress Plugin to import products from the Amazon Product SDK into The Events Calendar Plugin.

== Description ==

This plugin allows the user to import products from the [Amazon Product SDK](https://webservices.amazon.com/paapi5/documentation/)
into The Events Calendar Events post type (see https://theeventscalendar.com/products/wordpress-events-calendar/).

Fields currently imported include:

 - Product Title
 - Product Release Date
 - Product Image URL (medium size)
 - Product Author

We can't import the description (as this is not exposed by Amazon APIs unfortunately).

These fields are mapped to _tribe_events_ post types and stored as custom meta data.
See src/plugins/amazon-product-import/admin/class-amazon-product-import-admin.php.

To use the plugin:

 - Add an Amazon Product Identifier (or ISBN number) to an Event (tribe_events post type)
   using the new meta box added on the right side of the post.
 - Make sure to set a release date in the future
 - Click the "Import Products" button in the "Amazon Product Import" admin menu.

== Installation ==

Add the plugin to your plugin directory and active the plugin.

== Changelog ==

= 1.0 =
* Initial release