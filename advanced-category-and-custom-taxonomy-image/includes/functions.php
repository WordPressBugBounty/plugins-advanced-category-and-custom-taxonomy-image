<?php

/**
 * Retrieves the URL or HTML image tag for a taxonomy image.
 *
 * This function retrieves the image URL associated with a given taxonomy term ID.
 * It can optionally return an HTML <img> tag with specified classes.
 *
 * @param  int|string   $term_id        The ID of the taxonomy term. Defaults to ''.
 * @param  bool         $return_img_tag Optional. Whether to return an HTML <img> tag. Defaults to false.
 * @param  array        $class          Optional. An array of CSS classes to add to the <img> tag. Defaults to an empty array.
 * @return string|empty 				The image URL or HTML <img> tag, or an empty string if no image is found or term ID is invalid.
 */
function get_taxonomy_image( $term_id = '', $return_img_tag = false, $class = array() )
{
	$detect 							= new \Detection\MobileDetect();

	$term_id 							= ! intval( $term_id ) ? get_queried_object()->term_id : intval( $term_id );

	// get all image field enabled taxonomies
	$enabled_taxonomies 				= Advanced_Category_And_Custom_Taxonomy_Image::get_option( 'enabled_taxonomies', 'ad_cat_tax_img_basic_settings' );

	// get all image field enabled devices
	$enabled_devices 					= Advanced_Category_And_Custom_Taxonomy_Image::get_option( 'enabled_devices', 'ad_cat_tax_img_advanced_settings' );

	$device_image_url					= ''; // Set default image url to empty

	// previous version db name was universal, so for compatibility we are checking if universal exists anymore...
	$any_device_image_url 				= Advanced_Category_And_Custom_Taxonomy_Image::get_any_device_image( $term_id );

	//check if any taxonomy enabled
	if ( ! empty( $enabled_taxonomies ) )
	{
		//check if any device enabled
		if ( ! empty( $enabled_devices ) )
		{
			// registed custom image field for each enabled devices
			foreach ( $enabled_devices as $enabled_device )
			{
				if( $enabled_device == 'android' && $detect->isAndroidOS() )
				{
					$device_image_url 	= get_term_meta( $term_id, 'tax_image_url_' . $enabled_device, true );

					break; //android match found no need to check further
				}
				else if( $enabled_device == 'iphone' && $detect->isiOS() )
				{
					$device_image_url 	= get_term_meta( $term_id, 'tax_image_url_' . $enabled_device, true );

					break; //iOS match found no need to check further
				}
				else if( $enabled_device == 'windowsph' && $detect->version( 'Windows Phone' ) )
				{
					$device_image_url 	= get_term_meta( $term_id, 'tax_image_url_' . $enabled_device, true );

					break; //Windows Phone match found no need to check further
				}
				else if( $enabled_device == 'mobile' && $detect->isMobile() )
				{
					$device_image_url 	= get_term_meta( $term_id, 'tax_image_url_' . $enabled_device, true );

					break; //Any Mobile match found no need to check further
				}
				else if( $enabled_device == 'tablet' && $detect->isTablet() )
				{
					$device_image_url 	= get_term_meta( $term_id, 'tax_image_url_' . $enabled_device, true );

					break; //Any Mobile match found no need to check further
				}
				else if( $enabled_device == 'desktop' )
				{
					$device_image_url 	= get_term_meta( $term_id, 'tax_image_url_' . $enabled_device, true );

					break; //Dektop match found no need to check further
				}
			}
		}
	}
	else
	{
		$device_image_url 				= __( 'Please Enable Taxonomies First!', 'advanced-category-and-custom-taxonomy-image' );
	}

	if ( empty( $device_image_url ) && ! empty( $any_device_image_url ) )
	{
		// if no image found for enabled devices, opt for default any device if available
		$device_image_url 				= $any_device_image_url;
	}

	$classes 							= ! empty( $class ) && is_array( $class ) ? implode( ' ', $class ) : '';

	$result 							= filter_var( $return_img_tag, FILTER_VALIDATE_BOOLEAN ) ? "<img src='" . esc_url( $device_image_url ) . "' class='" . esc_attr( $classes ) . "'>" : esc_url( $device_image_url );
	
	return ! empty( $device_image_url ) ? $result : __( 'Please Upload Image First!', 'advanced-category-and-custom-taxonomy-image' );
}
