<?php
/**
 * WPBookList WPBookList_MpExtensionBoilerplate_Form Submenu Class
 *
 * @author   Jake Evans
 * @category ??????
 * @package  ??????
 * @version  1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WPBookList_MpExtensionBoilerplate_Form', false ) ) :
/**
 * WPBookList_MpExtensionBoilerplate_Form Class.
 */
class WPBookList_MpExtensionBoilerplate_Form {

	public static function output_mpextensionboilerplate_form(){

		global $wpdb;
	
		// For grabbing an image from media library
		wp_enqueue_media();

		$string1 = '';
		
    	return $string1;
	}
}

endif;