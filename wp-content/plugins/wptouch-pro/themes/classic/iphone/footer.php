					</div><!-- #content -->
						
					<?php do_action( 'wptouch_body_bottom' ); ?>
							
					<?php if ( wptouch_show_switch_link() ) { ?>
						<div id="switch">
							<?php _e( "Version Movil", "wptouch-pro" ); ?> | <a href="<?php wptouch_the_mobile_switch_link(); ?>"><?php _e( "Cambiar a Version Normal", "wptouch-pro" ); ?></a>
						</div>
					<?php } ?>
							
					<div class="<?php wptouch_footer_classes(); ?>">
						<?php wptouch_footer(); ?>
					</div>
		
					<?php do_action( 'wptouch_advertising_bottom' ); ?>
				</div> <!-- #inner-ajax -->
			</div> <!-- #outer-ajax -->
<?php if ( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] == 0 ) { ?>			
			<?php include_once('web-app-bubble.php'); ?>
			<!-- <?php echo WPTOUCH_VERSION; ?> -->
		</body>
	</html>
<?php } ?>