<?php

/**
 * NOTE: You should always use the wp_enqueue_script() and wp_enqueue_style() functions to include
 * javascript and css files.
 */

/**
 * bp_easyalbums_add_js()
 *
 * This function will enqueue the components javascript file, so that you can make
 * use of any javascript you bundle with your component within your interface screens.
 */
function bp_easyalbums_add_js() {

	global $bp;

	
		$y = basename(__FILE__);
		$x = plugin_basename(__FILE__);
		$myPath =  (substr($x,0,-strlen($y)));
		
		wp_enqueue_script( 'bp-easyalbums-js', plugins_url( $myPath.'/js/general.js' ) );
		
		
	
}
add_action( 'admin_init', 'bp_easyalbums_add_js', 1 );


function bp_easyalbums_add_css() {
	global $bp;
		$y = basename(__FILE__);
		$x = plugin_basename(__FILE__);
		$myPath =  (substr($x,0,-strlen($y)));
		
		//wp_enqueue_script( 'bp-easyalbums-js', plugins_url( $myPath.'/js/general.js' ) );
		//wp_enqueue_script( 'something', plugins_url( $myPath.'/js/general.js' ) );
		wp_enqueue_style( 'bp-easyalbums-css', plugins_url($myPath.'/css/general.css') );
		wp_print_styles();
		
}
add_action( 'wp_head', 'bp_easyalbums_add_css' );
?>