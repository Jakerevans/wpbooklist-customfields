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

				if ( function_exists( 'preg_replace' ) ) {

					$name = preg_replace( '/[^ \w]+/', '', $name );

				} else {

					/** Just an empty function to satisfy the preg_replace_callback() requirements - needed for PHP 7. Apparently, this callback function is ran for each matching character in the string, and returns whatever you specify.
					 *
					 * @param array $matches - the preg_replace matches.
					 */
					function wpbooklist_preg_replace_callback_func( $matches ) {
						return '';
					}

					$name = preg_replace_callback( '/[^ \w]+/', 'wpbooklist_preg_replace_callback_func', $name );
				}

				// If the name contains any spaces, replace with underscores.
				if ( false !== stripos( $name, ' ' ) ) {
					$name = str_replace( ' ', '_', $name );
				}

				// If the name contains any dashes, replace with underscores.
				if ( false !== stripos( $name, '-' ) ) {
					$name = str_replace( '-', '_', $name );
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
				echo 'nametaken--sep--';
				wp_die( 0 );
			} else {

				// Add new Custom Field name to the column in the 'wpbooklist_jre_user_options' table that holds the field name & info.
				$new_string   = $row->customfields . '--' . $name . ';' . $type . ';' . $this->trans->trans_31 . ';' . $this->trans->trans_30 . ';' . $dropdownoptions;
				$data         = array(
					'customfields' => $new_string,
				);
				$format       = array( '%s' );
				$where        = array( 'ID' => 1 );
				$where_format = array( '%d' );
				$wpdb->update( $user_options_table, $data, $where, $format, $where_format );

				// Add proposed custom field to the db of the default table first.
				$default_book_log = $wpdb->prefix . 'wpbooklist_jre_saved_book_log';

				// If it's a Paragraph type, create column as MEDIUMTEXT, otherwise, as TEXT.
				if ( $this->trans->trans_12 === $type ) {
					$default_column_create_result = $wpdb->query( "ALTER TABLE $default_book_log ADD " . $name . ' MEDIUMTEXT' );
				} else {
					$default_column_create_result = $wpdb->query( "ALTER TABLE $default_book_log ADD " . $name . ' TEXT' );
				}

				// If we've encountered an error adding to the default table, end execution right here.
				if ( true !== $default_column_create_result ) {
					wp_die( '0--sep--' . $wpdb->last_error . '--sep--Error-After-Default-Table-Attempt' );
				}

				// Adding custom field to all dynamic libraries.
				foreach ( $this->dynamic_libs as $db ) {

					// If it's a Paragraph type, create column as MEDIUMTEXT, otherwise, as TEXT.
					$wpdb->prefix . 'wpbooklist_jre_' . $db->user_table_name;
					if ( $this->trans->trans_12 === $type ) {
						$result = $wpdb->query( 'ALTER TABLE ' . $wpdb->prefix . 'wpbooklist_jre_' . $db->user_table_name . ' ADD ' . $name . ' MEDIUMTEXT' );

						// If we've encountered an error adding to this Dynamic table, end execution right here.
						if ( true !== $result ) {
							wp_die( '0--sep--' . $wpdb->last_error . '--sep--Error-After-Dynamic-Table-Dropdown-Attempt' );
						}

						$default_column_create_result = $default_column_create_result . '--sep--' . $result . '--sep--' . $wpdb->prefix . 'wpbooklist_jre_' . $db->user_table_name;
					} else {
						$result = $wpdb->query( 'ALTER TABLE ' . $wpdb->prefix . 'wpbooklist_jre_' . $db->user_table_name . ' ADD . ' . $name . ' TEXT' );

						// If we've encountered an error adding to this Dynamic table, end execution right here.
						if ( true !== $result ) {
							wp_die( '0--sep--' . $wpdb->last_error . '--sep--Error-After-Default-Table-Notdropdown-Attempt' );
						}

						$default_column_create_result = $default_column_create_result . '--sep--' . $result . '--sep--' . $wpdb->prefix . 'wpbooklist_jre_' . $db->user_table_name;
					}
				}

				// If we made it this far, assume no errors, and return a string containing the names of all the tables this Custom Field was added to.
				wp_die( $default_column_create_result );

			}
		}

		/**
		 * Callback function for deleting custom fields.
		 */
		public function wpbooklist_custom_fields_delete_entry_action_callback() {

			global $wpdb;
			check_ajax_referer( 'wpbooklist_custom_fields_delete_entry_action_callback', 'security' );

			if ( isset( $_POST['name'] ) ) {
				$name = filter_var( wp_unslash( $_POST['name'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['dbstring'] ) ) {
				$dbstring = filter_var( wp_unslash( $_POST['dbstring'] ), FILTER_SANITIZE_STRING );
			}

			// Now grab the Custom Fields entry in the 'wpbooklist_jre_user_options' table, remove the string.
			$dbfieldsentry = $wpdb->get_row( 'SELECT * FROM ' . $wpdb->prefix . 'wpbooklist_jre_user_options' );
			$newtext = str_replace( $dbstring, '', $dbfieldsentry->customfields );

			$data         = array(
				'customfields' => $newtext,
			);
			$format       = array( '%s' );
			$where        = array( 'ID' => 1 );
			$where_format = array( '%d' );
			$result = $wpdb->update( $wpdb->prefix . 'wpbooklist_jre_user_options', $data, $where, $format, $where_format );

			// Drop the columns in default table.
			$default_column_delete_result = $wpdb->query( 'ALTER TABLE ' . $wpdb->prefix . 'wpbooklist_jre_saved_book_log DROP COLUMN ' . $name );

			// Now drop from all Dynamic tables.
			foreach ( $this->dynamic_libs as $db ) {
				$dynamic_column_delete_result = $wpdb->query( 'ALTER TABLE ' . $wpdb->prefix . 'wpbooklist_jre_' . $db->user_table_name . ' DROP COLUMN ' . $name );
			}

			wp_die( $result );
		}

		/**
		 * Callback function for editing custom fields.
		 */
		public function wpbooklist_custom_fields_edit_entry_action_callback() {

			global $wpdb;
			check_ajax_referer( 'wpbooklist_custom_fields_edit_entry_action_callback', 'security' );

			if ( isset( $_POST['dbstring'] ) ) {
				$dbstring = filter_var( wp_unslash( $_POST['dbstring'] ), FILTER_SANITIZE_STRING );
			}

			$data         = array(
				'customfields' => $dbstring,
			);
			$format       = array( '%s' );
			$where        = array( 'ID' => 1 );
			$where_format = array( '%d' );
			$result = $wpdb->update( $wpdb->prefix . 'wpbooklist_jre_user_options', $data, $where, $format, $where_format );

			

			wp_die( $result );
		}
	}
endif;
