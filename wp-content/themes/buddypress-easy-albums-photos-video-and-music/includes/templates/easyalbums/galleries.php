<?php 
get_header(); 
global $bp;
?>

	<div id="content">
		<div class="padder">
			<div id="item-header">
				<?php locate_template( array( 'members/single/member-header.php' ), true ) ?>
			</div>
			<div id="item-nav">
				<div class="item-list-tabs no-ajax" id="object-nav">
					<ul>
						<?php bp_get_displayed_user_nav() ?>
					</ul>
				</div>
			</div>
			<div id="item-body">
				<?php
					if(bp_is_my_profile()){ 
						$user_fullname = __("Your",'bp-easyalbums');
					}else{
						$user_fullname = $bp->displayed_user->fullname.__("'s",'bp-easyalbums');
					}						
				?>

				<div style='float:left'><h4 class='easyalbums_h4'><?php echo $user_fullname." ".__( "Albums:", 'bp-easyalbums' ) ?></h4></div>
				<?php
					if(bp_is_my_profile()){ 	
				?>
						<div style='float:right;margin-top:10px;'>
							<a href='javascript:void(0)' id="createBT" onclick='jQuery("#easyalbums_createDropDown").animate({height:"toggle"},250)' class='albumsBT'><? _e("+ Create Album",'bp-easyalbums')?></a>
							<div id='easyalbums_createDropDown'>
							<?php
								
								$sql = "SELECT * FROM {$bp->easyalbums->table_templates} ORDER BY indx";
								$tpls = $wpdb->get_results( $sql );
								$types_html = "";
								foreach($tpls as $value){
									printf( '<a href="%s">'.$value->title.'</a>' , wp_nonce_url( bp_displayed_user_domain() . bp_current_component() . "?action=create&tpl=".$value->tpl, 'bp_easyalbum_screen_create' ) );
								}
							?>
							</div>
						</div>
				<?php 
					}
				?>
				<div class="void"></div>
				<?php 
				
				
				function getUserPermissions(){
					global $bp;
					$p = 0;
					if ( is_user_logged_in()){
						$p =2;
					}
					
					$friend_status = friends_check_friendship_status( $bp->loggedin_user->id, $bp->displayed_user->id );
					if ($friend_status=='is_friend'){
						$p = 4;
					}
					
					
					if ($bp->loggedin_user->id == $bp->displayed_user->id || current_user_can('administrator')){
					
						$p =6;
					}
					
					return $p;
				}

				$perms = getUserPermissions();
				$editClass = ($perms==6)?"easyalbums_gallerytitle_edit":"";
				$_uid 	= $bp->displayed_user->id;
				$sql = "SELECT * FROM {$bp->easyalbums->table_name} WHERE uID='$_uid' AND gal_type<=$perms AND status='1' ORDER BY ID";
				$result = $wpdb->get_results( $sql );
				if (count($result)>0){
				
					foreach($result as $gal){
						
						$chars = 18;
						$galTitle= (strlen($gal->gal_title)>$chars)?substr(stripslashes($gal->gal_title),0,$chars)."...":stripslashes($gal->gal_title);
						?>
							
							<div class='easyalbums_galleriesholder' >
								<div class='easyalbums_thumbslider'>
									<div class="easyalbums_thumb" style='background-image:url("http://www.cincopa.com/media-platform/api/thumb.aspx?fid=<?php echo $gal->fID?>");' onclick="window.location='<?php printf( '%s', wp_nonce_url( bp_displayed_user_domain() . bp_current_component() ."/?action=view&fid=".$gal->fID, 'bp_easyalbums_screen_publish' ) ) ?>';"></div>
									<div class='easyalbums_gallerycontrol' 
										onMouseOver='var T=-jQuery(this).children(".easyalbums_gallerycontrol_inner").height();jQuery(this).stop().animate({top:T+"px"},150);'
										onMouseOut='jQuery(this).stop().animate({top:"0px"},150)'
									 >
										<div class='easyalbums_gallerytitle <?php echo $editClass ?>'><?php echo $galTitle; ?> </div>
									<?php
										if ($bp->displayed_user->id==$bp->loggedin_user->id || current_user_can('administrator')){
									?>
											<div class="easyalbums_gallerycontrol_inner" >
												<a href='<?php printf( '%s', wp_nonce_url( bp_displayed_user_domain() . bp_current_component() ."?action=view&fid=".$gal->fID, 'bp_easyalbums_screen_publish' ) ) ?>' ><? _e('View Album','bp-easyalbums')?></a>
												<a href='<?php printf( '%s', wp_nonce_url( bp_displayed_user_domain() . bp_current_component() . '?action=edit&fid='.$gal->fID, 'bp_easyalbums_screen_edit' ) ) ?>'><? _e('Image Manager','bp-easyalbums')?></a>
												<a href='<?php printf( '%s', wp_nonce_url( bp_displayed_user_domain() . bp_current_component() . '?action=info&fid='.$gal->fID, 'bp_easyalbums_screen_delete' ) ) ?>'><? _e('Album Info','bp-easyalbums')?></a>
												<a href='<?php printf( '%s', wp_nonce_url( bp_displayed_user_domain() . bp_current_component() ."?action=publish&fid=".$gal->fID, 'bp_easyalbums_screen_publish' ) ) ?>' onclick='return (confirm("The album will be published to the activity stream and will be available for view to all! are you sure?"))'><? _e('Publish to activity','bp-easyalbums')?></a>
												<a href='javascript:void(0)' onclick='var _a = confirm("<? _e('Are you sure you want to delete this album?','bp-easyalbums')?>");if(_a)window.location="<?php printf( '%s', wp_nonce_url( bp_displayed_user_domain() . bp_current_component() . '?action=delete&fid='.$gal->fID, 'bp_easyalbums_screen_delete' ) ) ?>";'><? _e('Delete Album','bp-easyalbums')?></a> 
											</div>
									<?php 
										}
									?>
									</div>
								</div><!--  end thumb slider -->
							</div>
							
						<?php 
						
					}
				}else{
					echo "<br/><br/><br/><br/><br/>";
				}
				?>
			</div><!-- #item-body -->
		</div><!-- .padder -->
	</div><!-- #content -->
	<?php locate_template( array( 'sidebar.php' ), true ) ?>
<?php get_footer() ?>