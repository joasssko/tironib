<?php if ( wptouch_api_server_down() ) { ?>
	<p class="api-warning round-6"><?php _e( "Oops! The license server cannot not be reached right now.", "wptouch-pro" ); ?><br /><?php _e( "If you continuously see this message, contact support.", "wptouch-pro" ); ?></p>
<?php } ?>