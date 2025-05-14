<?php
/**
 * This file contains the definition of the Advanced_Category_And_Custom_Taxonomy_Image_I18n class, which
 * is used to load the plugin's internationalization.
 *
 * @package       Advanced_Category_And_Custom_Taxonomy_Image
 * @subpackage    Advanced_Category_And_Custom_Taxonomy_Image/includes
 * @author        Sajjad Hossain Sagor <sagorh672@gmail.com>
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since    2.0.0
 */
class Advanced_Category_And_Custom_Taxonomy_Image_I18n {
	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since     2.0.0
	 * @access    public
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain(
			'advanced-category-and-custom-taxonomy-image',
			false,
			dirname( ADVANCED_CATEGORY_AND_CUSTOM_TAXONOMY_IMAGE_PLUGIN_BASENAME ) . '/languages/'
		);
	}
}
