<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @package           Advanced_Category_And_Custom_Taxonomy_Image
 * @author            Sajjad Hossain Sagor <sagorh672@gmail.com>
 *
 * Plugin Name:       Advanced Category and Custom Taxonomy Image
 * Plugin URI:        https://wordpress.org/plugins/advanced-category-and-custom-taxonomy-image/
 * Description:       Advanced Category and Taxonomy Image Plugin allow you to add image to your category / tag / custom taxonomy for different platforms (Mobile/ Desktop/ Tablet/ Mac/ Any etc).
 * Version:           2.0.5
 * Requires at least: 6.5
 * Requires PHP:      8.0
 * Author:            Sajjad Hossain Sagor
 * Author URI:        https://sajjadhsagor.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       advanced-category-and-custom-taxonomy-image
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Currently plugin version.
 */
define( 'ADVANCED_CATEGORY_AND_CUSTOM_TAXONOMY_IMAGE_PLUGIN_VERSION', '2.0.4' );

/**
 * Define Plugin Folders Path
 */
define( 'ADVANCED_CATEGORY_AND_CUSTOM_TAXONOMY_IMAGE_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

define( 'ADVANCED_CATEGORY_AND_CUSTOM_TAXONOMY_IMAGE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

define( 'ADVANCED_CATEGORY_AND_CUSTOM_TAXONOMY_IMAGE_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-advanced-category-and-custom-taxonomy-image-activator.php
 *
 * @since    2.0.0
 */
function on_activate_advanced_category_and_custom_taxonomy_image() {
	require_once ADVANCED_CATEGORY_AND_CUSTOM_TAXONOMY_IMAGE_PLUGIN_PATH . 'includes/class-advanced-category-and-custom-taxonomy-image-activator.php';

	Advanced_Category_And_Custom_Taxonomy_Image_Activator::on_activate();
}

register_activation_hook( __FILE__, 'on_activate_advanced_category_and_custom_taxonomy_image' );

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-advanced-category-and-custom-taxonomy-image-deactivator.php
 *
 * @since    2.0.0
 */
function on_deactivate_advanced_category_and_custom_taxonomy_image() {
	require_once ADVANCED_CATEGORY_AND_CUSTOM_TAXONOMY_IMAGE_PLUGIN_PATH . 'includes/class-advanced-category-and-custom-taxonomy-image-deactivator.php';

	Advanced_Category_And_Custom_Taxonomy_Image_Deactivator::on_deactivate();
}

register_deactivation_hook( __FILE__, 'on_deactivate_advanced_category_and_custom_taxonomy_image' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 *
 * @since    2.0.0
 */
require ADVANCED_CATEGORY_AND_CUSTOM_TAXONOMY_IMAGE_PLUGIN_PATH . 'includes/class-advanced-category-and-custom-taxonomy-image.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    2.0.0
 */
function run_advanced_category_and_custom_taxonomy_image() {
	$plugin = new Advanced_Category_And_Custom_Taxonomy_Image();

	$plugin->run();
}

run_advanced_category_and_custom_taxonomy_image();

/**
 * Retrieves the URL or HTML image tag for a taxonomy image.
 *
 * This function retrieves the image URL associated with a given taxonomy term ID.
 * It can optionally return an HTML <img> tag with specified classes.
 *
 * @since    2.0.0
 * @param    int|string   $term_id        The ID of the taxonomy term. Defaults to ''.
 * @param    bool         $return_img_tag Optional. Whether to return an HTML <img> tag. Defaults to false.
 * @param    array|string $extra_classes  Optional. An array or space separated string of CSS classes to add to the <img> tag.
 *                                        Defaults to an empty string.
 * @return   string|empty                 The image URL or HTML <img> tag, or an empty string if no image is found or term ID is invalid.
 */
function get_taxonomy_image( $term_id = '', $return_img_tag = false, $extra_classes = '' ) {
	$term_id = ! intval( $term_id ) ? get_queried_object()->term_id : intval( $term_id );

	// get all image field enabled taxonomies.
	$enabled_taxonomies = Advanced_Category_And_Custom_Taxonomy_Image::get_option( 'enabled_taxonomies', 'ad_cat_tax_img_basic_settings' );

	// get all image field enabled devices.
	$enabled_devices = Advanced_Category_And_Custom_Taxonomy_Image::get_option( 'enabled_devices', 'ad_cat_tax_img_advanced_settings' );

	$device_image_url = ''; // Set default image url to empty.

	// previous version db name was universal, so for compatibility we are checking if universal exists anymore.
	$any_device_image_url = Advanced_Category_And_Custom_Taxonomy_Image::get_any_device_image( $term_id );

	// check if any taxonomy enabled.
	if ( ! empty( $enabled_taxonomies ) ) {
		// check if any device enabled.
		if ( ! empty( $enabled_devices ) ) {
			$detect = new \Detection\MobileDetect();

			// registed custom image field for each enabled devices.
			foreach ( $enabled_devices as $enabled_device ) {
				if ( 'android' === $enabled_device && $detect->isAndroidOS() ) {
					$device_image_url = get_term_meta( $term_id, 'tax_image_url_' . $enabled_device, true );
					break; // android match found no need to check further.
				} elseif ( 'iphone' === $enabled_device && $detect->isiOS() ) {
					$device_image_url = get_term_meta( $term_id, 'tax_image_url_' . $enabled_device, true );
					break; // iOS match found no need to check further.
				} elseif ( 'windowsph' === $enabled_device && $detect->version( 'Windows Phone' ) ) {
					$device_image_url = get_term_meta( $term_id, 'tax_image_url_' . $enabled_device, true );
					break; // Windows Phone match found no need to check further.
				} elseif ( 'mobile' === $enabled_device && $detect->isMobile() ) {
					$device_image_url = get_term_meta( $term_id, 'tax_image_url_' . $enabled_device, true );
					break; // Any Mobile match found no need to check further.
				} elseif ( 'tablet' === $enabled_device && $detect->isTablet() ) {
					$device_image_url = get_term_meta( $term_id, 'tax_image_url_' . $enabled_device, true );
					break; // Any Mobile match found no need to check further.
				} elseif ( 'desktop' === $enabled_device ) {
					$device_image_url = get_term_meta( $term_id, 'tax_image_url_' . $enabled_device, true );
					break; // Dektop match found no need to check further.
				}
			}
		}
	} else {
		$device_image_url = __( 'Please Enable Taxonomies First!', 'advanced-category-and-custom-taxonomy-image' );
	}

	if ( empty( $device_image_url ) && ! empty( $any_device_image_url ) ) {
		// if no image found for enabled devices, opt for default any device if available.
		$device_image_url = $any_device_image_url;
	}

	$classes = is_array( $extra_classes ) ? implode( ' ', $extra_classes ) : esc_attr( $extra_classes );
	$result  = filter_var( $return_img_tag, FILTER_VALIDATE_BOOLEAN ) ? "<img src='" . esc_url( $device_image_url ) . "' class='" . esc_attr( $classes ) . "'>" : esc_url( $device_image_url );

	return ! empty( $device_image_url ) ? $result : __( 'Please Upload Image First!', 'advanced-category-and-custom-taxonomy-image' );
}
