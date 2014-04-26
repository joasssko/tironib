<?php get_header() ?>

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
					global $bp;
					$url = "http://www.cincopa.com/media-platform/api/edit_gallery.aspx?fid=".$bp->easyalbums->currentAlbum."&uid=".$bp->easyalbums->cincopaId."&sig=".$bp->easyalbums->sig;
				?>
				
				
				<?php if("create" == $_GET['action']){?>
					<div style='float:right;margin-top:10px;'>
						<a href='<?php printf( '%s', wp_nonce_url( bp_displayed_user_domain() . bp_current_component() . '?action=info&fid='.$bp->easyalbums->currentAlbum, 'bp_easyalbums_screen_delete' ) ) ?>' id="finishBT" class='albumsBT'><? _e('Publish album','bp-easyalbums')?></a>
					</div>
				<?php }else{ ?>
					<div style='float:right;margin-top:10px;'>
						<a href='<?php printf( '%s', wp_nonce_url( bp_displayed_user_domain() . bp_current_component(), 'bp_easyalbums' ) ) ?>' ><? _e('Back to albums >','bp-easyalbums')?></a>
					</div>
				<?php } ?>
				<h4 class='easyalbums_h4'><?php _e( 'Image Manager:', 'bp-easyalbums' ) ?></h4>
				<div class="void"></div>
		
				<?php 	
				
					$priv_str = array(
						0 => __('Public','bp-easyalbum'),
						2 => __('Registered members','bp-easyalbum'),
						4 => __('Only friends','bp-easyalbum'),
						6 => __('Private','bp-easyalbum'),
					);
					
					$aID 		= $bp->action_variables[0];
					$uID 		= $bp->loggedin_user->id;
					
					if(!empty($aID)){
						
						$sql = "SELECT * FROM {$bp->easyalbums->table_name} WHERE fID='$aID' AND uID='$uID' AND status='1' ORDER BY ID";
						$result = $wpdb->get_results( $sql );
						$row = $result[0];
						
						
						$gal_title = htmlentities(stripslashes($row->gal_title),ENT_QUOTES);
						$gal_type = $row->gal_type;
						$form_action = $bp->easyalbums->rename_action;
						
					}else{
						
						$gal_title = "";
						$gal_type = "0";
						$form_action = $bp->easyalbums->create_action;
					}
					
		?>
		
				<iframe style='width:700px;height:500px;' src='<?=$url ?>'></iframe>
			
			</div><!-- #item-body -->

		</div><!-- .padder -->
	</div><!-- #content -->

	<?php locate_template( array( 'sidebar.php' ), true ) ?>

<?php get_footer() ?>