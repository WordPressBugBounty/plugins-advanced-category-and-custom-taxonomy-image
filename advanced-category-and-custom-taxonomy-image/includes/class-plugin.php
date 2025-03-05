<?php

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      2.0.0
 * @package    Advanced_Category_And_Custom_Taxonomy_Image
 * @subpackage Advanced_Category_And_Custom_Taxonomy_Image/includes
 * @author     Sajjad Hossain Sagor <sagorh672@gmail.com>
 */
class Advanced_Category_And_Custom_Taxonomy_Image
{
	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    2.0.0
	 * @access   protected
	 * @var      Advanced_Category_And_Custom_Taxonomy_Image_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    2.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    2.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    2.0.0
	 */
	public function __construct()
	{
		if ( defined( 'ADVANCED_CATEGORY_AND_CUSTOM_TAXONOMY_IMAGE_VERSION' ) )
		{
			$this->version = ADVANCED_CATEGORY_AND_CUSTOM_TAXONOMY_IMAGE_VERSION;
		}
		else
		{
			$this->version = '1.0.0';
		}
		
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
	 * - Advanced_Category_And_Custom_Taxonomy_Image_i18n. Defines internationalization functionality.
	 * - Advanced_Category_And_Custom_Taxonomy_Image_Admin. Defines all hooks for the admin area.
	 * - Advanced_Category_And_Custom_Taxonomy_Image_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    2.0.0
	 * @access   private
	 */
	private function load_dependencies()
	{
		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once ADVANCED_CATEGORY_AND_CUSTOM_TAXONOMY_IMAGE_PLUGIN_PATH . 'includes/class-plugin-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once ADVANCED_CATEGORY_AND_CUSTOM_TAXONOMY_IMAGE_PLUGIN_PATH . 'includes/class-plugin-i18n.php';

		/**
		 * The class responsible for defining options api wrapper
		 */
		require_once ADVANCED_CATEGORY_AND_CUSTOM_TAXONOMY_IMAGE_PLUGIN_PATH . 'includes/class-plugin-settings-api.php';

		/**
		 * The Mobile Detect PHP library
		 */
		require_once ADVANCED_CATEGORY_AND_CUSTOM_TAXONOMY_IMAGE_PLUGIN_PATH . 'vendor/autoload.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once ADVANCED_CATEGORY_AND_CUSTOM_TAXONOMY_IMAGE_PLUGIN_PATH . 'admin/class-plugin-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once ADVANCED_CATEGORY_AND_CUSTOM_TAXONOMY_IMAGE_PLUGIN_PATH . 'public/class-plugin-public.php';

		$this->loader = new Advanced_Category_And_Custom_Taxonomy_Image_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Advanced_Category_And_Custom_Taxonomy_Image_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    2.0.0
	 * @access   private
	 */
	private function set_locale()
	{
		$plugin_i18n = new Advanced_Category_And_Custom_Taxonomy_Image_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 */
	private function define_admin_hooks()
	{
		$plugin_admin = new Advanced_Category_And_Custom_Taxonomy_Image_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		$this->loader->add_action( 'plugin_action_links_' . ADVANCED_CATEGORY_AND_CUSTOM_TAXONOMY_IMAGE_PLUGIN_BASENAME, $plugin_admin, 'add_plugin_action_links' );

		$this->loader->add_action( 'admin_init', $plugin_admin, 'admin_init' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'admin_menu' );
		
		$this->loader->add_action( 'edit_term', $plugin_admin, 'save_img_url' );
		$this->loader->add_action( 'create_term', $plugin_admin, 'save_img_url' );

		// get all image field enabled taxonomies
		$enabled_taxonomies 	= self::get_option( 'enabled_taxonomies', 'ad_cat_tax_img_basic_settings' );

		//check if any taxonomy enabled
		if ( ! empty( $enabled_taxonomies ) )
		{
			//iterate all enabled taxonomies
			foreach ( $enabled_taxonomies as $enabled_taxonomy )
			{
				// Add shortcode column to taxonomy list
				$this->loader->add_filter( "manage_edit-{$enabled_taxonomy}_columns", $plugin_admin, 'template_tag_of_taxonomy' );
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
	 * @since    2.0.0
	 * @access   private
	 */
	private function define_public_hooks()
	{
		$plugin_public = new Advanced_Category_And_Custom_Taxonomy_Image_Public( $this->get_plugin_name(), $this->get_version() );

		// $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		// $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		add_shortcode( 'ad_tax_image', array( $plugin_public, 'ad_tax_image_shortcode_callback' ) );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    2.0.0
	 */
	public function run()
	{
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     2.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name()
	{
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     2.0.0
	 * @return    Advanced_Category_And_Custom_Taxonomy_Image_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader()
	{
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     2.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version()
	{
		return $this->version;
	}

	/**
	 * Returns option value
	 *
	 * @return string|array option value
	 */
	public static function get_option( $option, $section, $default = '' )
	{
		$options = get_option( $section );

		if ( isset( $options[$option] ) )
		{
			return $options[$option];
		}

		return $default;
	}

	/**
	 * Returns default image which will be used for any devices
	 *
	 * @return string|empty $device_image_url return the url or empty
	 */
	public static function get_any_device_image( $term_id = '' )
	{
		if ( empty( $term_id ) && ! intval( $term_id ) ) return '';

		// previous version db name was universal, so for compatibility we are checking if universal exists anymore...
		$device_image_url 				= get_term_meta( $term_id, 'tax_image_url_universal', true );

		if( empty( $device_image_url ) )
		{
			$device_image_url 			= get_term_meta( $term_id, 'tax_image_url_any', true );
		}

		return $device_image_url;
	}

	/**
	 * checks if taxonomy image is available for any device
	 *
	 * @return string|empty $device_image_url return the url or empty
	 */
	public static function tax_image_available( $term_id = '' )
	{
		if ( empty( $term_id ) && ! intval( $term_id ) ) return false;

		// get all image field enabled taxonomies
		$enabled_taxonomies 				= self::get_option( 'enabled_taxonomies', 'ad_cat_tax_img_basic_settings' );

		// get all image field enabled devices
		$enabled_devices 					= self::get_option( 'enabled_devices', 'ad_cat_tax_img_advanced_settings' );

		//check if any taxonomy enabled
		if ( ! empty( $enabled_taxonomies ) )
		{
			// previous version db name was universal, so for compatibility we are checking if universal exists anymore...
			$device_image_url 				= self::get_any_device_image( $term_id );
			
			//check if any device enabled
			if ( ! empty( $enabled_devices ) )
			{
				// registed custom image field for each enabled devices
				foreach ( $enabled_devices as $enabled_device )
				{
					if( $enabled_device == 'android' )
					{
						$device_image_url 	= get_term_meta( $term_id, 'tax_image_url_' . $enabled_device, true );

						break; //android match found no need to check further
					}
					else if( $enabled_device == 'iphone' )
					{
						$device_image_url 	= get_term_meta( $term_id, 'tax_image_url_' . $enabled_device, true );

						break; //iOS match found no need to check further
					}
					else if( $enabled_device == 'windowsph' )
					{
						$device_image_url 	= get_term_meta( $term_id, 'tax_image_url_' . $enabled_device, true );

						break; //Windows Phone match found no need to check further
					}
					else if( $enabled_device == 'mobile' )
					{
						$device_image_url 	= get_term_meta( $term_id, 'tax_image_url_' . $enabled_device, true );

						break; //Any Mobile match found no need to check further
					}
					else if( $enabled_device == 'tablet' )
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
			return false;
		}
		
		return ! empty( $device_image_url ) ? true : false;
	}

	/**
	 * register template tag function to show taxonomy image
	 *
	 * @return string|empty $device_image_url return the url or default message
	 */
	public static function get_taxonomy_image( $term_id = '', $return_img_tag = false, $class = array() )
	{
		$detect 				= new \Detection\MobileDetect();

		$term_id 				= ! intval( $term_id ) ? get_queried_object()->term_id : intval( $term_id );

		// get all image field enabled taxonomies
		$enabled_taxonomies 	= self::get_option( 'enabled_taxonomies', 'ad_cat_tax_img_basic_settings' );

		// get all image field enabled devices
		$enabled_devices 		= self::get_option( 'enabled_devices', 'ad_cat_tax_img_advanced_settings' );

		// previous version db name was universal, so for compatibility we are checking if universal exists anymore...
		$device_image_url 		= self::get_any_device_image( $term_id );

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

		$classes 							= ! empty( $class ) ? implode( ' ', $class ) : '';

		$result 							= filter_var( $return_img_tag, FILTER_VALIDATE_BOOLEAN ) ? "<img src='" . esc_url( $device_image_url ) . "' class='" . esc_attr( $classes ) . "'>" : esc_url( $device_image_url );
		
		return ! empty( $device_image_url ) ? $result : __( 'Please Upload Image First!', 'advanced-category-and-custom-taxonomy-image' );
	}
}
