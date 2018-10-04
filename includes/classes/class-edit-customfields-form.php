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

			// Get Translations.
			require_once CUSTOMFIELDS_CLASS_TRANSLATIONS_DIR . 'class-wpbooklist-customfields-translations.php';
			$this->trans = new WPBookList_CustomFields_Translations();
			$this->trans->trans_strings();
		}

		/**
		 * Edit_CustomFields_Form Class.
		 */
		public function output_customfields_form() {

			global $wpdb;

			// Build HTML for the saved fields.
			$settings_row        = $wpdb->get_row( 'SELECT * FROM ' . $wpdb->prefix . 'wpbooklist_jre_user_options' );
			$fields              = $settings_row->customfields;
			$dropdown_string     = '';
			$not_dropdown_string = '';

			// If there is any saved custom fields...
			if ( false !== stripos( $fields, '--' ) ) {

				$fields = explode( '--', $fields );

				foreach ( $fields as $key => $value ) {

					if ( '' !== $value ) {

						$exploded_fields = explode( ';', $value );

						$display_name = str_replace( '_', ' ', $exploded_fields[0] );

						// Build the options string for hiding from Book/Library View.
						if ( $this->trans->trans_31 === $exploded_fields[2] ) {
							$hide_book_view_options =
							'<option>' . $this->trans->trans_30 . '</option>
							<option selected>' . $this->trans->trans_31 . '</option>';
							$book_display = $this->trans->trans_31;
						} else {
							$hide_book_view_options =
							'<option selected>' . $this->trans->trans_30 . '</option>
							<option>' . $this->trans->trans_31 . '</option>';
							$book_display = $this->trans->trans_30;
						}

						if ( 'Drop-Down' !== $exploded_fields[1] ) {

							// Build the options string for hiding from Book/Library View.
							if ( $this->trans->trans_31 === $exploded_fields[3] ) {
								$hide_library_view_options =
								'<option>' . $this->trans->trans_30 . '</option>
								<option selected>' . $this->trans->trans_31 . '</option>';
								$lib_display = $this->trans->trans_31;
							} else {
								$hide_library_view_options =
								'<option selected>' . $this->trans->trans_30 . '</option>
								<option>' . $this->trans->trans_31 . '</option>';
								$lib_display = $this->trans->trans_30;
							}

							$not_dropdown_string = $not_dropdown_string . '
							<div class="wpbooklist-customfields-edit-indiv-container-div" id="wpbooklist-customfields-edit-indiv-container-div-' . $key . '">
								<div class="wpbooklist-customfields-edit-input-div">
									<img class="wpbooklist-icon-image-question" data-label="customfields-form-fieldname" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
									<label class="wpbooklist-question-icon-label">' . $this->trans->trans_3 . '</label>
									<div>
										<input readonly class="wpbooklist-customfield-input" id="wpbooklist-customfield-input-fieldname" type="text" placeholder="' . $this->trans->trans_10 . '" value="' . $display_name . '" />
										<input readonly class="wpbooklist-customfield-input" id="wpbooklist-customfield-input-fieldtype" type="hidden" value="' . $exploded_fields[1] . '" />
									</div>
								</div>
								<div class="wpbooklist-customfields-edit-input-div">
									<img class="wpbooklist-icon-image-question" data-label="customfields-form-hidebookview" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
									<label class="wpbooklist-question-icon-label">' . $this->trans->trans_27 . '</label>
									<div>
										<select class="wpbooklist-customfield-select-book-view">
											' . $hide_book_view_options . '
										</select>
									</div>
								</div>
								<div class="wpbooklist-customfields-edit-input-div">
									<img class="wpbooklist-icon-image-question" data-label="customfields-form-hidelibraryview" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
									<label class="wpbooklist-question-icon-label">' . $this->trans->trans_28 . '</label>
									<div>
										<select class="wpbooklist-customfield-select-lib-view">
											' . $hide_library_view_options . '
										</select>
									</div>
								</div>
								<div class="wpbooklist-customfields-edit-input-div">
									<img class="wpbooklist-icon-image-question" data-label="customfields-form-hidelibraryview" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
									<label class="wpbooklist-question-icon-label">' . $this->trans->trans_29 . '</label>
									<div class="wpbooklist-addition-div-customfields-delete" data-key="' . $key . '" data-type="' . $exploded_fields[1] . '" data-name="' . $display_name . '" data-book="' . $book_display . '" data-lib="' . $lib_display . '" data-options="' . $exploded_fields[4] . '"> 
										<img class="wpbooklist-addition-div-img-delete" src="' . ROOT_IMG_ICONS_URL . 'cancel.svg" />
									</div>
								</div>
								<div class="wpbooklist-spinner" id="wpbooklist-spinner-'. $key .'"></div>
							</div>
							';

						} else {
							
							$dropdown_string = $dropdown_string . '
							<div class="wpbooklist-customfields-edit-indiv-container-div" id="wpbooklist-customfields-edit-indiv-container-div-' . $key . '">
								<div class="wpbooklist-customfields-edit-input-div">
									<img class="wpbooklist-icon-image-question" data-label="customfields-form-fieldname" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
									<label class="wpbooklist-question-icon-label">' . $this->trans->trans_3 . '</label>
									<div>
										<input readonly class="wpbooklist-customfield-input" id="wpbooklist-customfield-input-fieldname" type="text" placeholder="' . $this->trans->trans_10 . '" value="' . $display_name . '" />
										<input readonly class="wpbooklist-customfield-input" id="wpbooklist-customfield-input-fieldtype" type="hidden" value="' . $exploded_fields[1] . '" />
									</div>
								</div>
								<div class="wpbooklist-customfields-edit-input-div">
									<img class="wpbooklist-icon-image-question" data-label="customfields-form-hidebookview" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
									<label class="wpbooklist-question-icon-label">' . $this->trans->trans_27 . '</label>
									<div>
										<select class="wpbooklist-customfield-select-book-view">
											' . $hide_book_view_options . '
										</select>
									</div>
								</div>
								<div class="wpbooklist-customfields-edit-input-div">
									<img class="wpbooklist-icon-image-question" data-label="customfields-form-hidelibraryview" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
									<label class="wpbooklist-question-icon-label">' . $this->trans->trans_28 . '</label>
									<div>
										<select class="wpbooklist-customfield-select-lib-view">
											' . $hide_library_view_options . '
										</select>
									</div>
								</div>
								<div class="wpbooklist-customfields-edit-input-div">
									<img class="wpbooklist-icon-image-question" data-label="customfields-form-hidelibraryview" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
									<label class="wpbooklist-question-icon-label">' . $this->trans->trans_29 . '</label>
									<div class="wpbooklist-addition-div-customfields-delete" data-key="' . $key . '" data-type="' . $exploded_fields[1] . '" data-name="' . $display_name . '" data-book="' . $book_display . '" data-lib="' . $lib_display . '" data-options="' . $exploded_fields[4] . '"> 
										<img class="wpbooklist-addition-div-img-delete" src="' . ROOT_IMG_ICONS_URL . 'cancel.svg" />
									</div>
								</div>';

							// Now build out the individual options for the drop-down.
							$optionstring = '';
							$options = explode( '/', $exploded_fields[4] );
							foreach ( $options as $optionkey => $optionvalue ) {

								if ( 0 === $optionkey ) {
									$addimg = '<div class="wpbooklist-addition-div-customfields-add-img-edit"><p class="wpbooklist-addition-div-p-edit-options">' .$this->trans->trans_15 . '<br/>' . $this->trans->trans_16 . '</p><img class="wpbooklist-addition-div-img" src="' . ROOT_IMG_ICONS_URL . 'addrow.svg"></div>';
								} else {
									$addimg = '';
								}

								$optionstring = $optionstring .
								'<div class="wpbooklist-customfields-edit-input-div wpbooklist-customfields-edit-input-dropdown-div">
									<img class="wpbooklist-icon-image-question" data-label="customfields-form-hidelibraryview" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
									<label class="wpbooklist-question-icon-label">' . $this->trans->trans_32 . '</label>
									<div>
										<div class="wpbooklist-customfields-edit-options" style="position:relative;">
											<input class="wpbooklist-customfield-input wpbooklist-customfield-dropdown-input" type="text" placeholder="' . $this->trans->trans_10 . '" value="' . $optionvalue . '" />
											' . $addimg . '
											<input readonly class="wpbooklist-customfield-input" id="wpbooklist-customfield-input-fieldtype" type="hidden" value="' . $exploded_fields[1] . '" />
										</div>
									</div>
								</div>';
							}

							$dropdown_string = $dropdown_string . $optionstring . '<div class="wpbooklist-spinner" id="wpbooklist-spinner-' . $key . '"></div>
							</div>
							';
						}
					}
				}
			} else {
				$not_dropdown_string = '<p class="wpbooklist-tab-intro-para"><img id="wpbooklist-smile-icon-1" src="' . ROOT_IMG_ICONS_URL .  'shocked.svg">' . $this->trans->trans_33 . ' <a href="' . menu_page_url( 'WPBookList-Options-customfields', false ) . '&tab=create-custom-fields">' . $this->trans->trans_34 . '</a></p>';
			}

			$string1 = '
			<p class="wpbooklist-tab-intro-para">' . $this->trans->trans_25 . '</p>
			<div id="wpbooklist-customfields-create-wrapper">
				<div id="wpbooklist-customfields-create-inner-wrapper">
					' . $not_dropdown_string . $dropdown_string . '
				</div>
				<div id="wpbooklist-customfields-dynamic-html-div"></div>
			</div>
			<div class="wpbooklist-response-success-fail-container">
	    		<button class="wpbooklist-response-success-fail-button" type="button" id="wpbooklist-admin-customfields-edit-button">' . $this->trans->trans_35 . '</button>
	    		<div class="wpbooklist-spinner" id="wpbooklist-spinner-edit"></div>
	    		<div class="wpbooklist-response-success-fail-response-actual-container" id="wpbooklist-admin-customfields-response-actual-container"></div>
    		</div>';

			return $string1;
		}
	}

endif;
