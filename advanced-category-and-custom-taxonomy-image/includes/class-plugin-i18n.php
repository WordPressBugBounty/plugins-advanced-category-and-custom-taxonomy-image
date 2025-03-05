<?php

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      2.0.0
 * @package    Advanced_Category_And_Custom_Taxonomy_Image
 * @subpackage Advanced_Category_And_Custom_Taxonomy_Image/includes
 * @author     Sajjad Hossain Sagor <sagorh672@gmail.com>
 */
class Advanced_Category_And_Custom_Taxonomy_Image_i18n
{
	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    2.0.0
	 */
	public function load_plugin_textdomain()
	{
		load_plugin_textdomain(
			'advanced-category-and-custom-taxonomy-image',
			false,
			dirname( ADVANCED_CATEGORY_AND_CUSTOM_TAXONOMY_IMAGE_PLUGIN_BASENAME ) . '/languages/'
		);
	}
}
