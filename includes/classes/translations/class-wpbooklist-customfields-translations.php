<?php
/**
 * Class WPBookList_CustomFields_Translations - class-wpbooklist-customfields-translations.php
 *
 * @author   Jake Evans
 * @category Translations
 * @package  Includes/Classes/Translations
 * @version  6.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPBookList_CustomFields_Translations', false ) ) :
	/**
	 * WPBookList_CustomFields_Translations class. This class will house all the translations we may ever need...
	 */
	class WPBookList_CustomFields_Translations {

		/**
		 * Class Constructor - Simply calls the one function to return all Translated strings.
		 */
		public function __construct() {
			$this->trans_strings();
		}

		/**
		 * All the Translations.
		 */
		public function trans_strings() {
			$this->trans_1  = __( 'Edit & Delete Custom Fields', 'wpbooklist-textdomain' );
			$this->trans_2  = __( 'Here you can create your own Custom Fields for your WPBookList Books. Choose from one of 5 types, including Plain Text, Text Links, Image Links, Drop-Downs, and Paragraphs. Just choose a name and type, fill in your content, and click the \'Save Custom Fields\' button below!', 'wpbooklist-textdomain' );
			$this->trans_3  = __( 'Custom Field Name', 'wpbooklist-textdomain' );
			$this->trans_4  = __( 'Custom Field Type', 'wpbooklist-textdomain' );
			$this->trans_5  = __( 'Plain Text Entry', 'wpbooklist-textdomain' );
			$this->trans_6  = __( 'Text Link', 'wpbooklist-textdomain' );
			$this->trans_7  = __( 'Image Link', 'wpbooklist-textdomain' );
			$this->trans_8  = __( 'Drop-Down', 'wpbooklist-textdomain' );
			$this->trans_9  = __( 'Select a Field Type...', 'wpbooklist-textdomain' );
			$this->trans_10 = __( 'Name This Custom Field...', 'wpbooklist-textdomain' );
			$this->trans_11 = __( 'Create Custom Field', 'wpbooklist-textdomain' );
			$this->trans_12 = __( 'Paragraph', 'wpbooklist-textdomain' );
			$this->trans_13 = __( 'Create an Option for this Drop-Down', 'wpbooklist-textdomain' );
			$this->trans_14 = __( 'Create an Option...', 'wpbooklist-textdomain' );
			$this->trans_15 = __( 'Add', 'wpbooklist-textdomain' );
			$this->trans_16 = __( 'Option', 'wpbooklist-textdomain' );



			// The array of translation strings.
			$translation_array = array(
				'trans1'  => $this->trans_1,
				'trans2'  => $this->trans_2,
				'trans3'  => $this->trans_3,
				'trans4'  => $this->trans_4,
				'trans5'  => $this->trans_5,
				'trans6'  => $this->trans_6,
				'trans7'  => $this->trans_7,
				'trans8'  => $this->trans_8,
				'trans9'  => $this->trans_1,
				'trans10' => $this->trans_10,
				'trans11' => $this->trans_11,
				'trans12' => $this->trans_12,
				'trans13' => $this->trans_13,
				'trans14' => $this->trans_14,
				'trans15' => $this->trans_15,
				'trans16' => $this->trans_16,
			);

			return $translation_array;
		}
	}
endif;
