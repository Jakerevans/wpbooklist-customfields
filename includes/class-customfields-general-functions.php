<?php
/**
 * Class CustomFields_General_Functions - class-customfields-general-functions.php
 *
 * @author   Jake Evans
 * @category Admin
 * @package  Includes
 * @version  6.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'CustomFields_General_Functions', false ) ) :
	/**
	 * CustomFields_General_Functions class. Here we'll do things like enqueue scripts/css, set up menus, etc.
	 */
	class CustomFields_General_Functions {

		/**
		 * Class Constructor.
		 */
		public function __construct() {

			global $wpdb;

			// Get all of the possible User-created Libraries.
			$this->dynamic_libs = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'wpbooklist_jre_list_dynamic_db_names' );

			// Get user options.
			$this->user_options = $wpdb->get_row( 'SELECT * FROM ' . $wpdb->prefix . 'wpbooklist_jre_user_options' );

			// Get Translations.
			require_once CUSTOMFIELDS_CLASS_TRANSLATIONS_DIR . 'class-wpbooklist-customfields-translations.php';
			$this->trans = new WPBookList_CustomFields_Translations();

		}

		/**
		 * Verifies that the core WPBookList plugin is installed and activated - otherwise, the Extension doesn't load and a message is displayed to the user.
		 */
		public function wpbooklist_customfields_core_plugin_required() {

			// Require core WPBookList Plugin.
			if ( ! is_plugin_active( 'wpbooklist/wpbooklist.php' ) && current_user_can( 'activate_plugins' ) ) {

				// Stop activation redirect and show error.
				wp_die( 'Whoops! This WPBookList Extension requires the Core WPBookList Plugin to be installed and activated! <br><a target="_blank" href="https://wordpress.org/plugins/wpbooklist/">Download WPBookList Here!</a><br><br><a href="' . admin_url( 'plugins.php' ) . '">&laquo; Return to Plugins</a>');
			}
		}

		/** Functions that loads up the menu page entry for this Extension.
		 *
		 *  @param array $submenu_array - The array that contains submenu entries to add to.
		 */
		public function wpbooklist_customfields_submenu( $submenu_array ) {
			$extra_submenu = array(
				'CustomFields',
			);

			// Combine the two arrays.
			$submenu_array = array_merge( $submenu_array, $extra_submenu );
			return $submenu_array;
		}

		/**
		 *  Here we take the Constant defined in wpbooklist.php that holds the values that all our nonces will be created from, we create the actual nonces using wp_create_nonce, and the we define our new, final nonces Constant, called WPBOOKLIST_FINAL_NONCES_ARRAY.
		 */
		public function wpbooklist_customfields_create_nonces() {

			$temp_array = array();
			foreach ( json_decode( CUSTOMFIELDS_NONCES_ARRAY ) as $key => $noncetext ) {
				$nonce              = wp_create_nonce( $noncetext );
				$temp_array[ $key ] = $nonce;
			}

			// Defining our final nonce array.
			define( 'CUSTOMFIELDS_FINAL_NONCES_ARRAY', wp_json_encode( $temp_array ) );

		}

		/**
		 *  Runs once upon extension activation and adds it's version number to the 'extensionversions' column in the 'wpbooklist_jre_user_options' table of the core plugin.
		 */
		public function wpbooklist_customfields_record_extension_version() {
			global $wpdb;

			$table_name      = $wpdb->prefix . 'wpbooklist_jre_user_options';
			$existing_string = $wpdb->get_row( 'SELECT * from ' . $wpdb->prefix . 'wpbooklist_jre_user_options' );

			// Check to see if Extension is already registered.
			if ( false !== strpos( $existing_string->extensionversions, 'customfields' ) ) {
				$split_string = explode( 'customfields', $existing_string->extensionversions );
				$first_part   = $split_string[0];
				$last_part    = substr( $split_string[1], 5 );
				$new_string   = $first_part . 'customfields' . WPBOOKLIST_CUSTOMFIELDS_VERSION_NUM . $last_part;
			} else {
				$new_string = $existing_string->extensionversions . 'customfields' . WPBOOKLIST_CUSTOMFIELDS_VERSION_NUM;
			}

			$data         = array(
				'extensionversions' => $new_string,
			);
			$format       = array( '%s' );
			$where        = array( 'ID' => 1 );
			$where_format = array( '%d' );
			$wpdb->update( $wpdb->prefix . 'wpbooklist_jre_user_options', $data, $where, $format, $where_format );

		}

		/**
		 *  Function to run the compatability code in the Compat class for upgrades/updates, if stored version number doesn't match the defined global in wpbooklist-customfields.php
		 */
		public function wpbooklist_customfields_update_upgrade_function() {

			// Get current version #.
			global $wpdb;
			$existing_string = $wpdb->get_row( 'SELECT * from ' . $wpdb->prefix . 'wpbooklist_jre_user_options' );

			// Check to see if Extension is already registered and matches this version.
			if ( false !== strpos( $existing_string->extensionversions, 'customfields' ) ) {
				$split_string = explode( 'customfields', $existing_string->extensionversions );
				$version      = substr( $split_string[1], 0, 5 );

				// If version number does not match the current version number found in wpbooklist.php, call the Compat class and run upgrade functions.
				if ( WPBOOKLIST_CUSTOMFIELDS_VERSION_NUM !== $version ) {
					require_once CUSTOMFIELDS_CLASS_COMPAT_DIR . 'class-customfields-compat-functions.php';
					$compat_class = new CustomFields_Compat_Functions();
				}
			}
		}

		/**
		 * Adding the admin js file
		 */
		public function wpbooklist_customfields_admin_js() {

			wp_register_script( 'wpbooklist_customfields_adminjs', CUSTOMFIELDS_JS_URL . 'wpbooklist_customfields_admin.min.js', array( 'jquery' ), WPBOOKLIST_VERSION_NUM, true );

			// Next 4-5 lines are required to allow translations of strings that would otherwise live in the wpbooklist-admin-js.js JavaScript File.
			require_once CUSTOMFIELDS_CLASS_TRANSLATIONS_DIR . 'class-wpbooklist-customfields-translations.php';
			$trans = new WPBookList_CustomFields_Translations();

			// Localize the script with the appropriate translation array from the Translations class.
			$translation_array1 = $trans->trans_strings();

			// Now grab all of our Nonces to pass to the JavaScript for the Ajax functions and merge with the Translations array.
			$final_array_of_php_values = array_merge( $translation_array1, json_decode( CUSTOMFIELDS_FINAL_NONCES_ARRAY, true ) );

			// Adding some other individual values we may need.
			$final_array_of_php_values['CUSTOMFIELDS_ROOT_IMG_ICONS_URL'] = CUSTOMFIELDS_ROOT_IMG_ICONS_URL;
			$final_array_of_php_values['ROOT_IMG_ICONS_URL']              = ROOT_IMG_ICONS_URL;
			$final_array_of_php_values['CUSTOMFIELDS_ROOT_IMG_URL']       = CUSTOMFIELDS_ROOT_IMG_URL;
			$final_array_of_php_values['FOR_TAB_HIGHLIGHT']               = admin_url() . 'admin.php';
			$final_array_of_php_values['SAVED_ATTACHEMENT_ID']            = get_option( 'media_selector_attachment_id', 0 );

			// Now registering/localizing our JavaScript file, passing all the PHP variables we'll need in our $final_array_of_php_values array, to be accessed from 'wphealthtracker_php_variables' object (like wphealthtracker_php_variables.nameofkey, like any other JavaScript object).
			wp_localize_script( 'wpbooklist_customfields_adminjs', 'wpbooklistCustomFieldsPhpVariables', $final_array_of_php_values );

			wp_enqueue_script( 'wpbooklist_customfields_adminjs' );

			return $final_array_of_php_values;

		}

		/**
		 * Adding the frontend js file
		 */
		public function wpbooklist_customfields_frontend_js() {

			wp_register_script( 'wpbooklist_customfields_frontendjs', CUSTOMFIELDS_JS_URL . 'wpbooklist_customfields_frontend.min.js', array( 'jquery' ), WPBOOKLIST_CUSTOMFIELDS_VERSION_NUM, true );

			// Next 4-5 lines are required to allow translations of strings that would otherwise live in the wpbooklist-admin-js.js JavaScript File.
			require_once CUSTOMFIELDS_CLASS_TRANSLATIONS_DIR . 'class-wpbooklist-customfields-translations.php';
			$trans = new WPBookList_CustomFields_Translations();

			// Localize the script with the appropriate translation array from the Translations class.
			$translation_array1 = $trans->trans_strings();

			// Now grab all of our Nonces to pass to the JavaScript for the Ajax functions and merge with the Translations array.
			$final_array_of_php_values = array_merge( $translation_array1, json_decode( CUSTOMFIELDS_FINAL_NONCES_ARRAY, true ) );

			// Adding some other individual values we may need.
			$final_array_of_php_values['CUSTOMFIELDS_ROOT_IMG_ICONS_URL'] = CUSTOMFIELDS_ROOT_IMG_ICONS_URL;
			$final_array_of_php_values['CUSTOMFIELDS_ROOT_IMG_URL']       = CUSTOMFIELDS_ROOT_IMG_URL;

			// Now registering/localizing our JavaScript file, passing all the PHP variables we'll need in our $final_array_of_php_values array, to be accessed from 'wphealthtracker_php_variables' object (like wphealthtracker_php_variables.nameofkey, like any other JavaScript object).
			wp_localize_script( 'wpbooklist_customfields_frontendjs', 'wpbooklistCustomFieldsPhpVariables', $final_array_of_php_values );

			wp_enqueue_script( 'wpbooklist_customfields_frontendjs' );

			return $final_array_of_php_values;

		}

		/**
		 * Adding the admin css file
		 */
		public function wpbooklist_customfields_admin_style() {

			wp_register_style( 'wpbooklist_customfields_adminui', CUSTOMFIELDS_CSS_URL . 'wpbooklist-customfields-main-admin.css', null, WPBOOKLIST_CUSTOMFIELDS_VERSION_NUM );
			wp_enqueue_style( 'wpbooklist_customfields_adminui' );

		}

		/**
		 * Adding the frontend css file
		 */
		public function wpbooklist_customfields_frontend_style() {

			wp_register_style( 'wpbooklist_customfields_frontendui', CUSTOMFIELDS_CSS_URL . 'wpbooklist-customfields-main-frontend.css', null, WPBOOKLIST_CUSTOMFIELDS_VERSION_NUM );
			wp_enqueue_style( 'wpbooklist_customfields_frontendui' );

		}

		/**
		 *  Function to add table names to the global $wpdb.
		 */
		public function wpbooklist_customfields_register_table_name() {
			global $wpdb;
			//$wpdb->wpbooklist_jre_saved_book_log = "{$wpdb->prefix}wpbooklist_jre_saved_book_log";
		}

		/**
		 *  Function that calls the Style and Scripts needed for displaying of admin pointer messages.
		 */
		public function wpbooklist_customfields_admin_pointers_javascript() {
			wp_enqueue_style( 'wp-pointer' );
			wp_enqueue_script( 'wp-pointer' );
			wp_enqueue_script( 'utils' );
		}

		/**
		 *  Runs once upon plugin activation and creates the table that holds info on WPBookList Pages & Posts.
		 */
		public function wpbooklist_customfields_create_tables() {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			global $wpdb;
			global $charset_collate;

			/*
			// Call this manually as we may have missed the init hook.
			$this->wpbooklist_customfields_register_table_name();

			$sql_create_table1 = "CREATE TABLE {$wpdb->wpbooklist_customfields}
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

		/** Checks for any 'Plain Text' custom fields, and if found, outputs the HTML into the 'Plain Text' Fields area of the 'Book Form'.
		 *
		 *  @param string $string_book_form - The string that contains the existing form HTML.
		 */
		public function wpbooklist_customfields_insert_basic_fields( $string_book_form ) {

			// If there are fields saved...
			$final_html = '';
			if ( null !== $this->user_options->customfields || '' !== $this->user_options->customfields ) {

				// If there are Plain-Text entries saved...
				if ( false !== stripos( $this->user_options->customfields, 'Plain Text Entry' ) ) {
					$fields_array = explode( '--', $this->user_options->customfields );
					foreach ( $fields_array as $key => $value ) {

						// If the custom field is a 'Plain Text Entry' field...
						if ( false !== stripos( $value, 'Plain Text Entry' ) ) {
							$indiv_fields_array = explode( ';', $value );

							// If the Field name isn't blank or null...
							if ( '' !== $indiv_fields_array[0] && null !== $indiv_fields_array[0] ) {

								$for_label = str_replace( '_', ' ', $indiv_fields_array[0] );

								// Add row to final HTML.
								$final_html = $final_html . '<div class="wpbooklist-book-form-indiv-attribute-container wpbooklist-book-form-indiv-attribute-container-customfields">
									<img class="wpbooklist-icon-image-question-with-link" data-label="book-form-customfield-plaintext" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
									<label class="wpbooklist-question-icon-label" for="book-customfield-' . $indiv_fields_array[0] . '">' . $for_label . '</label>
									<input type="text" data-customfield-type="plaintextentry" class="wpbooklist-addbook-customfield-plain-text-entry" name="book-customfield-' . $indiv_fields_array[0] . '">
								</div>';
							}
						}
					}
				}
			}

			return $string_book_form . $final_html;
		}

		/** Checks for any 'Text Link' custom fields, and if found, outputs the HTML into the 'Text Link' Fields area of the 'Book Form'.
		 *
		 *  @param string $string_book_form - The string that contains the existing form HTML.
		 */
		public function wpbooklist_customfields_insert_textlink_fields( $string_book_form ) {

			// Get Translations.
			require_once CLASS_TRANSLATIONS_DIR . 'class-wpbooklist-translations.php';
			$this->trans = new WPBookList_Translations();
			$this->trans->trans_strings();

			// If there are fields saved...
			$final_html = '';
			if ( null !== $this->user_options->customfields || '' !== $this->user_options->customfields ) {

				// If there are Plain-Text entries saved...
				if ( false !== stripos( $this->user_options->customfields, 'Text Link' ) ) {
					$fields_array = explode( '--', $this->user_options->customfields );
					foreach ( $fields_array as $key => $value ) {

						// If the custom field is a 'Plain Text Entry' field...
						if ( false !== stripos( $value, 'Text Link' ) ) {
							$indiv_fields_array = explode( ';', $value );

							// If the Field name isn't blank or null...
							if ( '' !== $indiv_fields_array[0] && null !== $indiv_fields_array[0] ) {

								$for_label = str_replace( '_', ' ', $indiv_fields_array[0] );

								// Add row to final HTML.
								$final_html = $final_html . '<div class="wpbooklist-book-form-indiv-attribute-container wpbooklist-book-form-indiv-attribute-container-customfields">
									<img class="wpbooklist-icon-image-question-with-link" data-label="book-form-customfield-textlink" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
									<label class="wpbooklist-question-icon-label" for="book-customfield-' . $indiv_fields_array[0] . '">' . $for_label . '</label>
									<input type="text" data-customfield-type="textlink" class="wpbooklist-addbook-customfield-textlink-text-input" name="book-customfield-textlink-text-' . $indiv_fields_array[0] . '" placeholder="' . $this->trans->trans_227 . '">
									<input type="text" data-customfield-type="textlink" class="wpbooklist-addbook-customfield-textlink-url-input" name="book-customfield-textlink-link-' . $indiv_fields_array[0] . '" placeholder="' . $this->trans->trans_228 . '">
								</div>';
							}
						}
					}
				}
			}

			return $string_book_form . $final_html;
		}


		/** Checks for any 'Image Link' custom fields, and if found, outputs the HTML into the 'Image Link' Fields area of the 'Book Form'.
		 *
		 *  @param string $string_book_form - The string that contains the existing form HTML.
		 */
		public function wpbooklist_customfields_insert_imagelink_fields( $string_book_form ) {

			// Get Translations.
			require_once CLASS_TRANSLATIONS_DIR . 'class-wpbooklist-translations.php';
			$this->trans = new WPBookList_Translations();
			$this->trans->trans_strings();

			// If there are fields saved...
			$final_html = '';
			if ( null !== $this->user_options->customfields || '' !== $this->user_options->customfields ) {

				// If there are Plain-Text entries saved...
				if ( false !== stripos( $this->user_options->customfields, 'Image Link' ) ) {
					$fields_array = explode( '--', $this->user_options->customfields );
					foreach ( $fields_array as $key => $value ) {

						// If the custom field is a 'Plain Text Entry' field...
						if ( false !== stripos( $value, 'Image Link' ) ) {
							$indiv_fields_array = explode( ';', $value );

							// If the Field name isn't blank or null...
							if ( '' !== $indiv_fields_array[0] && null !== $indiv_fields_array[0] ) {

								$for_ids = str_replace( ' ', '_', $indiv_fields_array[0] );
								$for_label = str_replace( '_', ' ', $indiv_fields_array[0] );

								// Add row to final HTML.
								$final_html = $final_html . '<div class="wpbooklist-book-form-indiv-attribute-container" style="margin-top:25px;">
									<div class="wpbooklist-book-form-indiv-attribute-image-controls-container">
										<img class="wpbooklist-icon-image-question" data-label="book-form-customfield-imagelink" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
										<label class="wpbooklist-question-icon-label" for="book-customfield-' . $for_ids . '">' . $for_label . '</label>
										<input class="wpbooklist-addbook-upload_image_button" data-previewid="wpbooklist-addbook-preview-img-' . $for_ids . '" data-urlinputid="wpbooklist-addbook-' . $for_ids . '" type="button" value="' . $this->trans->trans_169 . '"/>
										<img class="wpbooklist-addbook-preview-img" id="wpbooklist-addbook-preview-img-' . $for_ids . '"  src="' . ROOT_IMG_ICONS_URL . 'book-placeholder.svg" />
									</div>
									<div class="wpbooklist-book-form-indiv-attribute-image-input-container">
										<input type="text" placeholder="' . $this->trans->trans_172 . '" class="wpbooklist-addbook-image-url-image-input" id="wpbooklist-addbook-' . $for_ids . '" data-previewid="wpbooklist-addbook-preview-img-' . $for_ids . '" name="book-customfield-imagelink-image-' . $for_ids . '">
										<input type="text" placeholder="' . $this->trans->trans_228 . '" class="wpbooklist-addbook-image-url-href-input" id="wpbooklist-addbook-' . $for_ids . '" data-previewid="wpbooklist-addbook-preview-img-' . $for_ids . '" name="book-customfield-imagelink-text-' . $for_ids . '">
									</div>
								</div>';
							}
						}
					}
				}
			}

			return $string_book_form . $final_html;
		}

		/** Checks for any 'Dropdown' custom fields, and if found, outputs the HTML into the 'Dropdown' Fields area of the 'Book Form'.
		 *
		 *  @param string $string_book_form - The string that contains the existing form HTML.
		 */
		public function wpbooklist_customfields_insert_dropdown_fields( $string_book_form ) {

			// Get Translations.
			require_once CLASS_TRANSLATIONS_DIR . 'class-wpbooklist-translations.php';
			$this->trans = new WPBookList_Translations();
			$this->trans->trans_strings();

			// If there are fields saved...
			$final_html = '';
			if ( null !== $this->user_options->customfields || '' !== $this->user_options->customfields ) {

				// If there are Drop-down entries saved...
				if ( false !== stripos( $this->user_options->customfields, 'Drop-Down' ) ) {
					$fields_array = explode( '--', $this->user_options->customfields );
					foreach ( $fields_array as $key => $value ) {

						if ( false !== stripos( $value, 'Drop-Down' ) ) {
							$indiv_fields_array = explode( ';', $value );

							// If the Field name isn't blank or null...
							if ( '' !== $indiv_fields_array[0] && null !== $indiv_fields_array[0] ) {

								$for_ids = str_replace( ' ', '_', $indiv_fields_array[0] );
								$for_label = str_replace( '_', ' ', $indiv_fields_array[0] );

								// Build Option string.
								$options_fields_array = explode( '/', $indiv_fields_array[4] );
								$option_string = '<option value="default" selected disabled default>' . $this->trans->trans_229 . '</option>';
								foreach ( $options_fields_array as $key => $option) {
									$option_string = $option_string . '<option>' . $option . '</option>';
								}

								// Add row to final HTML.
								$final_html = $final_html . '<div class="wpbooklist-book-form-indiv-attribute-container">
									<img class="wpbooklist-icon-image-question" data-label="book-form-customfield-dropdown" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
									<label class="wpbooklist-question-icon-label" for="book-genre">' . $for_label . '</label>
									<select class="wpbooklist-addbook-select-default wpbooklist-addbook-customfield-dropdown-entry" name="book-customfield-' . $indiv_fields_array[0] . '" id="wpbooklist-customfield-dropdown-' . $indiv_fields_array[0] . '" >
										' . $option_string . '
									</select>
								</div>';
							}
						}
					}
				}
			}

			return $string_book_form . $final_html;
		}

		/** Checks for any 'Dropdown' custom fields, and if found, outputs the HTML into the 'Dropdown' Fields area of the 'Book Form'.
		 *
		 *  @param string $string_book_form - The string that contains the existing form HTML.
		 */
		public function wpbooklist_customfields_insert_paragraph_fields( $string_book_form ) {

			// Get Translations.
			require_once CLASS_TRANSLATIONS_DIR . 'class-wpbooklist-translations.php';
			$this->trans = new WPBookList_Translations();
			$this->trans->trans_strings();

			// If there are fields saved...
			$final_html = '';
			if ( null !== $this->user_options->customfields || '' !== $this->user_options->customfields ) {

				// If there are Plain-Text entries saved...
				if ( false !== stripos( $this->user_options->customfields, 'Paragraph' ) ) {
					$fields_array = explode( '--', $this->user_options->customfields );
					foreach ( $fields_array as $key => $value ) {

						// If the custom field is a 'Plain Text Entry' field...
						if ( false !== stripos( $value, 'Paragraph' ) ) {
							$indiv_fields_array = explode( ';', $value );

							// If the Field name isn't blank or null...
							if ( '' !== $indiv_fields_array[0] && null !== $indiv_fields_array[0] ) {

								$for_ids = str_replace( ' ', '_', $indiv_fields_array[0] );
								$for_label = str_replace( '_', ' ', $indiv_fields_array[0] );

								// Add row to final HTML.
								$final_html = $final_html . '<div class="wpbooklist-book-form-indiv-attribute-container">
									<img class="wpbooklist-icon-image-question" data-label="book-form-customfield-paragraph" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
									<label class="wpbooklist-question-icon-label" for="book-notes">' . $for_label . '</label>
									<textarea class="wpbooklist-addbook-customfield-paragraph-input" id="wpbooklist-customfield-textarea-' . $indiv_fields_array[0] . '" name="book-customfield-' . $indiv_fields_array[0] . '"></textarea>
								</div>';
							}
						}
					}
				}
			}

			return $string_book_form . $final_html;
		}

		/** Function that adds in basic text fields to Colorbox.
		 *
		 *  @param array $book_info - The array that contains needed book info.
		 */
		public function wpbooklist_customfields_insert_book_view_basic_fields( $book_info ) {

			// If there are fields saved...
			$final_html = '';
			if ( null !== $this->user_options->customfields || '' !== $this->user_options->customfields ) {

				// If there are Plain-Text entries saved...
				if ( false !== stripos( $this->user_options->customfields, 'Plain Text Entry' ) ) {
					$fields_array = explode( '--', $this->user_options->customfields );
					foreach ( $fields_array as $key => $value ) {

						// If the custom field is a 'Plain Text Entry' field...
						if ( false !== stripos( $value, 'Plain Text Entry' ) ) {

							$indiv_fields_array = explode( ';', $value );

							// If the user hasn't hidden this Custom Field from appearing in Colorbox...
							if ( $this->trans->trans_31 === $indiv_fields_array[2] ) {

								// If the Field name isn't blank or null...
								if ( '' !== $indiv_fields_array[0] && null !== $indiv_fields_array[0] ) {

									$for_label = str_replace( '_', ' ', $indiv_fields_array[0] );

									// If the actual value for this field isn't blank...
									if ( '' !== $book_info->{"$indiv_fields_array[0]"} && null !== $book_info->{"$indiv_fields_array[0]"} ) {

										// Add row to final HTML.
										$final_html = $final_html . '<tr><td><span class="wpbooklist-bold-stats-class" id="wpbooklist_bold">' . $for_label . ': </span><span class="wpbooklist-bold-stats-value">' . $book_info->{"$indiv_fields_array[0]"} . '</span></td></tr>';
									}
								}
							}
						}
					}
				}
			}

			return $final_html;

		}


		/** Function that adds in link text fields to Colorbox.
		 *
		 *  @param array $book_info - The array that contains needed book info.
		 */
		public function wpbooklist_customfields_insert_book_view_text_link_fields( $book_info ) {

			// If there are fields saved...
			$final_html = '';
			if ( null !== $this->user_options->customfields || '' !== $this->user_options->customfields ) {

				// If there are Plain-Text entries saved...
				if ( false !== stripos( $this->user_options->customfields, 'Text Link' ) ) {
					$fields_array = explode( '--', $this->user_options->customfields );
					foreach ( $fields_array as $key => $value ) {

						// If the custom field is a 'Plain Text Entry' field...
						if ( false !== stripos( $value, 'Text Link' ) ) {

							$indiv_fields_array = explode( ';', $value );

							// If the user hasn't hidden this Custom Field from appearing in Colorbox...
							if ( $this->trans->trans_31 === $indiv_fields_array[2] ) {

								// If the Field name isn't blank or null...
								if ( '' !== $indiv_fields_array[0] && null !== $indiv_fields_array[0] ) {

									// If the actual value for this field isn't blank...
									if ( '' !== $book_info->{"$indiv_fields_array[0]"} && null !== $book_info->{"$indiv_fields_array[0]"} ) {

										$values_array = explode( '---', $book_info->{"$indiv_fields_array[0]"} );

										if ( '' !== $values_array[0] && '' !== $values_array[1] && null !== $values_array[0] && null !== $values_array[1] ) {

											// Add row to final HTML.
											$final_html = $final_html . '<tr><td><a class="wpbooklist-bold-stats-class" id="wpbooklist_bold" href="' . $values_array[1] . '">' . $values_array[0] . '</a></td></tr>';

										}
									}
								}
							}
						}
					}
				}
			}

			return $final_html;

		}

		/** Function that adds in Image Link fields to Colorbox.
		 *
		 *  @param array $book_info - The array that contains needed book info.
		 */
		public function wpbooklist_customfields_insert_book_view_image_link_fields( $book_info ) {

			// If there are fields saved...
			$final_html = '';
			if ( null !== $this->user_options->customfields || '' !== $this->user_options->customfields ) {

				// If there are Plain-Text entries saved...
				if ( false !== stripos( $this->user_options->customfields, 'Image Link' ) ) {
					$fields_array = explode( '--', $this->user_options->customfields );
					foreach ( $fields_array as $key => $value ) {

						// If the custom field is a 'Plain Text Entry' field...
						if ( false !== stripos( $value, 'Image Link' ) ) {

							$indiv_fields_array = explode( ';', $value );

							// If the user hasn't hidden this Custom Field from appearing in Colorbox...
							if ( $this->trans->trans_31 === $indiv_fields_array[2] ) {

								// If the Field name isn't blank or null...
								if ( '' !== $indiv_fields_array[0] && null !== $indiv_fields_array[0] ) {

									// If the actual value for this field isn't blank...
									if ( '' !== $book_info->{"$indiv_fields_array[0]"} && null !== $book_info->{"$indiv_fields_array[0]"} ) {

										$values_array = explode( '---', $book_info->{"$indiv_fields_array[0]"} );

										if ( '' !== $values_array[0] && '' !== $values_array[1] && null !== $values_array[0] && null !== $values_array[1] ) {

											// Modify the URL if needed.
											if ( false === stripos( $values_array[1], 'http://' ) && false === stripos( $values_array[1], 'https://' ) ) {
												$values_array[1] = 'http://' . $values_array[1];
											}

											// Add row to final HTML.
											$final_html = $final_html . '<tr><td><a class="wpbooklist-bold-stats-class" id="wpbooklist_bold" href="' . $values_array[1] . '"><img style="width:50px; display: inline-block; margin-left:5px; margin-right:5px;" src="' . $values_array[0] . '"</a></td></tr>';

										}
									}
								}
							}
						}
					}
				}
			}

			return $final_html;

		}

		/** Function that adds in Image Link fields to Colorbox.
		 *
		 *  @param array $book_info - The array that contains needed book info.
		 */
		public function wpbooklist_customfields_insert_book_view_dropdown_fields( $book_info ) {

			// If there are fields saved...
			$final_html = '';
			if ( null !== $this->user_options->customfields || '' !== $this->user_options->customfields ) {

				// If there are Plain-Text entries saved...
				if ( false !== stripos( $this->user_options->customfields, 'Drop-Down' ) ) {
					$fields_array = explode( '--', $this->user_options->customfields );
					foreach ( $fields_array as $key => $value ) {

						// If the custom field is a 'Plain Text Entry' field...
						if ( false !== stripos( $value, 'Drop-Down' ) ) {

							$indiv_fields_array = explode( ';', $value );

							// If the user hasn't hidden this Custom Field from appearing in Colorbox...
							if ( $this->trans->trans_31 === $indiv_fields_array[2] ) {

								// If the Field name isn't blank or null...
								if ( '' !== $indiv_fields_array[0] && null !== $indiv_fields_array[0] ) {

									$for_label = str_replace( '_', ' ', $indiv_fields_array[0] );

									// If the actual value for this field isn't blank...
									if ( '' !== $book_info->{"$indiv_fields_array[0]"} && null !== $book_info->{"$indiv_fields_array[0]"} ) {

										// Add row to final HTML.
										$final_html = $final_html . '<tr><td><span class="wpbooklist-bold-stats-class" id="wpbooklist_bold">' . $for_label . ': </span><span class="wpbooklist-bold-stats-value">' . $book_info->{"$indiv_fields_array[0]"} . '</span></td></tr>';
									}
								}
							}
						}
					}
				}
			}

			return $final_html;

		}

		/** Function that adds in Paragraph fields to Colorbox.
		 *
		 *  @param array $book_info - The array that contains needed book info.
		 */
		public function wpbooklist_customfields_insert_book_view_paragraph_fields( $book_info ) {

			// If there are fields saved...
			$final_html = '';
			if ( null !== $this->user_options->customfields || '' !== $this->user_options->customfields ) {

				// If there are Plain-Text entries saved...
				if ( false !== stripos( $this->user_options->customfields, 'Paragraph' ) ) {
					$fields_array = explode( '--', $this->user_options->customfields );
					foreach ( $fields_array as $key => $value ) {

						// If the custom field is a 'Plain Text Entry' field...
						if ( false !== stripos( $value, 'Paragraph' ) ) {

							$indiv_fields_array = explode( ';', $value );

							// If the user hasn't hidden this Custom Field from appearing in Colorbox...
							if ( $this->trans->trans_31 === $indiv_fields_array[2] ) {

								// If the Field name isn't blank or null...
								if ( '' !== $indiv_fields_array[0] && null !== $indiv_fields_array[0] ) {

									$for_label = str_replace( '_', ' ', $indiv_fields_array[0] );

									// If the actual value for this field isn't blank...
									if ( '' !== $book_info->{"$indiv_fields_array[0]"} && null !== $book_info->{"$indiv_fields_array[0]"} ) {

										// Add row to final HTML.
										$final_html = $final_html . '<p class="wpbooklist_description_p" id="wpbooklist-desc-title-id">' . $for_label . '</p><div class="wpbooklist_desc_p_class">' . stripslashes( html_entity_decode( $book_info->{"$indiv_fields_array[0]"} ) ) . '</div>';
									}
								}
							}
						}
					}
				}
			}

			return $final_html;

		}

		/** Function that adds in basic text fields to a book's Page.
		 *
		 *  @param array $book_info - The array that contains needed book info.
		 */
		public function wpbooklist_customfields_insert_page_view_basic_fields( $book_info ) {

			// If there are fields saved...
			$final_html = '';
			if ( null !== $this->user_options->customfields || '' !== $this->user_options->customfields ) {

				// If there are Plain-Text entries saved...
				if ( false !== stripos( $this->user_options->customfields, 'Plain Text Entry' ) ) {
					$fields_array = explode( '--', $this->user_options->customfields );
					foreach ( $fields_array as $key => $value ) {

						// If the custom field is a 'Plain Text Entry' field...
						if ( false !== stripos( $value, 'Plain Text Entry' ) ) {

							$indiv_fields_array = explode( ';', $value );

							// If the user hasn't hidden this Custom Field from appearing in Colorbox...
							if ( $this->trans->trans_31 === $indiv_fields_array[2] ) {

								// If the Field name isn't blank or null...
								if ( '' !== $indiv_fields_array[0] && null !== $indiv_fields_array[0] ) {

									$for_label = str_replace( '_', ' ', $indiv_fields_array[0] );

									// If the actual value for this field isn't blank...
									if ( '' !== $book_info->{"$indiv_fields_array[0]"} && null !== $book_info->{"$indiv_fields_array[0]"} ) {

										// Add row to final HTML.
										$final_html = $final_html . '<div id="wpbl-pagetd-book-details-7">
										<span>' . $for_label . ': </span>' . $book_info->{"$indiv_fields_array[0]"} . '</div>';
									}
								}
							}
						}
					}
				}
			}

			return $final_html;

		}

		/** Function that adds in basic text fields to a book's Page.
		 *
		 *  @param array $book_info - The array that contains needed book info.
		 */
		public function wpbooklist_customfields_insert_page_view_text_link_fields( $book_info ) {

			// If there are fields saved...
			$final_html = '';
			// If there are Plain-Text entries saved...
			if ( false !== stripos( $this->user_options->customfields, 'Text Link' ) ) {
				$fields_array = explode( '--', $this->user_options->customfields );
				foreach ( $fields_array as $key => $value ) {

					// If the custom field is a 'Plain Text Entry' field...
					if ( false !== stripos( $value, 'Text Link' ) ) {

						$indiv_fields_array = explode( ';', $value );

						// If the user hasn't hidden this Custom Field from appearing in Colorbox...
						if ( $this->trans->trans_31 === $indiv_fields_array[2] ) {

							// If the Field name isn't blank or null...
							if ( '' !== $indiv_fields_array[0] && null !== $indiv_fields_array[0] ) {

								// If the actual value for this field isn't blank...
								if ( '' !== $book_info->{"$indiv_fields_array[0]"} && null !== $book_info->{"$indiv_fields_array[0]"} ) {

									$values_array = explode( '---', $book_info->{"$indiv_fields_array[0]"} );

									if ( array_key_exists( 0, $values_array ) && array_key_exists( 1, $values_array ) ) {

										if ( '' !== $values_array[0] && '' !== $values_array[1] && null !== $values_array[0] && null !== $values_array[1] ) {

											// Modify the URL if needed.
											if ( false === stripos( $values_array[1], 'http://' ) && false === stripos( $values_array[1], 'https://' ) ) {
												$values_array[1] = 'http://' . $values_array[1];
											}

											// Add row to final HTML.
											$final_html = $final_html . '<div id="wpbl-pagetd-book-details-7">
											<span><a class="wpbooklist-bold-stats-class" id="wpbooklist_bold" href="' . $values_array[1] . '">' . $values_array[0] . '</a></span></div>';

										}
									}
								}
							}
						}
					}
				}
			}

			return $final_html;

		}

		/** Function that adds in basic text fields to a book's Page.
		 *
		 *  @param array $book_info - The array that contains needed book info.
		 */
		public function wpbooklist_customfields_insert_page_view_dropdown_fields( $book_info ) {

			// If there are fields saved...
			$final_html = '';
			if ( null !== $this->user_options->customfields || '' !== $this->user_options->customfields ) {

				// If there are Plain-Text entries saved...
				if ( false !== stripos( $this->user_options->customfields, 'Drop-Down' ) ) {
					$fields_array = explode( '--', $this->user_options->customfields );
					foreach ( $fields_array as $key => $value ) {

						// If the custom field is a 'Plain Text Entry' field...
						if ( false !== stripos( $value, 'Drop-Down' ) ) {

							$indiv_fields_array = explode( ';', $value );

							// If the user hasn't hidden this Custom Field from appearing in Colorbox...
							if ( $this->trans->trans_31 === $indiv_fields_array[2] ) {

								// If the Field name isn't blank or null...
								if ( '' !== $indiv_fields_array[0] && null !== $indiv_fields_array[0] ) {

									$for_label = str_replace( '_', ' ', $indiv_fields_array[0] );

									// If the actual value for this field isn't blank...
									if ( '' !== $book_info->{"$indiv_fields_array[0]"} && null !== $book_info->{"$indiv_fields_array[0]"} ) {

										// Add row to final HTML.
										$final_html = $final_html . '<div id="wpbl-pagetd-book-details-7"><span class="wpbooklist-bold-stats-value">' . $for_label . ': </span>' . $book_info->{"$indiv_fields_array[0]"} . '</div>';

									}
								}
							}
						}
					}
				}
			}

			return $final_html;

		}

		/** Function that adds in basic text fields to a book's Page.
		 *
		 *  @param array $book_info - The array that contains needed book info.
		 */
		public function wpbooklist_customfields_insert_page_view_image_link_fields( $book_info ) {

			// If there are fields saved...
			$final_html = '';
			// If there are Plain-Text entries saved...
			if ( false !== stripos( $this->user_options->customfields, 'Image Link' ) ) {
				$fields_array = explode( '--', $this->user_options->customfields );
				foreach ( $fields_array as $key => $value ) {

					// If the custom field is a 'Plain Text Entry' field...
					if ( false !== stripos( $value, 'Image Link' ) ) {

						$indiv_fields_array = explode( ';', $value );

						// If the user hasn't hidden this Custom Field from appearing in Colorbox...
						if ( $this->trans->trans_31 === $indiv_fields_array[2] ) {

							// If the Field name isn't blank or null...
							if ( '' !== $indiv_fields_array[0] && null !== $indiv_fields_array[0] ) {

								// If the actual value for this field isn't blank...
								if ( '' !== $book_info->{"$indiv_fields_array[0]"} && null !== $book_info->{"$indiv_fields_array[0]"} ) {

									$values_array = explode( '---', $book_info->{"$indiv_fields_array[0]"} );

									if ( '' !== $values_array[0] && '' !== $values_array[1] && null !== $values_array[0] && null !== $values_array[1] ) {

										// Modify the URL if needed.
										if ( false === stripos( $values_array[1], 'http://' ) && false === stripos( $values_array[1], 'https://' ) ) {
											$values_array[1] = 'http://' . $values_array[1];
										}

										// Add row to final HTML.
										$final_html = $final_html . '<div id="wpbl-pagetd-book-details-7">
										<span><a class="wpbooklist-bold-stats-class" id="wpbooklist_bold" href="' . $values_array[1] . '"><img style="width:50px; display: inline-block; margin-left:5px; margin-right:5px;" src="' . $values_array[0] . '"</a></span></div>';
									}
								}
							}
						}
					}
				}
			}

			return $final_html;

		}

		/** Function that adds in basic text fields to a book's Page.
		 *
		 *  @param array $book_info - The array that contains needed book info.
		 */
		public function wpbooklist_customfields_insert_page_view_paragraph_fields( $book_info ) {

			// If there are fields saved...
			$final_html = '';
			if ( null !== $this->user_options->customfields || '' !== $this->user_options->customfields ) {

				// If there are Plain-Text entries saved...
				if ( false !== stripos( $this->user_options->customfields, 'Paragraph' ) ) {
					$fields_array = explode( '--', $this->user_options->customfields );
					foreach ( $fields_array as $key => $value ) {

						// If the custom field is a 'Plain Text Entry' field...
						if ( false !== stripos( $value, 'Paragraph' ) ) {

							$indiv_fields_array = explode( ';', $value );

							// If the user hasn't hidden this Custom Field from appearing in Colorbox...
							if ( $this->trans->trans_31 === $indiv_fields_array[2] ) {

								// If the Field name isn't blank or null...
								if ( '' !== $indiv_fields_array[0] && null !== $indiv_fields_array[0] ) {

									$for_label = str_replace( '_', ' ', $indiv_fields_array[0] );

									// If the actual value for this field isn't blank...
									if ( '' !== $book_info->{"$indiv_fields_array[0]"} && null !== $book_info->{"$indiv_fields_array[0]"} ) {

										// Add row to final HTML.
										$final_html = $final_html . '<div id="wpbl-pagetd-book-description-div"><h5 id="wpbl-pagetd-book-description-h5">' . $for_label . '</h5><div id="wpbl-pagetd-book-description-contents">' . stripslashes( html_entity_decode( $book_info->{"$indiv_fields_array[0]"} ) ) . '</div></div>';
									}
								}
							}
						}
					}
				}
			}

			return $final_html;

		}

		/** Function that adds in basic text fields to a book's Page.
		 *
		 *  @param array $book_info - The array that contains needed book info.
		 */
		public function wpbooklist_customfields_insert_post_view_basic_fields( $book_info ) {

			// If there are fields saved...
			$final_html = '';
			if ( null !== $this->user_options->customfields || '' !== $this->user_options->customfields ) {

				// If there are Plain-Text entries saved...
				if ( false !== stripos( $this->user_options->customfields, 'Plain Text Entry' ) ) {
					$fields_array = explode( '--', $this->user_options->customfields );
					foreach ( $fields_array as $key => $value ) {

						// If the custom field is a 'Plain Text Entry' field...
						if ( false !== stripos( $value, 'Plain Text Entry' ) ) {

							$indiv_fields_array = explode( ';', $value );

							// If the user hasn't hidden this Custom Field from appearing in Colorbox...
							if ( $this->trans->trans_31 === $indiv_fields_array[2] ) {

								// If the Field name isn't blank or null...
								if ( '' !== $indiv_fields_array[0] && null !== $indiv_fields_array[0] ) {

									$for_label = str_replace( '_', ' ', $indiv_fields_array[0] );

									// If the actual value for this field isn't blank...
									if ( '' !== $book_info->{"$indiv_fields_array[0]"} && null !== $book_info->{"$indiv_fields_array[0]"} ) {

										// Add row to final HTML.
										$final_html = $final_html . '<div id="wpbl-posttd-book-details-7">
										<span>' . $for_label . ': </span>' . $book_info->{"$indiv_fields_array[0]"} . '</div>';
									}
								}
							}
						}
					}
				}
			}

			return $final_html;

		}

		/** Function that adds in basic text fields to a book's Page.
		 *
		 *  @param array $book_info - The array that contains needed book info.
		 */
		public function wpbooklist_customfields_insert_post_view_text_link_fields( $book_info ) {

			// If there are fields saved...
			$final_html = '';
			// If there are Plain-Text entries saved...
			if ( false !== stripos( $this->user_options->customfields, 'Text Link' ) ) {
				$fields_array = explode( '--', $this->user_options->customfields );
				foreach ( $fields_array as $key => $value ) {

					// If the custom field is a 'Plain Text Entry' field...
					if ( false !== stripos( $value, 'Text Link' ) ) {

						$indiv_fields_array = explode( ';', $value );

						// If the user hasn't hidden this Custom Field from appearing in Colorbox...
						if ( $this->trans->trans_31 === $indiv_fields_array[2] ) {

							// If the Field name isn't blank or null...
							if ( '' !== $indiv_fields_array[0] && null !== $indiv_fields_array[0] ) {

								// If the actual value for this field isn't blank...
								if ( '' !== $book_info->{"$indiv_fields_array[0]"} && null !== $book_info->{"$indiv_fields_array[0]"} ) {

									$values_array = explode( '---', $book_info->{"$indiv_fields_array[0]"} );

									if ( array_key_exists( 0, $values_array ) && array_key_exists( 1, $values_array ) ) {

										if ( '' !== $values_array[0] && '' !== $values_array[1] && null !== $values_array[0] && null !== $values_array[1] ) {

											// Modify the URL if needed.
											if ( false === stripos( $values_array[1], 'http://' ) && false === stripos( $values_array[1], 'https://' ) ) {
												$values_array[1] = 'http://' . $values_array[1];
											}

											// Add row to final HTML.
											$final_html = $final_html . '<div id="wpbl-posttd-book-details-7">
											<span><a class="wpbooklist-bold-stats-class" id="wpbooklist_bold" href="' . $values_array[1] . '">' . $values_array[0] . '</a></span></div>';

										}
									}
								}
							}
						}
					}
				}
			}

			return $final_html;

		}

		/** Function that adds in basic text fields to a book's Page.
		 *
		 *  @param array $book_info - The array that contains needed book info.
		 */
		public function wpbooklist_customfields_insert_post_view_dropdown_fields( $book_info ) {

			// If there are fields saved...
			$final_html = '';
			if ( null !== $this->user_options->customfields || '' !== $this->user_options->customfields ) {

				// If there are Plain-Text entries saved...
				if ( false !== stripos( $this->user_options->customfields, 'Drop-Down' ) ) {
					$fields_array = explode( '--', $this->user_options->customfields );
					foreach ( $fields_array as $key => $value ) {

						// If the custom field is a 'Plain Text Entry' field...
						if ( false !== stripos( $value, 'Drop-Down' ) ) {

							$indiv_fields_array = explode( ';', $value );

							// If the user hasn't hidden this Custom Field from appearing in Colorbox...
							if ( $this->trans->trans_31 === $indiv_fields_array[2] ) {

								// If the Field name isn't blank or null...
								if ( '' !== $indiv_fields_array[0] && null !== $indiv_fields_array[0] ) {

									$for_label = str_replace( '_', ' ', $indiv_fields_array[0] );

									// If the actual value for this field isn't blank...
									if ( '' !== $book_info->{"$indiv_fields_array[0]"} && null !== $book_info->{"$indiv_fields_array[0]"} ) {

										// Add row to final HTML.
										$final_html = $final_html . '<div id="wpbl-posttd-book-details-7"><span class="wpbooklist-bold-stats-value">' . $for_label . ': </span>' . $book_info->{"$indiv_fields_array[0]"} . '</div>';

									}
								}
							}
						}
					}
				}
			}

			return $final_html;

		}

		/** Function that adds in basic text fields to a book's Page.
		 *
		 *  @param array $book_info - The array that contains needed book info.
		 */
		public function wpbooklist_customfields_insert_post_view_image_link_fields( $book_info ) {

			// If there are fields saved...
			$final_html = '';
			// If there are Plain-Text entries saved...
			if ( false !== stripos( $this->user_options->customfields, 'Image Link' ) ) {
				$fields_array = explode( '--', $this->user_options->customfields );
				foreach ( $fields_array as $key => $value ) {

					// If the custom field is a 'Plain Text Entry' field...
					if ( false !== stripos( $value, 'Image Link' ) ) {

						$indiv_fields_array = explode( ';', $value );

						// If the user hasn't hidden this Custom Field from appearing in Colorbox...
						if ( $this->trans->trans_31 === $indiv_fields_array[2] ) {

							// If the Field name isn't blank or null...
							if ( '' !== $indiv_fields_array[0] && null !== $indiv_fields_array[0] ) {

								// If the actual value for this field isn't blank...
								if ( '' !== $book_info->{"$indiv_fields_array[0]"} && null !== $book_info->{"$indiv_fields_array[0]"} ) {

									$values_array = explode( '---', $book_info->{"$indiv_fields_array[0]"} );

									if ( '' !== $values_array[0] && '' !== $values_array[1] && null !== $values_array[0] && null !== $values_array[1] ) {

										// Modify the URL if needed.
										if ( false === stripos( $values_array[1], 'http://' ) && false === stripos( $values_array[1], 'https://' ) ) {
											$values_array[1] = 'http://' . $values_array[1];
										}

										// Add row to final HTML.
										$final_html = $final_html . '<div id="wpbl-posttd-book-details-7">
										<span><a class="wpbooklist-bold-stats-class" id="wpbooklist_bold" href="' . $values_array[1] . '"><img style="width:50px; display: inline-block; margin-left:5px; margin-right:5px;" src="' . $values_array[0] . '"</a></span></div>';
									}
								}
							}
						}
					}
				}
			}

			return $final_html;

		}

		/** Function that adds in basic text fields to a book's Page.
		 *
		 *  @param array $book_info - The array that contains needed book info.
		 */
		public function wpbooklist_customfields_insert_post_view_paragraph_fields( $book_info ) {

			// If there are fields saved...
			$final_html = '';
			if ( null !== $this->user_options->customfields || '' !== $this->user_options->customfields ) {

				// If there are Plain-Text entries saved...
				if ( false !== stripos( $this->user_options->customfields, 'Paragraph' ) ) {
					$fields_array = explode( '--', $this->user_options->customfields );
					foreach ( $fields_array as $key => $value ) {

						// If the custom field is a 'Plain Text Entry' field...
						if ( false !== stripos( $value, 'Paragraph' ) ) {

							$indiv_fields_array = explode( ';', $value );

							// If the user hasn't hidden this Custom Field from appearing in Colorbox...
							if ( $this->trans->trans_31 === $indiv_fields_array[2] ) {

								// If the Field name isn't blank or null...
								if ( '' !== $indiv_fields_array[0] && null !== $indiv_fields_array[0] ) {

									$for_label = str_replace( '_', ' ', $indiv_fields_array[0] );

									// If the actual value for this field isn't blank...
									if ( '' !== $book_info->{"$indiv_fields_array[0]"} && null !== $book_info->{"$indiv_fields_array[0]"} ) {

										// Add row to final HTML.
										$final_html = $final_html . '<div id="wpbl-posttd-book-description-div"><h5 id="wpbl-posttd-book-description-h5">' . $for_label . '</h5><div id="wpbl-posttd-book-description-contents">' . stripslashes( html_entity_decode( $book_info->{"$indiv_fields_array[0]"} ) ) . '</div></div>';
									}
								}
							}
						}
					}
				}
			}

			return $final_html;

		}
	}
endif;
