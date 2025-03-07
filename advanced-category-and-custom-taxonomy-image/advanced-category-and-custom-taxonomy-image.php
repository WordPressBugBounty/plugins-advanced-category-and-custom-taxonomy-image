<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @since             2.0.0
 * @package           Advanced_Category_And_Custom_Taxonomy_Image
 *
 * Plugin Name:       Advanced Category and Custom Taxonomy Image
 * Plugin URI:        https://wordpress.org/plugins/advanced-category-and-custom-taxonomy-image/
 * Description:       Advanced Category and Taxonomy Image Plugin allow you to add image to your category / tag / custom taxonomy for different platforms (Mobile/ Desktop/ Tablet/ Mac/ Any etc).
 * Version:           2.0.1
 * Author:            Sajjad Hossain Sagor
 * Author URI:        https://sajjadhsagor.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       advanced-category-and-custom-taxonomy-image
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) die;

/**
 * Currently plugin version.
 */
define( 'ADVANCED_CATEGORY_AND_CUSTOM_TAXONOMY_IMAGE_VERSION', '2.0.1' );

/**
 * Define Plugin Folders Path
 */
define( 'ADVANCED_CATEGORY_AND_CUSTOM_TAXONOMY_IMAGE_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

define( 'ADVANCED_CATEGORY_AND_CUSTOM_TAXONOMY_IMAGE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

define( 'ADVANCED_CATEGORY_AND_CUSTOM_TAXONOMY_IMAGE_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-plugin-activator.php
 * 
 * @since    2.0.0
 */
function activate_advanced_category_and_custom_taxonomy_image()
{
	require_once ADVANCED_CATEGORY_AND_CUSTOM_TAXONOMY_IMAGE_PLUGIN_PATH . 'includes/class-plugin-activator.php';
	
	Advanced_Category_And_Custom_Taxonomy_Image_Activator::activate();
}

register_activation_hook( __FILE__, 'activate_advanced_category_and_custom_taxonomy_image' );

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-plugin-deactivator.php
 * 
 * @since    2.0.0
 */
function deactivate_advanced_category_and_custom_taxonomy_image()
{
	require_once ADVANCED_CATEGORY_AND_CUSTOM_TAXONOMY_IMAGE_PLUGIN_PATH . 'includes/class-plugin-deactivator.php';
	
	Advanced_Category_And_Custom_Taxonomy_Image_Deactivator::deactivate();
}

register_deactivation_hook( __FILE__, 'deactivate_advanced_category_and_custom_taxonomy_image' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 * 
 * @since    2.0.0
 */
require ADVANCED_CATEGORY_AND_CUSTOM_TAXONOMY_IMAGE_PLUGIN_PATH . 'includes/class-plugin.php';

/**
 * Includes the plugin's functions file.
 *
 * This file contains utility functions.
 *
 * @since 2.0.1
 */
require ADVANCED_CATEGORY_AND_CUSTOM_TAXONOMY_IMAGE_PLUGIN_PATH . 'includes/functions.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    2.0.0
 */
function run_advanced_category_and_custom_taxonomy_image()
{
	$plugin = new Advanced_Category_And_Custom_Taxonomy_Image();
	
	$plugin->run();
}

run_advanced_category_and_custom_taxonomy_image();
