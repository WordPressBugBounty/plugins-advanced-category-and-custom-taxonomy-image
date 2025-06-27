<?php
/**
 * This file contains the definition of the Advanced_Category_And_Custom_Taxonomy_Image_Admin class, which
 * is used to load the plugin's admin-specific functionality.
 *
 * @package       Advanced_Category_And_Custom_Taxonomy_Image
 * @subpackage    Advanced_Category_And_Custom_Taxonomy_Image/admin
 * @author        Sajjad Hossain Sagor <sagorh672@gmail.com>
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version and other methods.
 *
 * @since    2.0.0
 */
class Advanced_Category_And_Custom_Taxonomy_Image_Admin {
	/**
	 * The ID of this plugin.
	 *
	 * @since     2.0.0
	 * @access    private
	 * @var       string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since     2.0.0
	 * @access    private
	 * @var       string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * The plugin options api wrapper object.
	 *
	 * @since     2.0.0
	 * @access    private
	 * @var       array $settings_api Holds the plugin options api wrapper class object.
	 */
	private $settings_api;

	/**
	 * The list of availalbe devices to detect.
	 *
	 * @since     2.0.0
	 * @static
	 * @access    public
	 * @var       array $devices Holds device types list.
	 */
	private static $devices = array();

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since     2.0.0
	 * @access    public
	 * @param     string $plugin_name The name of this plugin.
	 * @param     string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name  = $plugin_name;
		$this->version      = $version;
		$this->settings_api = new Sajjad_Dev_Settings_API();
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since     2.0.0
	 * @access    public
	 */
	public function enqueue_styles() {
		global $pagenow;

		// Only show if in term add/edit page.
		if ( ! in_array( $pagenow, array( 'term.php', 'edit-tags.php' ), true ) ) {
			return;
		}

		wp_enqueue_style( $this->plugin_name, ADVANCED_CATEGORY_AND_CUSTOM_TAXONOMY_IMAGE_PLUGIN_URL . 'admin/css/admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since     2.0.0
	 * @access    public
	 */
	public function enqueue_scripts() {
		global $pagenow;

		// Only show if in term add/edit page.
		if ( ! in_array( $pagenow, array( 'term.php', 'edit-tags.php' ), true ) ) {
			return;
		}

		wp_enqueue_script( $this->plugin_name, ADVANCED_CATEGORY_AND_CUSTOM_TAXONOMY_IMAGE_PLUGIN_URL . 'admin/js/admin.js', array( 'jquery' ), $this->version, false );

		wp_localize_script(
			$this->plugin_name,
			'AdvancedCategoryAndCustomTaxonomyImage',
			array(
				'ajaxurl'         => admin_url( 'admin-ajax.php' ),
				'uploadTaxImgTxt' => __( 'Upload Taxonomy Image', 'advanced-category-and-custom-taxonomy-image' ),
				'uploadTxt'       => __( 'Upload', 'advanced-category-and-custom-taxonomy-image' ),
			)
		);
	}

	/**
	 * Adds a settings link to the plugin's action links on the plugin list table.
	 *
	 * @since     2.0.0
	 * @access    public
	 * @param     array $links The existing array of plugin action links.
	 * @return    array $links The updated array of plugin action links, including the settings link.
	 */
	public function add_plugin_action_links( $links ) {
		$links[] = sprintf( '<a href="%s">%s</a>', esc_url( admin_url( 'admin.php?page=advanced-category-and-custom-taxonomy-image' ) ), __( 'Settings', 'advanced-category-and-custom-taxonomy-image' ) );

		return $links;
	}

	/**
	 * Adds the plugin settings page to the WordPress dashboard menu.
	 *
	 * @since     2.0.0
	 * @access    public
	 */
	public function admin_menu() {
		add_menu_page(
			__( 'Advanced Category and Custom Taxonomy Image', 'advanced-category-and-custom-taxonomy-image' ),
			__( 'Advanced Category and Custom Taxonomy Image', 'advanced-category-and-custom-taxonomy-image' ),
			'manage_options',
			'advanced-category-and-custom-taxonomy-image',
			array( $this, 'menu_page' ),
			'dashicons-admin-tools'
		);
	}

	/**
	 * Renders the plugin menu page content.
	 *
	 * @since     2.0.0
	 * @access    public
	 */
	public function menu_page() {
		$this->settings_api->show_forms();
	}

	/**
	 * Initializes admin-specific functionality.
	 *
	 * This function is hooked to the 'admin_init' action and is used to perform
	 * various administrative tasks, such as registering settings, enqueuing scripts,
	 * or adding admin notices.
	 *
	 * @since     2.0.0
	 * @access    public
	 */
	public function admin_init() {
		// set the settings.
		$this->settings_api->set_sections( $this->get_settings_sections() );

		$this->settings_api->set_fields( $this->get_settings_fields() );

		// initialize settings.
		$this->settings_api->admin_init();
	}

	/**
	 * Returns the settings sections for the plugin settings page.
	 *
	 * @since     2.0.0
	 * @access    public
	 * @return    array An array of settings sections, where each section is an array
	 *                  with 'id' and 'title' keys.
	 */
	public function get_settings_sections() {
		$settings_sections = array(
			array(
				'id'    => 'ad_cat_tax_img_basic_settings',
				'title' => __( 'General', 'advanced-category-and-custom-taxonomy-image' ),
			),
			array(
				'id'    => 'ad_cat_tax_img_advanced_settings',
				'title' => __( 'Advanced', 'advanced-category-and-custom-taxonomy-image' ),
			),
		);

		/**
		 * Filters the plugin settings sections.
		 *
		 * This filter allows you to modify the plugin settings sections.
		 * You can use this filter to add/remove/edit any settings sections.
		 *
		 * @since     2.0.3
		 * @param     array $settings_sections Default settings sections.
		 * @return    array $settings_sections Modified settings sections.
		 */
		return apply_filters( 'ad_cat_tax_img_settings_sections', $settings_sections );
	}

	/**
	 * Returns all the settings fields for the plugin settings page.
	 *
	 * @since     2.0.0
	 * @access    public
	 * @return    array An array of settings fields, organized by section ID.  Each
	 *                  section ID is a key in the array, and the value is an array
	 *                  of settings fields for that section. Each settings field is
	 *                  an array with 'name', 'label', 'type', 'desc', and other keys
	 *                  depending on the field type.
	 */
	public function get_settings_fields() {
		/**
		 * Filters the list of available devices.
		 *
		 * This filter is applied to the array of devices used for targeting images,
		 * allowing for modification of the device list.
		 *
		 * @since    2.0.0
		 * @param    array $devices An array of devices.
		 */
		self::$devices = apply_filters( 'ad_tax_image_devices', array() );

		$settings_fields = array(
			'ad_cat_tax_img_basic_settings'    => array(
				array(
					'name'    => 'enabled_taxonomies',
					'label'   => __( 'Select Taxonomies', 'advanced-category-and-custom-taxonomy-image' ),
					'desc'    => __( 'Please Select Taxonomies You Want To Include Custom Image', 'advanced-category-and-custom-taxonomy-image' ),
					'type'    => 'multicheck',
					'options' => $this->get_all_taxonomies(),
				),
			),
			'ad_cat_tax_img_advanced_settings' => array(
				array(
					'name'    => 'enabled_devices',
					'label'   => __( 'Enable Device Filter', 'advanced-category-and-custom-taxonomy-image' ),
					'desc'    => __( 'Please Select Device Type You Want To Use Enable For', 'advanced-category-and-custom-taxonomy-image' ),
					'type'    => 'multicheck',
					'options' => self::$devices,
				),
			),
		);

		/**
		 * Filters the plugin settings fields.
		 *
		 * This filter allows you to modify the plugin settings fields.
		 * You can use this filter to add/remove/edit any settings field.
		 *
		 * @since     2.0.3
		 * @param     array $settings_fields Default settings fields.
		 * @return    array $settings_fields Modified settings fields.
		 */
		return apply_filters( 'ad_cat_tax_img_settings_fields', $settings_fields );
	}

	/**
	 * Retrieves all registered taxonomies.
	 *
	 * This function returns an array containing all registered taxonomies in WordPress,
	 * excluding built-in internal taxonomies that are not intended for general use.
	 *
	 * @since     2.0.0
	 * @access    public
	 * @return    array An array of taxonomy names.
	 */
	public function get_all_taxonomies() {
		$args            = array();
		$output          = 'objects';
		$taxonomies      = get_taxonomies( $args, $output );
		$name_value_pair = array();

		// exclude some wp & woocommerce private taxonomies.
		$disabled_taxonomies = array(
			'nav_menu',
			'link_category',
			'post_format',
			'product_visibility',
			'product_shipping_class',
			'action-group',
			'product_type',
			'wp_theme',
			'wp_template_part_area',
		);

		if ( $taxonomies ) {
			foreach ( $taxonomies as $taxonomy ) {
				if ( in_array( $taxonomy->name, $disabled_taxonomies, true ) ) {
					continue;
				}

				$name_value_pair[ $taxonomy->name ] = ucwords( $taxonomy->label );
			}
		}

		return $name_value_pair;
	}

	/**
	 * Saves taxonomy image URLs.
	 *
	 * This function saves the taxonomy image URLs submitted via the form to the term meta.
	 * It iterates through the submitted URLs, sanitizes them, and updates the term meta.
	 *
	 * @since     2.0.0
	 * @access    public
	 * @param     int $term_id The ID of the taxonomy term being saved.
	 */
	public function save_img_url( $term_id ) {
		// Verify nonce.
		if ( ! isset( $_POST['ad_tax_image_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ad_tax_image_nonce'] ) ), 'ad_tax_image_nonce' ) ) {
			return;
		}

		if ( isset( $_POST['tax_image_url'] ) && ! empty( $_POST['tax_image_url'] ) && is_array( $_POST['tax_image_url'] ) ) {
			$images = array_map( 'sanitize_text_field', wp_unslash( $_POST['tax_image_url'] ) );

			foreach ( $images as $name => $value ) {
				update_term_meta( $term_id, $name, sanitize_url( $value ) );
			}
		}
	}

	/**
	 * Adds a shortcode column to the taxonomy list table.
	 *
	 * This function adds a new column labeled "Taxonomy Image Template Tag" to the taxonomy
	 * list table in the WordPress admin area. This column will display the template tag
	 * or shortcode that can be used to display the associated taxonomy image.
	 *
	 * @since     2.0.0
	 * @access    public
	 * @param     array $columns An associative array of columns to be displayed in the taxonomy list table.
	 * @return    array $columns An associative array of columns with the new "Taxonomy Image Template Tag" column added.
	 */
	public function template_tag_of_taxonomy( $columns ) {
		$columns['taxonomy_image_template_tag'] = __( 'Taxonomy Image Template Tag', 'advanced-category-and-custom-taxonomy-image' );

		return $columns;
	}

	/**
	 * Adds content to the shortcode column in the taxonomy list table.
	 *
	 * This function populates the "Taxonomy Image Template Tag" column with the appropriate
	 * template tag or shortcode for displaying the taxonomy image, if available.
	 *
	 * @since     2.0.0
	 * @access    public
	 * @param     string $content     The current content of the table cell.
	 * @param     string $column_name The name of the current column.
	 * @param     int    $term_id     The ID of the current taxonomy term.
	 * @return    string              The content to be displayed in the table cell.
	 */
	public function template_tag_content_of_taxonomy( $content, $column_name, $term_id ) {
		// check if column is our custom column 'taxonomy_image_template_tag'.
		if ( 'taxonomy_image_template_tag' === $column_name ) {
			return Advanced_Category_And_Custom_Taxonomy_Image::tax_image_available( $term_id ) ? '<code>echo get_taxonomy_image( ' . intval( $term_id ) . ', true, "your custom class list separated by space" );<br><br>echo do_shortcode( \'[ad_tax_image term_id="' . intval( $term_id ) . '" return_img_tag="true" class="your custom class list separated by space"]\' );</code>' : '';
		}

		return '';
	}

	/**
	 * Registers form fields for all enabled taxonomies.
	 *
	 * This function iterates through all taxonomies that have been enabled for custom image fields
	 * and registers the necessary form fields (add/edit) to allow users to upload or select images
	 * for those taxonomies.
	 *
	 * @since     2.0.0
	 * @access    public
	 */
	public function add_form_fields() {
		/**
		 * Filters the list of available devices.
		 *
		 * This filter is applied to the array of devices used for targeting images,
		 * allowing for modification of the device list.
		 *
		 * @since    2.0.0
		 * @param    array $devices An array of devices.
		 */
		self::$devices = apply_filters( 'ad_tax_image_devices', array() );
		$label         = __( 'Choose File', 'advanced-category-and-custom-taxonomy-image' );

		// get all image field enabled devices.
		$enabled_devices = Advanced_Category_And_Custom_Taxonomy_Image::get_option( 'enabled_devices', 'ad_cat_tax_img_advanced_settings' );

		// check if any device enabled.
		if ( ! empty( $enabled_devices ) ) {
			$html  = '<div class="form-field"><label for="tax_image_url_any">' . __( 'Taxonomy Image For Any Device', 'advanced-category-and-custom-taxonomy-image' ) . '</label>';
			$html .= '<input type="text" class="tax_image_upload advanced-category-and-custom-taxonomy-image-url" id="tax_image_url_any" name="tax_image_url[tax_image_url_any]" value=""/>';
			$html .= '<input type="button" class="button advanced-category-and-custom-taxonomy-image-upload-btn" value="' . esc_attr( $label ) . '" />';
			$html .= '<p class="description">' . __( 'Choose Image To Show For Any Device', 'advanced-category-and-custom-taxonomy-image' ) . '</p>';
			$html .= '</div>';

			// registed custom image field for each enabled devices.
			foreach ( $enabled_devices as $enabled_device ) {
				$html .= '<div class="form-field"> <label for="tax_image_url_' . esc_attr( $enabled_device ) . '">' . __( 'Taxonomy Image For ', 'advanced-category-and-custom-taxonomy-image' ) . esc_attr( self::$devices[ $enabled_device ] ) . '</label>';
				$html .= '<input type="text" class="tax_image_upload advanced-category-and-custom-taxonomy-image-url" id="tax_image_url_' . esc_attr( $enabled_device ) . '" name="tax_image_url[tax_image_url_' . esc_attr( $enabled_device ) . ']" value="" />';
				$html .= '<input type="button" class="button advanced-category-and-custom-taxonomy-image-upload-btn" value="' . esc_attr( $label ) . '" />';
				$html .= '<p class="description">' . __( 'Choose Image To Show For ', 'advanced-category-and-custom-taxonomy-image' ) . esc_attr( self::$devices[ $enabled_device ] ) . '</p>';
				$html .= '</div>';
			}

			echo wp_kses( $html, $this->settings_api::$allowed_html_tags );
		} else {
			$html  = '<div class="form-field"><label for="tax_image_url_any">' . __( 'Taxonomy Image For Any Device', 'advanced-category-and-custom-taxonomy-image' ) . '</label>';
			$html .= '<input type="text" class="tax_image_upload advanced-category-and-custom-taxonomy-image-url" id="tax_image_url_any" name="tax_image_url[tax_image_url_any]" value=""/>';
			$html .= '<input type="button" class="button advanced-category-and-custom-taxonomy-image-upload-btn" value="' . esc_attr( $label ) . '" />';
			$html .= '<p class="description">' . __( 'Choose Image To Show For Any Device', 'advanced-category-and-custom-taxonomy-image' ) . '</p>';
			$html .= '</div>';

			echo wp_kses( $html, $this->settings_api::$allowed_html_tags );
		}

		wp_nonce_field( 'ad_tax_image_nonce', 'ad_tax_image_nonce', false );
	}

	/**
	 * Registers edit form fields for all enabled taxonomies.
	 *
	 * This function iterates through all taxonomies that have been enabled for custom image fields
	 * and registers the necessary edit form fields to allow users to modify or remove images
	 * associated with those taxonomies.
	 *
	 * @since     2.0.0
	 * @access    public
	 * @param     string $taxonomy The name of the taxonomy being edited.
	 */
	public function edit_form_fields( $taxonomy ) {
		/**
		 * Filters the list of available devices.
		 *
		 * This filter is applied to the array of devices used for targeting images,
		 * allowing for modification of the device list.
		 *
		 * @since    2.0.0
		 * @param    array $devices An array of devices.
		 */
		self::$devices = apply_filters( 'ad_tax_image_devices', array() );
		$label         = __( 'Choose File', 'advanced-category-and-custom-taxonomy-image' );

		// get all image field enabled devices.
		$enabled_devices = Advanced_Category_And_Custom_Taxonomy_Image::get_option( 'enabled_devices', 'ad_cat_tax_img_advanced_settings' );

		// check if any device enabled.
		if ( ! empty( $enabled_devices ) ) {
			// previous version db name was universal, so for compatibility we are checking if universal exists anymore.
			$any_image_url = Advanced_Category_And_Custom_Taxonomy_Image::get_any_device_image( $taxonomy->term_id );

			$html  = '<tr class="form-field">';
			$html .= '<th scope="row" valign="top"><label for="tax_image_url_any">' . __( 'Taxonomy Image For Any Device', 'advanced-category-and-custom-taxonomy-image' ) . '</label></th><td>';
			$html .= empty( $any_image_url ) ? '' : '<img src="' . esc_url( $any_image_url ) . '" width="150"/><br><br>';
			$html .= '<input type="text" class="tax_image_upload advanced-category-and-custom-taxonomy-image-url" id="tax_image_url_any" name="tax_image_url[tax_image_url_any]" value="' . esc_url( $any_image_url ) . '"/>';
			$html .= '<input type="button" class="button advanced-category-and-custom-taxonomy-image-upload-btn" value="' . esc_attr( $label ) . '" />';
			$html .= '<p class="description">' . __( 'Choose Image To Show For Any Device', 'advanced-category-and-custom-taxonomy-image' ) . '</p></td></tr>';

			// registed custom image field for each enabled devices.
			foreach ( $enabled_devices as $enabled_device ) {
				$device_image_url = get_term_meta( $taxonomy->term_id, 'tax_image_url_' . $enabled_device, true );

				$html .= '<tr class="form-field">';
				$html .= '<th scope="row" valign="top"><label for="tax_image_url_' . esc_attr( $enabled_device ) . '">' . __( 'Taxonomy Image For ', 'advanced-category-and-custom-taxonomy-image' ) . esc_attr( self::$devices[ $enabled_device ] ) . '</label></th><td>';
				$html .= empty( $device_image_url ) ? '' : '<img src="' . esc_url( $device_image_url ) . '" width="150"/><br><br>';
				$html .= '<input type="text" class="tax_image_upload advanced-category-and-custom-taxonomy-image-url" id="tax_image_url_' . esc_attr( $enabled_device ) . '" name="tax_image_url[tax_image_url_' . esc_attr( $enabled_device ) . ']" value="' . esc_url( $device_image_url ) . '"/>';
				$html .= '<input type="button" class="button advanced-category-and-custom-taxonomy-image-upload-btn" value="' . esc_attr( $label ) . '" />';
				$html .= '<p class="description">' . __( 'Choose Image To Show For ', 'advanced-category-and-custom-taxonomy-image' ) . esc_attr( self::$devices[ $enabled_device ] ) . '</p>';
			}

			echo wp_kses( $html, $this->settings_api::$allowed_html_tags );
		} else {
			// previous version db name was universal, so for compatibility we are checking if universal exists anymore.
			$any_image_url = Advanced_Category_And_Custom_Taxonomy_Image::get_any_device_image( $taxonomy->term_id );

			$html  = '<tr class="form-field">';
			$html .= '<th scope="row" valign="top"><label for="tax_image_url_any">' . __( 'Taxonomy Image For Any Device', 'advanced-category-and-custom-taxonomy-image' ) . '</label></th><td>';
			$html .= empty( $any_image_url ) ? '' : '<img src="' . esc_url( $any_image_url ) . '" width="150"/><br><br>';
			$html .= '<input type="text" class="tax_image_upload advanced-category-and-custom-taxonomy-image-url" id="tax_image_url_any" name="tax_image_url[tax_image_url_any]" value="' . esc_url( $any_image_url ) . '"/>';
			$html .= '<input type="button" class="button advanced-category-and-custom-taxonomy-image-upload-btn" value="' . esc_attr( $label ) . '" />';
			$html .= '<p class="description">' . __( 'Choose Image To Show For Any Device', 'advanced-category-and-custom-taxonomy-image' ) . '</p></td></tr>';

			echo wp_kses( $html, $this->settings_api::$allowed_html_tags );
		}

		wp_nonce_field( 'ad_tax_image_nonce', 'ad_tax_image_nonce', false );
	}

	/**
	 * Filters the list of default devices.
	 *
	 * This filter allows you to modify the list of default devices that are
	 * available for targeting images.  You can add, remove, or change the order
	 * of devices in the list.
	 *
	 * @since     2.0.0
	 * @access    public
	 * @param     array $devices The list of default devices.
	 * @return    array $devices The modified list of devices.
	 */
	public function devices( $devices ) {
		$new_devices = array(
			'android'   => __( 'Android', 'advanced-category-and-custom-taxonomy-image' ),
			'ios'       => __( 'iOS (Mac | iPhone | iPad | iPod)', 'advanced-category-and-custom-taxonomy-image' ),
			'windowsph' => __( 'Windows Phone', 'advanced-category-and-custom-taxonomy-image' ),
			'mobile'    => __( 'Mobile (Any)', 'advanced-category-and-custom-taxonomy-image' ),
			'tablet'    => __( 'Tablet', 'advanced-category-and-custom-taxonomy-image' ),
			'desktop'   => __( 'Desktop', 'advanced-category-and-custom-taxonomy-image' ),
		);

		return array_merge( $new_devices, $devices );
	}
}
