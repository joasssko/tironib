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
				$_uid 	= $bp->displayed_user->id;
				$_fid 	= $_GET['fid'];
				
				$sql = "SELECT * FROM {$bp->easyalbums->table_name} WHERE uID='$_uid' AND fID='$_fid' AND gal_type<=$perms AND status='1' ORDER BY ID";
								
				$gal = $wpdb->get_row( $sql );
					
					$chars = 18;
					$galTitle= (strlen($gal->gal_title)>$chars)?substr(stripslashes($gal->gal_title),0,$chars)."...":stripslashes($gal->gal_title);
					$_fid = $gal->fID;
					if(!empty($_fid)){
						$title = stripslashes($gal->gal_title);
						$body = easyAlbums_cincopa_plugin("[cincopa $_fid]") ;
					}else{
						$title = __('This Album is Private','bp-easyalbums');
					}
						
				?>
					
				<div style='float:left'><h4 class='easyalbums_h4'><?php echo $title ?></h4></div>
				<div style='float:right;margin-top:10px;'>
					<a href='<?php echo bp_displayed_user_domain().bp_current_component()  ?>' ><? _e('Back to albums >','bp-easyalbums')?></a>
				</div>
				<div class="void"></div>
				<?
					echo $body; 
				?>

			</div><!-- #item-body -->

		</div><!-- .padder -->
	</div><!-- #content -->

	<?php locate_template( array( 'sidebar.php' ), true ) ?>

<?php get_footer() ?>