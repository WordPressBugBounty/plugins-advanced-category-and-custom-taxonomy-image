<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * @since      2.0.0
 * @package    Advanced_Category_And_Custom_Taxonomy_Image
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) die;

/**
 * Remove plugin options on uninstall/delete
 */
delete_option( 'ad_cat_tax_img_basic_settings' );
delete_option( 'ad_cat_tax_img_advanced_settings' );

global $wpdb;

/**
 * Remove terms meta on uninstall/delete
 */
$wpdb->query(
	$wpdb->prepare(
		"DELETE FROM {$wpdb->termmeta} WHERE meta_key LIKE %s",
		'%tax_image_url_%'
	)
);
