	<!-- The tab Icon Bar -->

	
	<div id="menu-container">
		<div id="menu-tab1">
			<h2><?php _e( "Menu", "wptouch-pro" ); ?></h2>
			<!-- The WPtouch Page Menu -->		
			<?php wptouch_show_menu(); ?>
		</div>

		<?php if ( classic_mobile_show_categories_tab() ) { ?>
			<div id="menu-tab2">
				<h2><?php _e( "Categories", "wptouch-pro" ); ?></h2>
				<?php wptouch_ordered_cat_list(); ?>
			</div>
		<?php } ?>

		<?php if ( classic_mobile_show_tags_tab() ) { ?>
			<div id="menu-tab3">
				<h2><?php _e( "Tags", "wptouch-pro" ); ?></h2>
				<?php wp_tag_cloud( 'smallest=13&largest=13&unit=px&number=20&order=asc&format=list' ); ?>
			</div>
		<?php } ?>
		
		<?php if ( wptouch_prowl_direct_message_enabled() ) { ?>
		<div id="menu-tab4">
			 <h4><?php _e( "Send a Message", "wptouch-pro" ); ?></h4>
			 <p><?php _e( "This message will be pushed to the admin's iPhone instantly.", "wptouch-pro" ); ?></p>
			 
			 <form id="prowl-direct-message" method="post" action="">
			 	<p>
			 		<input name="prowl-msg-name" id="prowl-msg-name" type="text" tabindex="3" />
			 		<label for="prowl-msg-name"><?php _e( 'Name', 'wptouch-pro' ); ?></label>
			 	</p>
				<p>
					<input name="prowl-msg-email" id="prowl-msg-email" autocapitalize="off" type="text" tabindex="4" />
					<label for="prowl-msg-email"><?php _e( 'E-Mail', 'wptouch-pro' ); ?></label>
				</p>
				<textarea name="prowl-msg-message" tabindex="5"></textarea>
				<input type="submit" name="prowl-submit" value="<?php _e( 'Send Now', 'wptouch-pro' ); ?>" id="prowl-submit" tabindex="6" />
				<input type="hidden" name="wptouch-prowl-nonce" value="<?php echo wp_create_nonce( 'wptouch-prowl' ); ?>" />			
			 </form>
		</div>
		<?php } ?>
		

	</div><!-- #tab-bar -->
