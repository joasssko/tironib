<?php
/*
Plugin Name: Easy Albums - Buddypress users create and share images, video and audio albums - the easy way.
Plugin URI: http://www.itaynoy.com/easyalbums/
Description: Let your users upload images, manage their media and post and share it on buddypress activity stream!
Version: 1.0.0
Revision Date: NOV 23, 2010
Requires at least: 2.0.2
License: (Easyalbums: GNU General Public License 2.0 (GPL) http://www.gnu.org/licenses/gpl.html)
Author: Itay Noy
Author URI: http://www.itaynoy.com/
Site Wide Only: false
*/
define ( 'BP_EASYALBUMS_VERSION', '1.0.0' );


//add_action( 'bp_include', 'bp_example_init' );

function bp_easyalbums_init() {
	require( dirname( __FILE__ ) . '/includes/bp-easyalbums-core.php' );
	do_action('bp_album_init');
}
add_action( 'bp_include', 'bp_easyalbums_init' );


function bp_example_setup_root_component() {
	bp_core_add_root_component( BP_EXAMPLE_SLUG );
}


function bp_easyalbums_activate() {
	global $wpdb;

	if ( !empty($wpdb->charset) )
		$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";


	 $sql[] = "CREATE TABLE {$wpdb->base_prefix}bp_cp_galleries (
				ID int(11) NOT NULL AUTO_INCREMENT,
				uID varchar(255) DEFAULT NULL,
				fID varchar(255) DEFAULT NULL,
				gal_type varchar(255) DEFAULT NULL,
				gal_title varchar(255) DEFAULT NULL,
				published int(11) DEFAULT '0',
				dateModified timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				status enum('1','0') DEFAULT '1',
				PRIMARY KEY (ID),
				KEY idx_uid (uID),
				KEY idx_fid (fID)
			) ENGINE=MyISAM {$charset_collate};";

	 
	  $sql[] = "CREATE TABLE IF NOT EXISTS {$wpdb->base_prefix}bp_easyalbums_templates (
			  ID int(11) NOT NULL AUTO_INCREMENT,
			  title varchar(255) DEFAULT NULL,
			  tpl varchar(20) DEFAULT NULL,
			  indx int(11) DEFAULT NULL,
			  type varchar(20) DEFAULT NULL,
			  PRIMARY KEY (`ID`)
			) ENGINE=MyISAM AUTO_INCREMENT=4 {$charset_collate};";

	  $sql[] = "INSERT INTO {$wpdb->base_prefix}bp_easyalbums_templates VALUES ('1', 'General', 'AEAAqSaD_z3h', 10, 'images');"; 
	  $sql[] = "INSERT INTO {$wpdb->base_prefix}bp_easyalbums_templates VALUES ('2', 'Video', 'AELA3RKs_nti', 20, 'video');"; 
	  $sql[] = "INSERT INTO {$wpdb->base_prefix}bp_easyalbums_templates VALUES ('3', 'Audio', 'AkIALS6N_Xrj', 30, 'audio');"; 
	  
	
	require_once( ABSPATH . 'wp-admin/upgrade-functions.php' );

	dbDelta($sql);

	update_site_option( 'bp-easyalbums-db-version', BP_EASYALBUMS_DB_VERSION );
	
}
register_activation_hook( __FILE__, 'bp_easyalbums_activate' );

/* On deacativation, clean up anything your component has added. */
function bp_easyalbums_deactivate() {
	
}
register_deactivation_hook( __FILE__, 'bp_easyalbums_deactivate' );
?>