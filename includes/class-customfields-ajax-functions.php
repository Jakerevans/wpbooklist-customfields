<?php
/**
 * Class CustomFields_Ajax_Functions - class-customfields-ajax-functions.php
 *
 * @author   Jake Evans
 * @category Admin
 * @package  Includes
 * @version  6.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'CustomFields_Ajax_Functions', false ) ) :
	/**
	 * CustomFields_Ajax_Functions class. Here we'll do things like enqueue scripts/css, set up menus, etc.
	 */
	class CustomFields_Ajax_Functions {

		/**
		 * Class Constructor - Simply calls the Translations
		 */
		public function __construct() {

			global $wpdb;

			// Get Translations.
			require_once CUSTOMFIELDS_CLASS_TRANSLATIONS_DIR . 'class-wpbooklist-customfields-translations.php';
			$this->trans = new WPBookList_CustomFields_Translations();
			$this->trans->trans_strings();

			// Get all of the possible User-created Libraries.
			$this->dynamic_libs = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'wpbooklist_jre_list_dynamic_db_names' );

		}


		/**
		 * Callback function for saving custom fields.
		 */
		public function wpbooklist_customfields_save_custom_field_action_callback() {
			global $wpdb;
			check_ajax_referer( 'wpbooklist_customfields_save_custom_field_action_callback', 'security' );

			if ( isset( $_POST['name'] ) ) {
				$name = filter_var( wp_unslash( $_POST['name'] ), FILTER_SANITIZE_STRING );

				// If the name contains any spaces, replace with underscores.
				if ( false !== stripos( $name, ' ' ) ) {
					$name = str_replace( ' ', '_', $name );
				}
			}

			if ( isset( $_POST['type'] ) ) {
				$type = filter_var( wp_unslash( $_POST['type'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['dropdownoptions'] ) ) {
				$dropdownoptions = filter_var( wp_unslash( $_POST['dropdownoptions'] ), FILTER_SANITIZE_STRING );
			}

			// If Drop-Down Options aren't applicable to this custom field.
			if ( '' === $dropdownoptions || null === $dropdownoptions ) {
				$dropdownoptions = 'NA';
			}

			$user_options_table = $wpdb->prefix . 'wpbooklist_jre_user_options';
			$row = $wpdb->get_row( "SELECT * FROM $user_options_table" );

			// Check to see if name is taken already.
			if ( false !== strpos( $row->customfields, $name ) ) {
				echo 'nametaken';
				wp_die( 0 );
			} else {

				// Add new Custom Field name to the column in the 'wpbooklist_jre_user_options' table that holds the field name & info.
				$new_string   = $row->customfields . '--' . $name . ';' . $type . ';' . $dropdownoptions;
				$data         = array(
					'customfields' => $new_string,
				);
				$format       = array( '%s' );
				$where        = array( 'ID' => 1 );
				$where_format = array( '%d' );
				$wpdb->update( $user_options_table, $data, $where, $format, $where_format );

				// Add proposed custom field to the db of the default table first.
				$default_book_log = $wpdb->prefix . 'wpbooklist_jre_saved_book_log';

				// If it's a Paragraph type, create column as MEDIUMTEXT, otherwise, as varchar(255).
				if ( $this->trans->trans_12 === $type ) {
					$default_column_create_result = $wpdb->query( "ALTER TABLE $default_book_log ADD " . $name . ' MEDIUMTEXT' );
				} else {
					$default_column_create_result = $wpdb->query( "ALTER TABLE $default_book_log ADD " . $name . ' varchar(255)' );
				}

				// Adding custom field to all dynamic libraries.
				foreach ( $this->dynamic_libs as $db ) {

					// If it's a Paragraph type, create column as MEDIUMTEXT, otherwise, as varchar(255).
					$wpdb->prefix . 'wpbooklist_jre_' . $db->user_table_name;
					if ( $this->trans->trans_12 === $type ) {
						$result = $wpdb->query( 'ALTER TABLE ' . $wpdb->prefix . 'wpbooklist_jre_' . $db->user_table_name . ' ADD ' . $name . ' MEDIUMTEXT' );

						$default_column_create_result = $default_column_create_result . '--sep--' . $result . '--sep--' . $wpdb->prefix . 'wpbooklist_jre_' . $db->user_table_name;
					} else {
						$result = $wpdb->query( 'ALTER TABLE ' . $wpdb->prefix . 'wpbooklist_jre_' . $db->user_table_name . ' ADD . ' . $name . ' varchar(255)' );

						$default_column_create_result = $default_column_create_result . '--sep--' . $result . '--sep--' . $wpdb->prefix . 'wpbooklist_jre_' . $db->user_table_name;
					}
				}

				wp_die( $default_column_create_result );
			}

		}

	}
endif;
