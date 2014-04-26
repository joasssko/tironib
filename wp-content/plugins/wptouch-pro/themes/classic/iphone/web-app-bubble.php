<?php if ( show_webapp_notice() ) { ?>
	<div id="web-app-overlay">
		<img src="<?php wptouch_bloginfo( 'template_directory' ); ?>/images/web-app-bubble-arrow.png" alt="bubble-arrow" id="bubble-arrow" />
		<a href="#" id="close-wa-overlay">X</a>
		<img src="<?php  echo wptouch_get_site_menu_icon( WPTOUCH_ICON_BOOKMARK ); ?>" alt="bookmark-icon" id="bookmark-icon" />
		<h2><?php wptouch_bloginfo( 'site_title' ); ?></h2>
		<h3><?php _e( "esta optimizado para moviles", "wptouch-pro" ); ?></h3>
		<p><?php echo sprintf( __( "Guarda esta pagina como una aplicacion en tu pantalla de inicio.", "wptouch-pro" ), wptouch_get_bloginfo( 'site_title' ) ); ?></p>
		<p><?php _e( "Toca el boton abajo y luego selecciona Agregar a Pantalla de Inicio.", "wptouch-pro" ); ?></p>
	</div>
<?php } ?>
