<?php
/**
 * WordPress Book List CustomFields Extension
 *
 * @package     WordPress Book List CustomFields Extension
 * @author      Jake Evans
 * @copyright   2018 Jake Evans
 * @license     GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name: WPBookList CustomFields Extension
 * Plugin URI: https://www.jakerevans.com
 * Description: A Extension for WPBookList that allows the user to create their own custom fields for each book.
 * Version: 1.0.0
 * Author: Jake Evans
 * Text Domain: wpbooklist
 * Author URI: https://www.jakerevans.com
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $wpdb;

/* REQUIRE STATEMENTS */
	require_once 'includes/class-customfields-general-functions.php';
	require_once 'includes/class-customfields-ajax-functions.php';
/* END REQUIRE STATEMENTS */

/* CONSTANT DEFINITIONS */

	// Root plugin folder directory.
	if ( ! defined('WPBOOKLIST_VERSION_NUM' ) ) {
		define( 'WPBOOKLIST_VERSION_NUM', '6.1.2' );
	}



	// This is the URL our updater / license checker pings. This should be the URL of the site with EDD installed.
	define( 'EDD_SL_STORE_URL_CUSTOMFIELDS', 'https://wpbooklist.com' );

	// The id of your product in EDD.
	define( 'EDD_SL_ITEM_ID_CUSTOMFIELDS', 13515 );

	// This Extension's Version Number.
	define( 'WPBOOKLIST_CUSTOMFIELDS_VERSION_NUM', '1.0.0' );

	// Root plugin folder directory.
	define( 'CUSTOMFIELDS_ROOT_DIR', plugin_dir_path( __FILE__ ) );

	// Root WordPress Plugin Directory.
	define( 'CUSTOMFIELDS_ROOT_WP_PLUGINS_DIR', str_replace( '/wpbooklist-customfields', '', plugin_dir_path( __FILE__ ) ) );

	// Root WPBL Dir.
	if ( ! defined('ROOT_WPBL_DIR' ) ) {
		define( 'ROOT_WPBL_DIR', CUSTOMFIELDS_ROOT_WP_PLUGINS_DIR . 'wpbooklist/' );
	}

	// Root WPBL Url.
	if ( ! defined('ROOT_WPBL_URL' ) ) {
		define( 'ROOT_WPBL_URL', plugins_url() . '/wpbooklist/' );
	}

	// Root WPBL Classes Dir.
	if ( ! defined('ROOT_WPBL_CLASSES_DIR' ) ) {
		define( 'ROOT_WPBL_CLASSES_DIR', ROOT_WPBL_DIR . 'includes/classes/' );
	}

	// Root WPBL Transients Dir.
	if ( ! defined('ROOT_WPBL_TRANSIENTS_DIR' ) ) {
		define( 'ROOT_WPBL_TRANSIENTS_DIR', ROOT_WPBL_CLASSES_DIR . 'transients/' );
	}

	// Root WPBL Translations Dir.
	if ( ! defined('ROOT_WPBL_TRANSLATIONS_DIR' ) ) {
		define( 'ROOT_WPBL_TRANSLATIONS_DIR', ROOT_WPBL_CLASSES_DIR . 'translations/' );
	}

	// Root WPBL Root Img Icons Dir.
	if ( ! defined('ROOT_WPBL_IMG_ICONS_URL' ) ) {
		define( 'ROOT_WPBL_IMG_ICONS_URL', ROOT_WPBL_URL . 'assets/img/icons/' );
	}

	// Root WPBL Root Utilities Dir.
	if ( ! defined('ROOT_WPBL_UTILITIES_DIR' ) ) {
		define( 'ROOT_WPBL_UTILITIES_DIR', ROOT_WPBL_CLASSES_DIR . 'utilities/' );
	}

	// Root WPBL Dir.
	if ( ! defined('ROOT_WPBL_DIR' ) ) {
		define( 'ROOT_WPBL_DIR', COMMENTS_ROOT_WP_PLUGINS_DIR . 'wpbooklist/' );
	}

	// Root plugin folder URL .
	define( 'CUSTOMFIELDS_ROOT_URL', plugins_url() . '/wpbooklist-customfields/' );

	// Root Classes Directory.
	define( 'CUSTOMFIELDS_CLASS_DIR', CUSTOMFIELDS_ROOT_DIR . 'includes/classes/' );

	// Root Update Directory.
	define( 'CUSTOMFIELDS_UPDATE_DIR', CUSTOMFIELDS_CLASS_DIR . 'update/' );

	// Root REST Classes Directory.
	define( 'CUSTOMFIELDS_CLASS_REST_DIR', CUSTOMFIELDS_ROOT_DIR . 'includes/classes/rest/' );

	// Root Compatability Classes Directory.
	define( 'CUSTOMFIELDS_CLASS_COMPAT_DIR', CUSTOMFIELDS_ROOT_DIR . 'includes/classes/compat/' );

	// Root Translations Directory.
	define( 'CUSTOMFIELDS_CLASS_TRANSLATIONS_DIR', CUSTOMFIELDS_ROOT_DIR . 'includes/classes/translations/' );

	// Root Transients Directory.
	define( 'CUSTOMFIELDS_CLASS_TRANSIENTS_DIR', CUSTOMFIELDS_ROOT_DIR . 'includes/classes/transients/' );

	// Root Image URL.
	define( 'CUSTOMFIELDS_ROOT_IMG_URL', CUSTOMFIELDS_ROOT_URL . 'assets/img/' );

	// Root Image Icons URL.
	define( 'CUSTOMFIELDS_ROOT_IMG_ICONS_URL', CUSTOMFIELDS_ROOT_URL . 'assets/img/icons/' );

	// Root CSS URL.
	define( 'CUSTOMFIELDS_CSS_URL', CUSTOMFIELDS_ROOT_URL . 'assets/css/' );

	// Root JS URL.
	define( 'CUSTOMFIELDS_JS_URL', CUSTOMFIELDS_ROOT_URL . 'assets/js/' );

	// Root UI directory.
	define( 'CUSTOMFIELDS_ROOT_INCLUDES_UI', CUSTOMFIELDS_ROOT_DIR . 'includes/ui/' );

	// Root UI Admin directory.
	define( 'CUSTOMFIELDS_ROOT_INCLUDES_UI_ADMIN_DIR', CUSTOMFIELDS_ROOT_DIR . 'includes/ui/admin/' );

	// Define the Uploads base directory.
	$uploads     = wp_upload_dir();
	$upload_path = $uploads['basedir'];
	define( 'CUSTOMFIELDS_UPLOADS_BASE_DIR', $upload_path . '/' );

	// Define the Uploads base URL.
	$upload_url = $uploads['baseurl'];
	define( 'CUSTOMFIELDS_UPLOADS_BASE_URL', $upload_url . '/' );

	// Nonces array.
	define( 'CUSTOMFIELDS_NONCES_ARRAY',
		wp_json_encode(array(
			'adminnonce1' => 'wpbooklist_customfields_save_custom_field_action_callback',
			'adminnonce2' => 'wpbooklist_custom_fields_delete_entry_action_callback',
			'adminnonce3' => 'wpbooklist_custom_fields_edit_entry_action_callback',
		))
	);

/* END OF CONSTANT DEFINITIONS */

/* MISC. INCLUSIONS & DEFINITIONS */

	// Loading textdomain.
	load_plugin_textdomain( 'wpbooklist', false, CUSTOMFIELDS_ROOT_DIR . 'languages' );

/* END MISC. INCLUSIONS & DEFINITIONS */

/* CLASS INSTANTIATIONS */

	// Call the class found in wpbooklist-functions.php.
	$customfields_general_functions = new CustomFields_General_Functions();

	// Call the class found in wpbooklist-functions.php.
	$customfields_ajax_functions = new CustomFields_Ajax_Functions();


/* END CLASS INSTANTIATIONS */


/* FUNCTIONS FOUND IN CLASS-WPBOOKLIST-GENERAL-FUNCTIONS.PHP THAT APPLY PLUGIN-WIDE */

	// Function that loads up the menu page entry for this Extension.
	add_filter( 'wpbooklist_add_sub_menu', array( $customfields_general_functions, 'wpbooklist_customfields_submenu' ) );

	// Adding the function that will take our CUSTOMFIELDS_NONCES_ARRAY Constant from above and create actual nonces to be passed to Javascript functions.
	add_action( 'init', array( $customfields_general_functions, 'wpbooklist_customfields_create_nonces' ) );

	// Function to run any code that is needed to modify the plugin between different versions.
	add_action( 'plugins_loaded', array( $customfields_general_functions, 'wpbooklist_customfields_update_upgrade_function' ) );

	// Adding the admin js file.
	add_action( 'admin_enqueue_scripts', array( $customfields_general_functions, 'wpbooklist_customfields_admin_js' ) );

	// Adding the frontend js file.
	add_action( 'wp_enqueue_scripts', array( $customfields_general_functions, 'wpbooklist_customfields_frontend_js' ) );

	// Adding the admin css file for this extension.
	add_action( 'admin_enqueue_scripts', array( $customfields_general_functions, 'wpbooklist_customfields_admin_style' ) );

	// Adding the Front-End css file for this extension.
	add_action( 'wp_enqueue_scripts', array( $customfields_general_functions, 'wpbooklist_customfields_frontend_style' ) );

	// Function to add table names to the global $wpdb.
	add_action( 'admin_footer', array( $customfields_general_functions, 'wpbooklist_customfields_register_table_name' ) );

	// Function to run any code that is needed to modify the plugin between different versions.
	add_action( 'admin_footer', array( $customfields_general_functions, 'wpbooklist_customfields_admin_pointers_javascript' ) );

	// Verifies that the core WPBookList plugin is installed and activated - otherwise, the Extension doesn't load and a message is displayed to the user.
	register_activation_hook( __FILE__, array( $customfields_general_functions, 'wpbooklist_customfields_core_plugin_required' ) );

	// Creates tables upon activation.
	register_activation_hook( __FILE__, array( $customfields_general_functions, 'wpbooklist_customfields_create_tables' ) );

	// Runs once upon extension activation and adds it's version number to the 'extensionversions' column in the 'wpbooklist_jre_user_options' table of the core plugin.
	register_activation_hook( __FILE__, array( $customfields_general_functions, 'wpbooklist_customfields_record_extension_version' ) );

	add_filter( 'wpbooklist_append_to_book_form_basic_fields', array( $customfields_general_functions, 'wpbooklist_customfields_insert_basic_fields' ) );

	add_filter( 'wpbooklist_append_to_book_form_basic_fields', array( $customfields_general_functions, 'wpbooklist_customfields_insert_textlink_fields' ) );

	add_filter( 'wpbooklist_append_to_book_form_dropdown_fields', array( $customfields_general_functions, 'wpbooklist_customfields_insert_dropdown_fields' ) );

	add_filter( 'wpbooklist_append_to_book_form_image_fields', array( $customfields_general_functions, 'wpbooklist_customfields_insert_imagelink_fields' ) );

	add_filter( 'wpbooklist_append_to_book_form_paragraph_fields', array( $customfields_general_functions, 'wpbooklist_customfields_insert_paragraph_fields' ) );

	// Function that adds in basic text fields to Colorbox.
	add_filter( 'wpbooklist_append_to_book_view_basic_fields', array( $customfields_general_functions, 'wpbooklist_customfields_insert_book_view_basic_fields' ) );

	// Function that adds in text link fields to Colorbox.
	add_filter( 'wpbooklist_append_to_book_view_text_link_fields', array( $customfields_general_functions, 'wpbooklist_customfields_insert_book_view_text_link_fields' ) );

	// Function that adds in image links fields to Colorbox.
	add_filter( 'wpbooklist_append_to_book_view_image_link_fields', array( $customfields_general_functions, 'wpbooklist_customfields_insert_book_view_image_link_fields' ) );

	// Function that adds in Dropdown fields to Colorbox.
	add_filter( 'wpbooklist_append_to_book_view_dropdown_fields', array( $customfields_general_functions, 'wpbooklist_customfields_insert_book_view_dropdown_fields' ) );

	// Function that adds in Paragraph fields to Colorbox.
	add_filter( 'wpbooklist_append_to_book_view_paragraph_fields', array( $customfields_general_functions, 'wpbooklist_customfields_insert_book_view_paragraph_fields' ) );

	// Function that adds in basic text fields to Colorbox.
	add_filter( 'wpbooklist_append_to_page_view_basic_fields', array( $customfields_general_functions, 'wpbooklist_customfields_insert_page_view_basic_fields' ) );

	// Function that adds in text link fields to Colorbox.
	add_filter( 'wpbooklist_append_to_page_view_text_link_fields', array( $customfields_general_functions, 'wpbooklist_customfields_insert_page_view_text_link_fields' ) );

	// Function that adds in image links fields to Colorbox.
	add_filter( 'wpbooklist_append_to_page_view_image_link_fields', array( $customfields_general_functions, 'wpbooklist_customfields_insert_page_view_image_link_fields' ) );

	// Function that adds in Dropdown fields to Colorbox.
	add_filter( 'wpbooklist_append_to_page_view_dropdown_fields', array( $customfields_general_functions, 'wpbooklist_customfields_insert_page_view_dropdown_fields' ) );

	// Function that adds in Paragraph fields to Colorbox.
	add_filter( 'wpbooklist_append_to_page_view_paragraph_fields', array( $customfields_general_functions, 'wpbooklist_customfields_insert_page_view_paragraph_fields' ) );

	// Function that adds in basic text fields to Colorbox.
	add_filter( 'wpbooklist_append_to_post_view_basic_fields', array( $customfields_general_functions, 'wpbooklist_customfields_insert_post_view_basic_fields' ) );

	// Function that adds in text link fields to Colorbox.
	add_filter( 'wpbooklist_append_to_post_view_text_link_fields', array( $customfields_general_functions, 'wpbooklist_customfields_insert_post_view_text_link_fields' ) );

	// Function that adds in image links fields to Colorbox.
	add_filter( 'wpbooklist_append_to_post_view_image_link_fields', array( $customfields_general_functions, 'wpbooklist_customfields_insert_post_view_image_link_fields' ) );

	// Function that adds in Dropdown fields to Colorbox.
	add_filter( 'wpbooklist_append_to_post_view_dropdown_fields', array( $customfields_general_functions, 'wpbooklist_customfields_insert_post_view_dropdown_fields' ) );

	// Function that adds in Paragraph fields to Colorbox.
	add_filter( 'wpbooklist_append_to_post_view_paragraph_fields', array( $customfields_general_functions, 'wpbooklist_customfields_insert_post_view_paragraph_fields' ) );

/* END OF FUNCTIONS FOUND IN CLASS-WPBOOKLIST-GENERAL-FUNCTIONS.PHP THAT APPLY PLUGIN-WIDE */

/* FUNCTIONS FOUND IN CLASS-WPBOOKLIST-AJAX-FUNCTIONS.PHP THAT APPLY PLUGIN-WIDE */

	// For receiving user feedback upon deactivation & deletion.
	add_action( 'wp_ajax_customfields_exit_results_action', array( $customfields_ajax_functions, 'customfields_exit_results_action_callback' ) );

	// For creating new custom fields.
	add_action( 'wp_ajax_wpbooklist_customfields_save_custom_field_action', array( $customfields_ajax_functions, 'wpbooklist_customfields_save_custom_field_action_callback' ) );

	// For deleting custom fields.
	add_action( 'wp_ajax_wpbooklist_custom_fields_delete_entry_action', array( $customfields_ajax_functions, 'wpbooklist_custom_fields_delete_entry_action_callback' ) );

	// For editing custom fields.
	add_action( 'wp_ajax_wpbooklist_custom_fields_edit_entry_action', array( $customfields_ajax_functions, 'wpbooklist_custom_fields_edit_entry_action_callback' ) );

/* END OF FUNCTIONS FOUND IN CLASS-WPBOOKLIST-AJAX-FUNCTIONS.PHP THAT APPLY PLUGIN-WIDE */
