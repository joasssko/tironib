<?php

define ( 'BP_EASYALBUMS_IS_INSTALLED', 1 );
define ( 'BP_EASYALBUMS_DB_VERSION', '0.2' );

if ( !defined( 'BP_EASYALBUMS_SLUG' ) )
	define ( 'BP_EASYALBUMS_SLUG', get_site_option('bp_easyalbums_slug') );


if ( file_exists( dirname( __FILE__ ) . '/languages/' . get_locale() . '.mo' ) )
	load_textdomain( 'bp-easyalbums', dirname( __FILE__ ) . '/languages/' . get_locale() . '.mo' );


require ( dirname( __FILE__ ) . '/wp-media-cincopa.php' );
require ( dirname( __FILE__ ) . '/bp-easyalbums-cssjs.php' );
require ( dirname( __FILE__ ) . '/bp-easyalbums-cpBase.php' );
require ( dirname( __FILE__ ) . '/bp-easyalbums-cincopa.php' );


	function bp_easyalbums_setup_globals() {
		global $bp, $wpdb;
	
		/* For internal identification */
		$bp->easyalbums->id = BP_EASYALBUMS_SLUG;
	
		$bp->easyalbums->table_name = $wpdb->base_prefix . 'bp_cp_galleries';
		$bp->easyalbums->table_templates = $wpdb->base_prefix . 'bp_easyalbums_templates';
		$bp->easyalbums->format_notification_function = 'bp_easyalbums_format_notifications';
		$bp->easyalbums->slug = BP_EASYALBUMS_SLUG;
		$bp->easyalbums->rename_action = "rename_easy_album";
	
		$bp->easyalbums->cincopaId = get_site_option('bp_cp_uid');
		/* Register this in the active components array */
		$bp->active_components[$bp->easyalbums->slug] = $bp->easyalbums->id;
	}
	/***
	 * In versions of BuddyPress 1.2.2 and newer you will be able to use:
	 * add_action( 'bp_setup_globals', 'bp_easyalbums_setup_globals' );
	 */
	add_action( 'wp', 'bp_easyalbums_setup_globals', 2 );
	add_action( 'admin_menu', 'bp_easyalbums_setup_globals', 2 );

/**
 * bp_easyalbums_add_admin_menu()
 *
 * This function will add a WordPress wp-admin admin menu for your component under the
 * "BuddyPress" menu.
 */
	function bp_easyalbums_add_admin_menu() {
		global $bp;
	
		if ( !$bp->loggedin_user->is_site_admin )
			return false;
	
		require ( dirname( __FILE__ ) . '/bp-easyalbums-admin.php' );
	
		add_submenu_page( 'bp-general-settings', __( 'Easyalbums Admin', 'bp-easyalbums' ), __( 'Easyalbums Admin', 'bp-easyalbums' ), 'manage_options', 'bp-easyalbums-settings', 'bp_easyalbums_admin' );
	}
	add_action( 'admin_menu', 'bp_easyalbums_add_admin_menu' );

	function bp_easyalbums_setup_nav() {
		global $bp;
	
		/* Add 'Easyalbums' to the main user profile navigation */
		bp_core_new_nav_item( array(
			'name' => __( get_site_option('bp_easyalbums_tab'), 'bp-easyalbums' ),
			'slug' => $bp->easyalbums->slug,
			'parent_slug' => '',
			'parent_url' => $easyalbums_link,
			'position' => 80,
			'screen_function' => 'bp_easyalbums_screen_albums'
		) );
	
		$easyalbums_link = $bp->loggedin_user->domain . $bp->easyalbums->slug . '/';
			
	}

	add_action( 'wp', 'bp_easyalbums_setup_nav', 2 );
	add_action( 'admin_menu', 'bp_easyalbums_setup_nav', 2 );

	function bp_easyalbums_load_template_filter( $found_template, $templates ) {
		global $bp;
	
		/**
		 * Only filter the template location when we're on the easyalbums component pages.
		 */
		if ( $bp->current_component != $bp->easyalbums->slug )
			return $found_template;
	
		foreach ( (array) $templates as $template ) {
			if ( file_exists( STYLESHEETPATH . '/' . $template ) )
				$filtered_templates[] = STYLESHEETPATH . '/' . $template;
			else
				$filtered_templates[] = dirname( __FILE__ ) . '/templates/' . $template;
		}
	
		$found_template = $filtered_templates[0];
	
		return apply_filters( 'bp_easyalbums_load_template_filter', $found_template );
	}
	add_filter( 'bp_located_template', 'bp_easyalbums_load_template_filter', 10, 2 );
								
	function bp_easyalbums_defPage($err = ""){
		bp_core_add_message( $err, 'error' );	
		bp_core_load_template( apply_filters( 'bp_easyalbums_template_albums', 'easyalbums/galleries' ) );
	}
	
	function cac_email_activity_checkbox() {
		
		global $bp,$wpdb;
		
		$_uid 	= $bp->loggedin_user->id;
		$sql = "SELECT * FROM {$bp->easyalbums->table_name} WHERE uID='$_uid' AND status='1' ORDER BY ID";
		$result = $wpdb->get_results( $sql );
		$count = count($result);
		if(!empty($count)){
			echo "<div class='easyalbums_inject_album_holder'>Select Album: 
			<select id='easyalbums_inject_album'>";
			foreach($result as $gal){
				echo "<option value='".$gal->fID."'>".$gal->gal_title."</option>"; 
	
			} 
	?>
			</select>
			<div  id='injectBT' onclick='document.getElementById("whats-new").value+="[cincopa "+document.getElementById("easyalbums_inject_album").options[document.getElementById("easyalbums_inject_album").selectedIndex].value+"]"'> Insert </div> 
			</div>
	<?php
		}
	}
	add_action( 'bp_activity_post_form_options', 'cac_email_activity_checkbox',0 );

function bp_easyalbums_publish_to_activity($user_link,$_title,$_fid){
	
	$i = bp_easyalbums_record_activity( array(
		'type' => 'album created',
		'action' => sprintf( __( '%s published a new album: %s', 'bp-easyalbums' ),  $user_link,$_title),
		$user_link,
		'content' => '[cincopa '.$_fid.']'
	) );
	
	
	return $i;
}

function bp_easyalbums_record_activity( $args = '' ) {
	global $bp;

	if ( !function_exists( 'bp_activity_add' ) )
		return false;

	$defaults = array(
		'id' => false,
		'user_id' => $bp->loggedin_user->id,
		'action' => '',
		'content' => '',
		'primary_link' => '',
		'component' => $bp->easyalbums->id,
		'type' => false,
		'item_id' => false,
		'secondary_item_id' => false,
		'recorded_time' => gmdate( "Y-m-d H:i:s" ),
		'hide_sitewide' => false
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r );

	$i =  bp_activity_add(array( 
			'id' => $id, 
			'user_id' => $user_id, 
			'action' => $action, 
			'content' => $content, 
			'primary_link' => $primary_link, 
			'component' => $component, 
			'type' => $type, 
			'item_id' => $item_id, 
			'secondary_item_id' => $secondary_item_id, 
			'recorded_time' => $recorded_time, 
			'hide_sitewide' => $hide_sitewide 
		));
		
	return $i;
}
/********************************************************************************
/********************************************************************************
/********************************************************************************
 * Screen Functions
 */

	function bp_easyalbums_screen_albums() {
		global $bp,$wpdb;
		
		
				
		if ( $bp->current_component == $bp->easyalbums->slug ) {
			
			switch($_GET['action']){
			
				case "publish":
				
					$user_link = bp_core_get_userlink( $bp->loggedin_user->id );
					$_fid = $_GET['fid'];
					$_uid = $bp->loggedin_user->id;
					$sql = "SELECT * FROM {$bp->easyalbums->table_name} WHERE uID='$_uid' AND fID='$_fid' AND status='1' ORDER BY ID";
					$result = $wpdb->get_row( $sql );
					$_title = $result->gal_title;
					$_title = sprintf( '<a href="%s" title="Create Album!" >'.$result->gal_title.'</a>', wp_nonce_url(bp_displayed_user_domain() . bp_current_component() . "/?action=view&fid=$_fid", 'bp_easyalbum_screen_create' ));
					
					
					if($result){
						bp_easyalbums_publish_to_activity($user_link,$_title,$_fid);
						bp_core_add_message( __( "Album \"".$result->gal_title."\" has been publish to the activity stream!", 'bp-easyalbums' ), 'success' );	
					}else{
						bp_core_add_message( __( "Album \"".$result->gal_title."\" has not been publish to the activity stream!", 'bp-easyalbums' ), 'Error' );
					}
					bp_core_load_template( apply_filters( 'bp_easyalbums_template_albums', 'easyalbums/galleries' ) );
					break;
					
				case "create":
					bp_easyalbums_screen_create();
				break;
				
				case "edit":
					//die("here");
					bp_easyalbums_action_cincopa();
				break;
				
				case "info":
					bp_core_load_template( apply_filters( 'bp_easyalbums_template_create', 'easyalbums/create' ) );
				break;
				
				case "delete":
					bp_easyalbums_action_delete();
				break;
					
				case "view":
					bp_core_load_template( apply_filters( 'bp_easyalbums_template_albums', 'easyalbums/gallery' ) );
				break;
					
				default:
					//do_action( 'bp_easyalbums_screen_albums' );
					bp_core_load_template( apply_filters( 'bp_easyalbums_template_albums', 'easyalbums/galleries' ) );
				break;	
			}
		}
	}

	
	function bp_easyalbums_screen_create() {
		global $bp,$wpdb;
		do_action( 'bp_easyalbums_screen_create' );
		
		$secret = get_site_option('bp_cp_secret');
		$cpr 	= new cpRequest($secret);
		$cpr->Add("uid", get_site_option('bp_cp_uid'));
		$cpr->Add("cmd", "creategallery");
		$cpr->Add("template", $_GET['tpl']);
		
		try{
			$res 	= $cpr->GetResponse();
			$xml 	= new SimpleXMLElement($res);
		}catch (Exception $e) {
			bp_easyalbums_defPage(__("Error creating an album please contact the site's administrator",'bp-easyalbums') );
		}
		
		$_fid 	= $xml->fid;
		$_uid 	= $bp->loggedin_user->id;
		$_type	= 6;
		$_title = "Untitled, Private";
		
		if (!empty($_fid)){
			$sql = $wpdb->prepare(
					"INSERT INTO {$bp->easyalbums->table_name} (
						uID,
						fID,
						gal_type,
						gal_title
					) VALUES (
						%s, %s, %s, %s
					)",
						$_uid,
						$_fid,
						$_type,
						$_title
				);
		
			$result = $wpdb->query( $sql );
			$user_link = bp_core_get_userlink( $bp->loggedin_user->id );
			bp_easyalbums_action_edit_album($_fid);
			
		}else{
			bp_easyalbums_defPage("Error creating an album please contact the site's administrator" );
		}
		
	}
	
	function bp_easyalbums_action_rename() {
		
		global $wpdb,$bp;
		
		if ($_POST['action']==$bp->easyalbums->rename_action && $bp->current_component == $bp->easyalbums->slug) {

			check_admin_referer('bp-easyalbum-create');
			
			$_type	= intval($_POST['cp_gallery_type']);
			$_title = $_POST['cp_gallery_title'];
			$_uid 	= $bp->loggedin_user->id;
			$_fid 	= $_POST['easyalbums_fid'];//$bp->action_variables[0];
			
			
			if (empty($_fid)){	
				bp_easyalbums_defPage("Your album could not be saved");
				return false;
			}
			
			$sql 		= "SELECT * FROM {$bp->easyalbums->table_name} WHERE uID='$_uid' AND fID='$_fid' AND status='1'";
			$result 	= $wpdb->get_row( $sql );
			$published	= $result->published;
			
			if($_type=='0' && $published=='0'){
				
				$user_link 	= bp_core_get_userlink( $bp->loggedin_user->id );
				$albumLink = sprintf( '<a href="%s">'.$_title.'</a>', wp_nonce_url(bp_displayed_user_domain() . bp_current_component() . "/?action=view&fid=$_fid", 'bp_easyalbum_screen_create' ));
				$_published = bp_easyalbums_publish_to_activity($user_link,$albumLink,$_fid);
				bp_core_add_message( __( "Album","bp-easyalbums")." ".$_title." ".__("has been publish to the activity stream!", 'bp-easyalbums' ), 'success' );	
				$sql = $wpdb->prepare(
						"UPDATE {$bp->easyalbums->table_name} 
							SET 
								gal_type='%s',
								gal_title='%s',
								published='%s'
							WHERE 
								fID='%s' AND uID='%s'",
							$_type,
							$_title,
							$_published,
							$_fid,
							$_uid
					);
					
			}else{
			
				$sql = $wpdb->prepare(
						"UPDATE {$bp->easyalbums->table_name} 
							SET 
								gal_type='%s',
								gal_title='%s'
							WHERE 
								fID='%s' AND uID='%s'",
							$_type,
							$_title,
							$_fid,
							$_uid
					);
					
			}
			
			$result = $wpdb->query( $sql );
			
			bp_core_load_template( apply_filters( 'bp_easyalbums_template_albums', 'easyalbums/galleries' ) );
			
		}
		
	}
	add_action('wp','bp_easyalbums_action_rename',3);
	
	function bp_easyalbums_action_delete() {
		global $bp,$wpdb;
		
		if ( $bp->current_component == $bp->easyalbums->slug && "delete" == $_GET['action']  ) {
			
			$fid 		= $_GET['fid'];
			$secret 	= get_site_option('bp_cp_secret');
			$cid 		= $bp->easyalbums->cincopaId;
			
			$cpr 	= new cpRequest($secret);
			
			$cpr->Add("fid",$fid);
			$cpr->Add("uid",$cid);
			$cpr->Add("cmd","deletegallery");
			$uid = $bp->loggedin_user->id;
			$res 	= $cpr->GetResponse();
			
			$xml 	= new SimpleXMLElement($res);
			$_result 	= $xml->result;
			if ($_result=="ok"){
				
				$sql = $wpdb->prepare(
						"UPDATE {$bp->easyalbums->table_name} 
							SET status='0' 
							WHERE uID='%s' AND fID='%s'
							LIMIT 1",
							$uid,
							$fid
					);
		
				$result = $wpdb->query( $sql );
			}else{
				bp_easyalbums_defPage( __("Can't delete this album","bp-easyalbums") );
			}
			
		}else{}
		bp_core_load_template( apply_filters( 'bp_easyalbums_template_albums', 'easyalbums/galleries' ) );
	}
	
	function bp_easyalbums_action_cincopa() {
		global $bp,$wpdb;
		
		$_uid 	= $bp->loggedin_user->id;
		$fid = $_GET['fid'];
		$sql = "SELECT * FROM {$bp->easyalbums->table_name} WHERE uID='$_uid' AND fID='$fid' AND status='1' ORDER BY ID";
		$result = $wpdb->get_results( $sql );
		$count = count($result);
		if(!empty($count) || current_user_can('administrator')){
		
			$fid = $_GET['fid'];
			$secret = get_site_option('bp_cp_secret');
			
			$cpr 	= new cpRequest($secret);
			$cpr->Add("fid",$fid);
			$cpr->Add("uid",$bp->easyalbums->cincopaId);
			
			$sig 	= ($cpr->getSig());
			
			$bp->easyalbums->currentAlbum = $fid;
			$bp->easyalbums->sig = $sig;
		
			bp_core_load_template( apply_filters( 'bp_easyalbums_template_onsave', 'easyalbums/wizard' ) );
			
		}else{
		
			bp_core_load_template( apply_filters( 'bp_easyalbums_template_albums', 'easyalbums/galleries' ) );
			
		}
		
	}
	
	function bp_easyalbums_action_edit_album($fid=0) {
		
		global $wpdb,$bp;
		
		if (empty($fid)){
			bp_easyalbums_defPage(__("Error creating an album please contact the site's administrator","bp-easyalbums") );
		}else{
			if ( $bp->current_component == $bp->easyalbums->slug  ) {
				$secret = get_site_option('bp_cp_secret');
				$cpr 	= new cpRequest($secret);
				$cpr->Add("fid",$fid);
				$cpr->Add("uid",$bp->easyalbums->cincopaId);
				$sig 	= ($cpr->getSig());
				$bp->easyalbums->currentAlbum = $fid;
				$bp->easyalbums->sig = $sig;
				bp_core_load_template( apply_filters( 'bp_easyalbums_template_onsave', 'easyalbums/wizard' ) );
			}
		}
	}


function itayDebug($str){
	if(defined("ITAY_DEBUG")){
		//echo "\n<!--DEBUG:\n$str\n-->\n";
	}
}

?>