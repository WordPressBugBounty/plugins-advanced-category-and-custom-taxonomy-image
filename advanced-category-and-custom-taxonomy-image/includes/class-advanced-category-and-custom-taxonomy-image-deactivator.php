<?php
/**
 * This file contains the definition of the Advanced_Category_And_Custom_Taxonomy_Image_Deactivator class, which
 * is used during plugin deactivation.
 *
 * @package       Advanced_Category_And_Custom_Taxonomy_Image
 * @subpackage    Advanced_Category_And_Custom_Taxonomy_Image/includes
 * @author        Sajjad Hossain Sagor <sagorh672@gmail.com>
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin deactivation.
 *
 * @since    2.0.0
 */
class Advanced_Category_And_Custom_Taxonomy_Image_Deactivator {
	/**
	 * Deactivation hook.
	 *
	 * This function is called when the plugin is deactivated. It can be used to
	 * perform tasks such as cleaning up temporary data, unscheduling cron jobs,
	 * or removing options.
	 *
	 * @since     2.0.0
	 * @access    public
	 */
	public static function on_deactivate() {}
}
