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

				<h4 class='easyalbums_h4'><?php _e( 'Edit Album:', 'bp-easyalbums' ) ?></h4>
				
				<div class="void"></div>
				<?php 
				
				global $bp;
				
				$priv_str = array(
					0 => __('Public','bp-easyalbum'),
					2 => __('Registered members','bp-easyalbum'),
					4 => __('Only friends','bp-easyalbum'),
					6 => __('Private','bp-easyalbum'),
				);
				
				$aID 		= $_GET['fid'];
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
				
				$sql = "SELECT * FROM {$bp->easyalbums->table_templates} ORDER BY indx";
				$tpls = $wpdb->get_results( $sql );
				$types_html = "";
				
				foreach($tpls as $value){
					$types_html .= "<option value='".$value->tpl."'>".$value->title."</option>";
				}
				
				
		?>
		
			<form method="post" enctype="multipart/form-data" action='<?php printf(  '%s', wp_nonce_url( bp_displayed_user_domain() . bp_current_component(), 'bp_easyalbums_screen_delete' ) ) ?>' name="bp-easyalbum-upload-form" id="bp-easyalbum-upload-form" class="standard-form">
       
				<input type="hidden" name="action" value="<?php echo $form_action ?>" />
				<input type='hidden' name='easyalbums_fid' value='<?php echo $aID?>' />
			
            	<div class='easyalbums_formField'>
					<label class='easy_albums_label'><?php _e('Title','bp-easyalbum') ?></label>
					<input type='text' name='cp_gallery_title' value='<?php echo $gal_title?>' class='easyalbums_input'/>
					<div class='void'></div>
				</div>
				<div class='easyalbums_formField'>
               		<label class='easy_albums_label'><?php _e('Visibility','bp-easyalbum') ?></label>
					<select name='cp_gallery_type' class='easyalbums_input'>
						<?php 
							foreach($priv_str as $k => $str){
								echo "<option value='$k'";
								if($k==$row->gal_type){
									 echo 'selected';
								}
								echo '>'.$str."</option>";
							}
							
						?>
					</select>
					<div class='void'></div>
            	</div>
            	
           		<input type="submit" name="submit" id="submit" value="<?php _e( 'Save', 'bp-easyalbum' ) ?>"/>

			<?php
				wp_nonce_field( 'bp-easyalbum-create' );
			?>
		</form>
			</div><!-- #item-body -->
		</div><!-- .padder -->
	</div><!-- #content -->

	<?php locate_template( array( 'sidebar.php' ), true ) ?>

<?php get_footer() ?>