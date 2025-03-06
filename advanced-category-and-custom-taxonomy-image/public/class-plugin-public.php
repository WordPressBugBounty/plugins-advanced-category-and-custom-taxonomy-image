<?php

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, other methods and
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Advanced_Category_And_Custom_Taxonomy_Image
 * @subpackage Advanced_Category_And_Custom_Taxonomy_Image/public
 * @author     Sajjad Hossain Sagor <sagorh672@gmail.com>
 */
class Advanced_Category_And_Custom_Taxonomy_Image_Public
{
	/**
	 * The ID of this plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    2.0.0
	 * @param    string    $plugin_name   The name of the plugin.
	 * @param    string    $version   The version of this plugin.
	 */
	public function __construct( $plugin_name, $version )
	{
		$this->plugin_name 	= $plugin_name;
		
		$this->version 		= $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    2.0.0
	 */
	public function enqueue_styles()
	{
		wp_enqueue_style( $this->plugin_name, ADVANCED_CATEGORY_AND_CUSTOM_TAXONOMY_IMAGE_PLUGIN_URL . 'public/css/public.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    2.0.0
	 */
	public function enqueue_scripts()
	{
		wp_enqueue_script( $this->plugin_name, ADVANCED_CATEGORY_AND_CUSTOM_TAXONOMY_IMAGE_PLUGIN_URL . 'public/js/public.js', array( 'jquery' ), $this->version, false );

		wp_localize_script( $this->plugin_name, 'ADVANCED_CATEGORY_AND_CUSTOM_TAXONOMY_IMAGE', array(
			'ajaxurl'	=> admin_url( 'admin-ajax.php' ),
		) );
	}

	/**
	 * Generate output for the shortcode.
	 *
	 * @since    2.0.0
	 */
	public function ad_tax_image_shortcode_callback( $atts )
	{
		$attr = shortcode_atts( array(
			'term_id' 			=> '',
			'return_img_tag' 	=> false,
			'class' 			=> '',
		), $atts );

		return get_taxonomy_image( intval( $attr['term_id'] ), filter_var( $attr['return_img_tag'], FILTER_VALIDATE_BOOLEAN ), explode( ' ', esc_attr( $attr['class'] ) ) );
	}
}

/**
 * register template tag function to show taxonomy image
 *
 * @return string|empty $device_image_url return the url or default message
 */
function get_taxonomy_image( $term_id = '', $return_img_tag = false, $class = array() )
{
	$detect 							= new \Detection\MobileDetect();

	$term_id 							= ! intval( $term_id ) ? get_queried_object()->term_id : intval( $term_id );

	// get all image field enabled taxonomies
	$enabled_taxonomies 				= Advanced_Category_And_Custom_Taxonomy_Image::get_option( 'enabled_taxonomies', 'ad_cat_tax_img_basic_settings' );

	// get all image field enabled devices
	$enabled_devices 					= Advanced_Category_And_Custom_Taxonomy_Image::get_option( 'enabled_devices', 'ad_cat_tax_img_advanced_settings' );

	// previous version db name was universal, so for compatibility we are checking if universal exists anymore...
	$device_image_url 					= Advanced_Category_And_Custom_Taxonomy_Image::get_any_device_image( $term_id );

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

	$classes 							= ! empty( $class ) && is_array( $class ) ? implode( ' ', $class ) : '';

	$result 							= filter_var( $return_img_tag, FILTER_VALIDATE_BOOLEAN ) ? "<img src='" . esc_url( $device_image_url ) . "' class='" . esc_attr( $classes ) . "'>" : esc_url( $device_image_url );
	
	return ! empty( $device_image_url ) ? $result : __( 'Please Upload Image First!', 'advanced-category-and-custom-taxonomy-image' );
}