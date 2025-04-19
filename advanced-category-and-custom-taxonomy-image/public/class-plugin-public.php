<?php

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version and other methods.
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
	 * @var      string    $version    		The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    2.0.0
	 * @access   public
	 * @param    string    $plugin_name    	The name of the plugin.
	 * @param    string    $version    		The version of this plugin.
	 */
	public function __construct( $plugin_name, $version )
	{
		$this->plugin_name 	= $plugin_name;
		
		$this->version 		= $version;
	}

	/**
	 * Generate output for the taxonomy image shortcode.
	 *
	 * This function handles the 'ad_tax_image' shortcode, retrieving and displaying a taxonomy image
	 * based on the provided attributes.
	 *
	 * @access   public
	 * @since    2.0.0
	 * @param    array    $atts    Shortcode attributes.
	 * - 'term_id': (string|int, optional) The ID of the taxonomy term to retrieve the image for. Defaults to ''.
	 * - 'return_img_tag': (string|bool, optional) Whether to return the image as an <img> tag. Defaults to false.
	 * - 'class': (string, optional) Custom CSS classes to add to the image tag. Defaults to ''.
	 * @return   string   		   The taxonomy image or an empty string if no image is found or term_id is invalid.
	 */
	public function ad_tax_image_shortcode_callback( $atts )
	{
		 // Merge provided attributes with default values
		$attr = shortcode_atts( array(
			'term_id' 			=> '',
			'return_img_tag' 	=> false,
			'class' 			=> '',
		), $atts );

		// Retrieve and return the taxonomy image
		return get_taxonomy_image(
			intval( $attr['term_id'] ), // Convert term_id to an integer
			filter_var( $attr['return_img_tag'], FILTER_VALIDATE_BOOLEAN ), // Validate and convert return_img_tag to boolean
			explode( ' ', esc_attr( $attr['class'] ) ) // Sanitize and explode class attribute into an array
		);
	}
}
