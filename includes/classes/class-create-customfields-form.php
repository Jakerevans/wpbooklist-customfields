<?php
/**
 * WPBookList WPBookList_Create_CustomFields_Form Submenu Class
 *
 * @author   Jake Evans
 * @category ??????
 * @package  ??????
 * @version  1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPBookList_Create_CustomFields_Form', false ) ) :
	/**
	 * WPBookList_Create_CustomFields_Form Class.
	 */
	class WPBookList_Create_CustomFields_Form {

		/**
		 * Class Constructor
		 */
		public function __construct() {

			// Get Translations.
			require_once CUSTOMFIELDS_CLASS_TRANSLATIONS_DIR . 'class-wpbooklist-customfields-translations.php';
			$this->trans = new WPBookList_CustomFields_Translations();
			$this->trans->trans_strings();

		}

		public function output_customfields_form() {

			global $wpdb;

			// For grabbing an image from media library.
			wp_enqueue_media();

			$string1 = '
			<p class="wpbooklist-tab-intro-para">' . $this->trans->trans_2 . '</p>
			<div id="wpbooklist-customfields-create-wrapper">
				<div id="wpbooklist-customfields-create-inner-wrapper">
					<div class="wpbooklist-customfields-create-input-div">
						<img class="wpbooklist-icon-image-question" data-label="customfields-form-fieldname" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
						<label class="wpbooklist-question-icon-label">' . $this->trans->trans_3 . '</label>
						<div>
							<input class="wpbooklist-customfield-input" id="wpbooklist-customfield-input-fieldname" type="text" placeholder="' . $this->trans->trans_10 . '"/>
						</div>
					</div>
					<div class="wpbooklist-customfields-create-input-div">
						<img class="wpbooklist-icon-image-question" data-label="customfields-form-fieldname" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
						<label class="wpbooklist-question-icon-label">' . $this->trans->trans_4 . '</label>
						<div>
							<select class="wpbooklist-customfield-input" id="wpbooklist-customfield-input-fieldtype" type="text">
								<option selected default disabled>' . $this->trans->trans_9 . '</option>
								<option>' . $this->trans->trans_5 . '</option>
								<option>' . $this->trans->trans_6 . '</option>
								<option>' . $this->trans->trans_7 . '</option>
								<option>' . $this->trans->trans_8 . '</option>
								<option>' . $this->trans->trans_12 . '</option>
							</select>
						</div>
					</div>
				</div>
				<div id="wpbooklist-customfields-dynamic-html-div"></div>
			</div>
			<div class="wpbooklist-response-success-fail-container">
	    		<button disabled class="wpbooklist-response-success-fail-button" type="button" id="wpbooklist-admin-customfields-create-button">' . $this->trans->trans_11 . '</button>
	    		<div class="wpbooklist-spinner" id="wpbooklist-spinner-1"></div>
	    		<div class="wpbooklist-response-success-fail-response-actual-container" id="wpbooklist-admin-customfields-response-actual-container"></div>
    		</div>';

			return $string1;
		}
	}

endif;