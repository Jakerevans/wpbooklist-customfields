<?php
/**
 * Class MpExtensionBoilerplate_General_Functions - class-mpextensionboilerplate-general-functions.php
 *
 * @author   Jake Evans
 * @category Admin
 * @package  Includes
 * @version  6.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'MpExtensionBoilerplate_General_Functions', false ) ) :
	/**
	 * MpExtensionBoilerplate_General_Functions class. Here we'll do things like enqueue scripts/css, set up menus, etc.
	 */
	class MpExtensionBoilerplate_General_Functions {

		/** Functions that loads up the menu page entry for this Extension.
		 *
		 *  @param array $submenu_array - The array that contains submenu entries to add to.
		 */
		public function wpbooklist_mpextensionboilerplate_submenu( $submenu_array ) {
			$extra_submenu = array(
				'MpExtensionBoilerplate',
			);

			// Combine the two arrays.
			$submenu_array = array_merge( $submenu_array, $extra_submenu );
			return $submenu_array;
		}

		/**
		 *  Here we take the Constant defined in wpbooklist.php that holds the values that all our nonces will be created from, we create the actual nonces using wp_create_nonce, and the we define our new, final nonces Constant, called WPBOOKLIST_FINAL_NONCES_ARRAY.
		 */
		public function wpbooklist_mpextensionboilerplate_create_nonces() {

			$temp_array = array();
			foreach ( json_decode( MPEXTENSIONBOILERPLATE_NONCES_ARRAY ) as $key => $noncetext ) {
				$nonce              = wp_create_nonce( $noncetext );
				$temp_array[ $key ] = $nonce;
			}

			// Defining our final nonce array.
			define( 'MPEXTENSIONBOILERPLATE_FINAL_NONCES_ARRAY', wp_json_encode( $temp_array ) );

		}

		/**
		 *  Function to run the compatability code in the Compat class for upgrades/updates, if stored version number doesn't match the defined global in wpbooklist-mpextensionboilerplate.php
		 */
		public function wpbooklist_mpextensionboilerplate_update_upgrade_function() {

			// Get current version #.
			global $wpdb;
			$table_name = $wpdb->prefix . 'wpbooklist_jre_user_options';
			$row        = $wpdb->get_row( "SELECT * FROM $table_name" );
			$version    = $row->version;

			// If version number does not match the current version number found in wpbooklist.php, call the Compat class and run upgrade functions.
			if ( WPBOOKLIST_VERSION_NUM !== $version ) {
				require_once CLASS_COMPAT_DIR . 'class-mpextensionboilerplate-compat-functions.php';
				$compat_class = new WPBookList_Compat_Functions();
			}
		}

		/**
		 * Adding the admin js file
		 */
		public function wpbooklist_mpextensionboilerplate_admin_js() {

			wp_register_script( 'wpbooklist_mpextensionboilerplate_adminjs', MPEXTENSIONBOILERPLATE_JS_URL . 'wpbooklist_mpextensionboilerplate_admin.min.js', array( 'jquery' ), WPBOOKLIST_VERSION_NUM, true );

			// Next 4-5 lines are required to allow translations of strings that would otherwise live in the wpbooklist-admin-js.js JavaScript File.
			require_once MPEXTENSIONBOILERPLATE_CLASS_TRANSLATIONS_DIR . 'class-wpbooklist-mpextensionboilerplate-translations.php';
			$trans = new WPBookList_MpExtensionBoilerplate_Translations();

			// Localize the script with the appropriate translation array from the Translations class.
			$translation_array1 = $trans->trans_strings();

			// Now grab all of our Nonces to pass to the JavaScript for the Ajax functions and merge with the Translations array.
			$final_array_of_php_values = array_merge( $translation_array1, json_decode( MPEXTENSIONBOILERPLATE_FINAL_NONCES_ARRAY, true ) );

			// Adding some other individual values we may need.
			$final_array_of_php_values['MPEXTENSIONBOILERPLATE_ROOT_IMG_ICONS_URL'] = MPEXTENSIONBOILERPLATE_ROOT_IMG_ICONS_URL;
			$final_array_of_php_values['MPEXTENSIONBOILERPLATE_ROOT_IMG_URL']       = MPEXTENSIONBOILERPLATE_ROOT_IMG_URL;
			$final_array_of_php_values['FOR_TAB_HIGHLIGHT']                         = admin_url() . 'admin.php';
			$final_array_of_php_values['SAVED_ATTACHEMENT_ID']                      = get_option( 'media_selector_attachment_id', 0 );

			// Now registering/localizing our JavaScript file, passing all the PHP variables we'll need in our $final_array_of_php_values array, to be accessed from 'wphealthtracker_php_variables' object (like wphealthtracker_php_variables.nameofkey, like any other JavaScript object).
			wp_localize_script( 'wpbooklist_mpextensionboilerplate_adminjs', 'wpbooklistMpExtensionBoilerplatePhpVariables', $final_array_of_php_values );

			wp_enqueue_script( 'wpbooklist_mpextensionboilerplate_adminjs' );

			return $final_array_of_php_values;

		}

		/**
		 * Adding the frontend js file
		 */
		public function wpbooklist_mpextensionboilerplate_frontend_js() {

			wp_register_script( 'wpbooklist_mpextensionboilerplate_frontendjs', MPEXTENSIONBOILERPLATE_JS_URL . 'wpbooklist_mpextensionboilerplate_frontend.min.js', array( 'jquery' ), MPEXTENSIONBOILERPLATE_VERSION_NUM, true );

			// Next 4-5 lines are required to allow translations of strings that would otherwise live in the wpbooklist-admin-js.js JavaScript File.
			require_once MPEXTENSIONBOILERPLATE_CLASS_TRANSLATIONS_DIR . 'class-wpbooklist-mpextensionboilerplate-translations.php';
			$trans = new WPBookList_MpExtensionBoilerplate_Translations();

			// Localize the script with the appropriate translation array from the Translations class.
			$translation_array1 = $trans->trans_strings();

			// Now grab all of our Nonces to pass to the JavaScript for the Ajax functions and merge with the Translations array.
			$final_array_of_php_values = array_merge( $translation_array1, json_decode( MPEXTENSIONBOILERPLATE_FINAL_NONCES_ARRAY, true ) );

			// Adding some other individual values we may need.
			$final_array_of_php_values['MPEXTENSIONBOILERPLATE_ROOT_IMG_ICONS_URL'] = MPEXTENSIONBOILERPLATE_ROOT_IMG_ICONS_URL;
			$final_array_of_php_values['MPEXTENSIONBOILERPLATE_ROOT_IMG_URL']       = MPEXTENSIONBOILERPLATE_ROOT_IMG_URL;

			// Now registering/localizing our JavaScript file, passing all the PHP variables we'll need in our $final_array_of_php_values array, to be accessed from 'wphealthtracker_php_variables' object (like wphealthtracker_php_variables.nameofkey, like any other JavaScript object).
			wp_localize_script( 'wpbooklist_mpextensionboilerplate_frontendjs', 'wpbooklistMpExtensionBoilerplatePhpVariables', $final_array_of_php_values );

			wp_enqueue_script( 'wpbooklist_mpextensionboilerplate_frontendjs' );

			return $final_array_of_php_values;

		}

		/**
		 * Adding the admin css file
		 */
		public function wpbooklist_mpextensionboilerplate_admin_style() {

			wp_register_style( 'wpbooklist_mpextensionboilerplate_adminui', MPEXTENSIONBOILERPLATE_CSS_URL . 'wpbooklist-mpextensionboilerplate-main-admin.css', null, MPEXTENSIONBOILERPLATE_VERSION_NUM );
			wp_enqueue_style( 'wpbooklist_mpextensionboilerplate_adminui' );

		}

		/**
		 * Adding the frontend css file
		 */
		public function wpbooklist_mpextensionboilerplate_frontend_style() {

			wp_register_style( 'wpbooklist_mpextensionboilerplate_frontendui', MPEXTENSIONBOILERPLATE_CSS_URL . 'wpbooklist-mpextensionboilerplate-main-frontend.css', null, MPEXTENSIONBOILERPLATE_VERSION_NUM );
			wp_enqueue_style( 'wpbooklist_mpextensionboilerplate_frontendui' );

		}

		/**
		 *  Function to add table names to the global $wpdb.
		 */
		public function wpbooklist_mpextensionboilerplate_register_table_name() {
			global $wpdb;
			//$wpdb->wpbooklist_jre_saved_book_log = "{$wpdb->prefix}wpbooklist_jre_saved_book_log";
		}

		/**
		 *  Function that calls the Style and Scripts needed for displaying of admin pointer messages.
		 */
		public function wpbooklist_mpextensionboilerplate_admin_pointers_javascript() {
			wp_enqueue_style( 'wp-pointer' );
			wp_enqueue_script( 'wp-pointer' );
			wp_enqueue_script( 'utils' );
		}

		/**
		 *  Runs once upon plugin activation and creates the table that holds info on WPBookList Pages & Posts.
		 */
		public function wpbooklist_mpextensionboilerplate_create_tables() {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			global $wpdb;
			global $charset_collate;

			/*
			// Call this manually as we may have missed the init hook.
			$this->wpbooklist_mpextensionboilerplate_register_table_name();

			$sql_create_table1 = "CREATE TABLE {$wpdb->wpbooklist_mpextensionboilerplate}
			(
				ID bigint(190) auto_increment,
				getstories bigint(255),
				createpost bigint(255),
				createpage bigint(255),
				storypersist bigint(255),
				deletedefault bigint(255),
				notifydismiss bigint(255) NOT NULL DEFAULT 1,
				newnotify bigint(255) NOT NULL DEFAULT 1,
				notifymessage MEDIUMTEXT,
				storytimestylepak varchar(255) NOT NULL DEFAULT 'default',
				PRIMARY KEY  (ID),
				KEY getstories (getstories)
			) $charset_collate; ";
			dbDelta( $sql_create_table1 );
			*/
		}

	}
endif;
