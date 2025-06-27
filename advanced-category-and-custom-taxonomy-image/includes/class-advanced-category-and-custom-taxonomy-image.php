<?php
/**
 * This file contains the definition of the Advanced_Category_And_Custom_Taxonomy_Image class, which
 * is used to begin the plugin's functionality.
 *
 * @package       Advanced_Category_And_Custom_Taxonomy_Image
 * @subpackage    Advanced_Category_And_Custom_Taxonomy_Image/includes
 * @author        Sajjad Hossain Sagor <sagorh672@gmail.com>
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since    2.0.0
 */
class Advanced_Category_And_Custom_Taxonomy_Image {
	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since     2.0.0
	 * @access    protected
	 * @var       Advanced_Category_And_Custom_Taxonomy_Image_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since     2.0.0
	 * @access    protected
	 * @var       string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since     2.0.0
	 * @access    protected
	 * @var       string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since     2.0.0
	 * @access    public
	 */
	public function __construct() {
		$this->version     = defined( 'ADVANCED_CATEGORY_AND_CUSTOM_TAXONOMY_IMAGE_PLUGIN_VERSION' ) ? ADVANCED_CATEGORY_AND_CUSTOM_TAXONOMY_IMAGE_PLUGIN_VERSION : '1.0.0';
		$this->plugin_name = 'advanced-category-and-custom-taxonomy-image';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Advanced_Category_And_Custom_Taxonomy_Image_Loader. Orchestrates the hooks of the plugin.
	 * - Advanced_Category_And_Custom_Taxonomy_Image_i18n.   Defines internationalization functionality.
	 * - Sajjad_Dev_Settings_API.                            Provides an interface for interacting with the WordPress Settings API.
	 * - MobileDetect.                                       The Mobile Detect PHP library.
	 * - Advanced_Category_And_Custom_Taxonomy_Image_Admin.  Defines all hooks for the admin area.
	 * - Advanced_Category_And_Custom_Taxonomy_Image_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since     2.0.0
	 * @access    private
	 */
	private function load_dependencies() {
		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once ADVANCED_CATEGORY_AND_CUSTOM_TAXONOMY_IMAGE_PLUGIN_PATH . 'includes/class-advanced-category-and-custom-taxonomy-image-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once ADVANCED_CATEGORY_AND_CUSTOM_TAXONOMY_IMAGE_PLUGIN_PATH . 'includes/class-advanced-category-and-custom-taxonomy-image-i18n.php';

		/**
		 * The class responsible for defining an interface for interacting with the WordPress Settings API.
		 */
		require_once ADVANCED_CATEGORY_AND_CUSTOM_TAXONOMY_IMAGE_PLUGIN_PATH . 'includes/class-sajjad-dev-settings-api.php';

		/**
		 * The Mobile Detect PHP library.
		 */
		require_once ADVANCED_CATEGORY_AND_CUSTOM_TAXONOMY_IMAGE_PLUGIN_PATH . 'vendor/autoload.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once ADVANCED_CATEGORY_AND_CUSTOM_TAXONOMY_IMAGE_PLUGIN_PATH . 'admin/class-advanced-category-and-custom-taxonomy-image-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once ADVANCED_CATEGORY_AND_CUSTOM_TAXONOMY_IMAGE_PLUGIN_PATH . 'public/class-advanced-category-and-custom-taxonomy-image-public.php';

		$this->loader = new Advanced_Category_And_Custom_Taxonomy_Image_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Advanced_Category_And_Custom_Taxonomy_Image_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since     2.0.0
	 * @access    private
	 */
	private function set_locale() {
		$plugin_i18n = new Advanced_Category_And_Custom_Taxonomy_Image_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since     2.0.0
	 * @access    private
	 */
	private function define_admin_hooks() {
		$plugin_admin = new Advanced_Category_And_Custom_Taxonomy_Image_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		$this->loader->add_action( 'plugin_action_links_' . ADVANCED_CATEGORY_AND_CUSTOM_TAXONOMY_IMAGE_PLUGIN_BASENAME, $plugin_admin, 'add_plugin_action_links' );

		$this->loader->add_action( 'admin_menu', $plugin_admin, 'admin_menu' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'admin_init' );

		$this->loader->add_action( 'edit_term', $plugin_admin, 'save_img_url' );
		$this->loader->add_action( 'create_term', $plugin_admin, 'save_img_url' );

		$this->loader->add_filter( 'ad_tax_image_devices', $plugin_admin, 'devices', 10, 1 );

		// get all image field enabled taxonomies.
		$enabled_taxonomies = self::get_option( 'enabled_taxonomies', 'ad_cat_tax_img_basic_settings' );

		// check if any taxonomy enabled.
		if ( ! empty( $enabled_taxonomies ) ) {
			// iterate all enabled taxonomies.
			foreach ( $enabled_taxonomies as $enabled_taxonomy ) {
				// Add shortcode column to taxonomy list.
				$this->loader->add_filter( "manage_edit-{$enabled_taxonomy}_columns", $plugin_admin, 'template_tag_of_taxonomy', 10, 1 );
				$this->loader->add_filter( "manage_{$enabled_taxonomy}_custom_column", $plugin_admin, 'template_tag_content_of_taxonomy', 10, 3 );
				$this->loader->add_action( "{$enabled_taxonomy}_add_form_fields", $plugin_admin, 'add_form_fields' );
				$this->loader->add_action( "{$enabled_taxonomy}_edit_form_fields", $plugin_admin, 'edit_form_fields' );
			}
		}
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since     2.0.0
	 * @access    private
	 */
	private function define_public_hooks() {
		$plugin_public = new Advanced_Category_And_Custom_Taxonomy_Image_Public( $this->get_plugin_name(), $this->get_version() );

		add_shortcode( 'ad_tax_image', array( $plugin_public, 'ad_tax_image_shortcode_callback' ) );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since     2.0.0
	 * @access    public
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     2.0.0
	 * @access    public
	 * @return    string The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     2.0.0
	 * @access    public
	 * @return    Advanced_Category_And_Custom_Taxonomy_Image_Loader Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     2.0.0
	 * @access    public
	 * @return    string The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Returns default image which will be used for any devices
	 *
	 * @since     2.0.0
	 * @static
	 * @access    public
	 * @param     int|string $term_id          Term id to get the image.
	 * @return    string     $device_image_url Taxonomy image url.
	 */
	public static function get_any_device_image( $term_id = '' ) {
		if ( empty( $term_id ) && ! intval( $term_id ) ) {
			return '';
		}

		// previous version db name was universal, so for compatibility we are checking if universal exists anymore...
		$device_image_url = get_term_meta( $term_id, 'tax_image_url_universal', true );

		if ( empty( $device_image_url ) ) {
			$device_image_url = get_term_meta( $term_id, 'tax_image_url_any', true );
		}

		return $device_image_url;
	}

	/**
	 * Checks if taxonomy image is available for any device.
	 *
	 * @since     2.0.0
	 * @static
	 * @access    public
	 * @param     int|string $term_id Term id to get the image.
	 * @return    bool                Whether tax has image saved.
	 */
	public static function tax_image_available( $term_id = '' ) {
		if ( empty( $term_id ) && ! intval( $term_id ) ) {
			return false;
		}

		// get all image field enabled taxonomies.
		$enabled_taxonomies = self::get_option( 'enabled_taxonomies', 'ad_cat_tax_img_basic_settings' );

		// get all image field enabled devices.
		$enabled_devices = self::get_option( 'enabled_devices', 'ad_cat_tax_img_advanced_settings' );

		// check if any taxonomy enabled.
		if ( ! empty( $enabled_taxonomies ) ) {
			// previous version db name was universal, so for compatibility we are checking if universal exists anymore.
			$device_image_url = self::get_any_device_image( $term_id );

			// check if any device enabled.
			if ( ! empty( $enabled_devices ) ) {
				// registed custom image field for each enabled devices.
				foreach ( $enabled_devices as $enabled_device ) {
					if ( 'android' === $enabled_device ) {
						$device_image_url = get_term_meta( $term_id, 'tax_image_url_' . $enabled_device, true );

						if ( ! empty( $device_image_url ) ) {
							break; // android match found no need to check further.
						}
					} elseif ( 'iphone' === $enabled_device ) {
						$device_image_url = get_term_meta( $term_id, 'tax_image_url_' . $enabled_device, true );

						if ( ! empty( $device_image_url ) ) {
							break; // iOS match found no need to check further.
						}
					} elseif ( 'windowsph' === $enabled_device ) {
						$device_image_url = get_term_meta( $term_id, 'tax_image_url_' . $enabled_device, true );

						if ( ! empty( $device_image_url ) ) {
							break; // Windows Phone match found no need to check further.
						}
					} elseif ( 'mobile' === $enabled_device ) {
						$device_image_url = get_term_meta( $term_id, 'tax_image_url_' . $enabled_device, true );

						if ( ! empty( $device_image_url ) ) {
							break; // Mobile match found no need to check further.
						}
					} elseif ( 'tablet' === $enabled_device ) {
						$device_image_url = get_term_meta( $term_id, 'tax_image_url_' . $enabled_device, true );

						if ( ! empty( $device_image_url ) ) {
							break; // Tablet match found no need to check further.
						}
					} elseif ( 'desktop' === $enabled_device ) {
						$device_image_url = get_term_meta( $term_id, 'tax_image_url_' . $enabled_device, true );

						if ( ! empty( $device_image_url ) ) {
							break; // Desktop match found no need to check further.
						}
					}
				}
			}
		} else {
			return false;
		}

		return ! empty( $device_image_url ) ? true : false;
	}

	/**
	 * Retrieves the value of a specific settings field.
	 *
	 * This method fetches the value of a settings field from the WordPress options database.
	 * It retrieves the entire option group for the given section and then extracts the
	 * value for the specified field.
	 *
	 * @since     2.0.0
	 * @static
	 * @access    public
	 * @param     string $option        The name of the settings field.
	 * @param     string $section       The name of the section this field belongs to. This corresponds
	 *                                  to the option name used in `register_setting()`.
	 * @param     string $default_value Optional. The default value to return if the field's value
	 *                                  is not found in the database. Default is an empty string.
	 * @return    string|mixed          The value of the settings field, or the default value if not found.
	 */
	public static function get_option( $option, $section, $default_value = '' ) {
		$options = get_option( $section ); // Get all options for the section.

		// Check if the option exists within the section's options array.
		if ( isset( $options[ $option ] ) ) {
			return $options[ $option ]; // Return the option value.
		}

		return $default_value; // Return the default value if the option is not found.
	}
}
