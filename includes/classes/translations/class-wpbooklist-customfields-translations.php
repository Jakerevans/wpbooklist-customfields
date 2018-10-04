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
			$this->trans_17  = __( 'Success!', 'wpbooklist' );
			$this->trans_18  = __( 'You\'ve just added a new Custom Field!', 'wpbooklist' );
			$this->trans_19  = __( 'Thanks for using WPBookList, and', 'wpbooklist' );
			$this->trans_20  = __( 'be sure to check out the other WPBookList Extensions!', 'wpbooklist' );
			$this->trans_21  = __( 'If you happen to be thrilled with WPBookList, then by all means,', 'wpbooklist' );
			$this->trans_22  = __( 'Leave a 5-Star Review Here!', 'wpbooklist' );
			$this->trans_23  = __( 'Whoops! Looks like there was an error trying to add your Custom Field! Here is some developer/code info you might provide to', 'wpbooklist' );
			$this->trans_24  = __( 'WPBookList Tech Support at TechSupport@WPBookList.com:', 'wpbooklist' );
			$this->trans_25  = __( 'Here you can Edit & Delete the Custom Fields you\'ve already created. You can also set some options for each of your Fields, including whether or not you want a field to be displayed in the \'Book View\' (the Pop-Up Window), of a book, or if you\'d rather use the Custom Field for only Searching and Filtering purposes.', 'wpbooklist-textdomain' );
			$this->trans_26  = __( 'Whoops! Looks like you\'ve already created a Custom Field with this name! Rename this Custom Field, and then try again.', 'wpbooklist' );
			$this->trans_27  = __( 'Hide Book View?', 'wpbooklist' );
			$this->trans_28  = __( 'Hide Library View?', 'wpbooklist' );
			$this->trans_29  = __( 'Delete', 'wpbooklist' );
			$this->trans_30  = __( 'Yes', 'wpbooklist' );
			$this->trans_31  = __( 'No', 'wpbooklist' );
			$this->trans_32  = __( 'Edit This Drop-Down Option', 'wpbooklist' );
			$this->trans_33  = __( 'Whoops! Looks like you haven\'t created any Custom Fields yet!', 'wpbooklist' );
			$this->trans_34  = __( 'Click Here to Create some Custom Fields.', 'wpbooklist' );
			$this->trans_35  = __( 'Save All Changes', 'wpbooklist' );



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
				'trans17' => $this->trans_17,
				'trans18' => $this->trans_18,
				'trans19' => $this->trans_19,
				'trans20' => $this->trans_20,
				'trans21' => $this->trans_21,
				'trans22' => $this->trans_22,
				'trans23' => $this->trans_23,
				'trans24' => $this->trans_24,
				'trans25' => $this->trans_25,
				'trans26' => $this->trans_26,
				'trans27' => $this->trans_27,
				'trans28' => $this->trans_28,
				'trans29' => $this->trans_29,
				'trans30' => $this->trans_30,
				'trans31' => $this->trans_31,
				'trans32' => $this->trans_32,
				'trans33' => $this->trans_33,
				'trans34' => $this->trans_34,
				'trans35' => $this->trans_35,
			);

			return $translation_array;
		}
	}
endif;
