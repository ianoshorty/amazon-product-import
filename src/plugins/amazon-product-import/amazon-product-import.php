<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/ianoshorty/amazon-product-import
 * @since             1.0.0
 * @package           Amazon_Product_Import
 *
 * @wordpress-plugin
 * Plugin Name:       Amazon Product Import
 * Plugin URI:        https://github.com/ianoshorty/amazon-product-import/
 * Description:       This plugin imports products from the Amazon Product SDK into The Events Calendar "Event" Post type which have an Amazon Product Identifier (or ISBN) attached.
 * Version:           1.0.0
 * Author:            Ian Outterside
 * Author URI:        https://github.com/ianoshorty/amazon-product-import/
 * License:           MIT
 * License URI:       https://opensource.org/licenses/MIT
 * Text Domain:       amazon-product-import
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'AMAZON_PRODUCT_IMPORT_VERSION', '1.0.0' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-amazon-product-import.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_plugin_name() {

	$plugin = new Amazon_Product_Import();
	$plugin->run();

}
run_plugin_name();
