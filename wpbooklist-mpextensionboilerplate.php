<?php
/**
 * WordPress Book List MpExtensionBoilerplate Extension
 *
 * @package     WordPress Book List MpExtensionBoilerplate Extension
 * @author      Jake Evans
 * @copyright   2018 Jake Evans
 * @license     GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name: WPBookList MpExtensionBoilerplate Extension
 * Plugin URI: https://www.jakerevans.com
 * Description: A Boilerplate Extension for WPBookList that creates a menu page and has it's own tabs.
 * Version: 6.0.0
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
	require_once 'includes/class-mpextensionboilerplate-general-functions.php';
	require_once 'includes/class-mpextensionboilerplate-ajax-functions.php';
/* END REQUIRE STATEMENTS */

/* CONSTANT DEFINITIONS */

	// Root plugin folder directory.
	define( 'MPEXTENSIONBOILERPLATE_VERSION_NUM', '6.0.0' );

	// Root plugin folder directory.
	define( 'MPEXTENSIONBOILERPLATE_ROOT_DIR', plugin_dir_path( __FILE__ ) );

	// Root WordPress Plugin Directory.
	define( 'MPEXTENSIONBOILERPLATE_ROOT_WP_PLUGINS_DIR', str_replace( '/wpbooklist-mpextensionboilerplate', '', plugin_dir_path( __FILE__ ) ) );

	// Root plugin folder URL .
	define( 'MPEXTENSIONBOILERPLATE_ROOT_URL', plugins_url() . '/wpbooklist-mpextensionboilerplate/' );

	// Root Classes Directory.
	define( 'MPEXTENSIONBOILERPLATE_CLASS_DIR', MPEXTENSIONBOILERPLATE_ROOT_DIR . 'includes/classes/' );

	// Root REST Classes Directory.
	define( 'MPEXTENSIONBOILERPLATE_CLASS_REST_DIR', MPEXTENSIONBOILERPLATE_ROOT_DIR . 'includes/classes/rest/' );

	// Root Compatability Classes Directory.
	define( 'MPEXTENSIONBOILERPLATE_CLASS_COMPAT_DIR', MPEXTENSIONBOILERPLATE_ROOT_DIR . 'includes/classes/compat/' );

	// Root Translations Directory.
	define( 'MPEXTENSIONBOILERPLATE_CLASS_TRANSLATIONS_DIR', MPEXTENSIONBOILERPLATE_ROOT_DIR . 'includes/classes/translations/' );

	// Root Transients Directory.
	define( 'MPEXTENSIONBOILERPLATE_CLASS_TRANSIENTS_DIR', MPEXTENSIONBOILERPLATE_ROOT_DIR . 'includes/classes/transients/' );

	// Root Image URL.
	define( 'MPEXTENSIONBOILERPLATE_ROOT_IMG_URL', MPEXTENSIONBOILERPLATE_ROOT_URL . 'assets/img/' );

	// Root Image Icons URL.
	define( 'MPEXTENSIONBOILERPLATE_ROOT_IMG_ICONS_URL', MPEXTENSIONBOILERPLATE_ROOT_URL . 'assets/img/icons/' );

	// Root CSS URL.
	define( 'MPEXTENSIONBOILERPLATE_CSS_URL', MPEXTENSIONBOILERPLATE_ROOT_URL . 'assets/css/' );

	// Root JS URL.
	define( 'MPEXTENSIONBOILERPLATE_JS_URL', MPEXTENSIONBOILERPLATE_ROOT_URL . 'assets/js/' );

	// Root UI directory.
	define( 'MPEXTENSIONBOILERPLATE_ROOT_INCLUDES_UI', MPEXTENSIONBOILERPLATE_ROOT_DIR . 'includes/ui/' );

	// Root UI Admin directory.
	define( 'MPEXTENSIONBOILERPLATE_ROOT_INCLUDES_UI_ADMIN_DIR', MPEXTENSIONBOILERPLATE_ROOT_DIR . 'includes/ui/admin/' );

	// Define the Uploads base directory.
	$uploads     = wp_upload_dir();
	$upload_path = $uploads['basedir'];
	define( 'MPEXTENSIONBOILERPLATE_UPLOADS_BASE_DIR', $upload_path . '/' );

	// Define the Uploads base URL.
	$upload_url = $uploads['baseurl'];
	define( 'MPEXTENSIONBOILERPLATE_UPLOADS_BASE_URL', $upload_url . '/' );

	// Nonces array.
	define( 'MPEXTENSIONBOILERPLATE_NONCES_ARRAY',
		wp_json_encode(array(
			'adminnonce1' => 'wpbooklist_mpextensionboilerplate_functionname_action_callback',
		))
	);

/* END OF CONSTANT DEFINITIONS */

/* MISC. INCLUSIONS & DEFINITIONS */

	// Loading textdomain.
	load_plugin_textdomain( 'wpbooklist', false, MPEXTENSIONBOILERPLATE_ROOT_DIR . 'languages' );

/* END MISC. INCLUSIONS & DEFINITIONS */

/* CLASS INSTANTIATIONS */

	// Call the class found in wpbooklist-functions.php.
	$mpextensionboilerplate_general_functions = new MpExtensionBoilerplate_General_Functions();

	// Call the class found in wpbooklist-functions.php.
	$mpextensionboilerplate_ajax_functions = new MpExtensionBoilerplate_Ajax_Functions();


/* END CLASS INSTANTIATIONS */


/* FUNCTIONS FOUND IN CLASS-WPBOOKLIST-GENERAL-FUNCTIONS.PHP THAT APPLY PLUGIN-WIDE */

	// Function that loads up the menu page entry for this Extension.
	add_filter( 'wpbooklist_add_sub_menu', array( $mpextensionboilerplate_general_functions, 'wpbooklist_mpextensionboilerplate_submenu' ) );

	// Adding the function that will take our MPEXTENSIONBOILERPLATE_NONCES_ARRAY Constant from above and create actual nonces to be passed to Javascript functions.
	add_action( 'init', array( $mpextensionboilerplate_general_functions, 'wpbooklist_mpextensionboilerplate_create_nonces' ) );

	// Function to run any code that is needed to modify the plugin between different versions.
	add_action( 'plugins_loaded', array( $mpextensionboilerplate_general_functions, 'wpbooklist_mpextensionboilerplate_update_upgrade_function' ) );

	// Adding the admin js file.
	add_action( 'admin_enqueue_scripts', array( $mpextensionboilerplate_general_functions, 'wpbooklist_mpextensionboilerplate_admin_js' ) );

	// Adding the frontend js file.
	add_action( 'wp_enqueue_scripts', array( $mpextensionboilerplate_general_functions, 'wpbooklist_mpextensionboilerplate_frontend_js' ) );

	// Adding the admin css file for this extension.
	add_action( 'admin_enqueue_scripts', array( $mpextensionboilerplate_general_functions, 'wpbooklist_mpextensionboilerplate_admin_style' ) );

	// Adding the Front-End css file for this extension.
	add_action( 'wp_enqueue_scripts', array( $mpextensionboilerplate_general_functions, 'wpbooklist_mpextensionboilerplate_frontend_style' ) );

	// Function to add table names to the global $wpdb.
	add_action( 'admin_footer', array( $mpextensionboilerplate_general_functions, 'wpbooklist_mpextensionboilerplate_register_table_name' ) );

	// Function to run any code that is needed to modify the plugin between different versions.
	add_action( 'admin_footer', array( $mpextensionboilerplate_general_functions, 'wpbooklist_mpextensionboilerplate_admin_pointers_javascript' ) );

	// Creates tables upon activation.
	register_activation_hook( __FILE__, array( $mpextensionboilerplate_general_functions, 'wpbooklist_mpextensionboilerplate_create_tables' ) );


/* END OF FUNCTIONS FOUND IN CLASS-WPBOOKLIST-GENERAL-FUNCTIONS.PHP THAT APPLY PLUGIN-WIDE */

/* FUNCTIONS FOUND IN CLASS-WPBOOKLIST-AJAX-FUNCTIONS.PHP THAT APPLY PLUGIN-WIDE */

	// For receiving user feedback upon deactivation & deletion.
	add_action( 'wp_ajax_mpextensionboilerplate_exit_results_action', array( $mpextensionboilerplate_ajax_functions, 'mpextensionboilerplate_exit_results_action_callback' ) );

/* END OF FUNCTIONS FOUND IN CLASS-WPBOOKLIST-AJAX-FUNCTIONS.PHP THAT APPLY PLUGIN-WIDE */






















