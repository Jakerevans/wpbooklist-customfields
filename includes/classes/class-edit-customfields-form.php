<?php
/**
 * WPBookList Edit_CustomFields_Form Submenu Class
 *
 * @author   Jake Evans
 * @category Extension Ui
 * @package  Includes/Classes
 * @version  6.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Edit_CustomFields_Form', false ) ) :
	/**
	 * Edit_CustomFields_Form Class.
	 */
	class Edit_CustomFields_Form {

		/**
		 * Class Constructor
		 */
		public function __construct() {
			require_once CLASS_DIR . 'class-admin-ui-template.php';
			require_once CUSTOMFIELDS_CLASS_DIR . 'class-edit-customfields-form.php';

			// Get Translations.
			require_once CUSTOMFIELDS_CLASS_TRANSLATIONS_DIR . 'class-wpbooklist-customfields-translations.php';
			$this->trans = new WPBookList_CustomFields_Translations();
			$this->trans->trans_strings();

			$this->output_customfields_form();

		}

		/**
		 * Edit_CustomFields_Form Class.
		 */
		public static function output_customfields_form() {

			global $wpdb;

			// For grabbing an image from media library.
			wp_enqueue_media();

			$string1 = '';

			return $string1;
		}
	}

endif;
