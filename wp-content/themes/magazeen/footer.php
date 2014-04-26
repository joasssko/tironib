<?php
/**
 * @package WordPress
 * @subpackage Magazeen_Theme
 */
?>

	<div id="footer">
    
      	<div id="navigation2">
			
            <?php wp_nav_menu( array( 'container' => 'none', 'menu_class' => 'nav2 clearfix pages' , 'theme_location' => 'secondary' ) ); ?>
            
			
      </div>
	
		<div class="container footer-divider clearfix">
		
			<!-- End categories -->
			
			
			
				<div id="contacto">
				
<p class="copyright">
			    Edificio Matta, Nueva Las Condes, Rosario Norte 532, Piso 4 &middot; Santiago de Chile<br /> 
			    Tel.: (56 2) 2 290 70 00 &middot; Fax: (56 2) 2 290 70 80 <br />
			    Contacto: <img src="http://web.tironi.cl/wp-content/uploads/2011/03/mail.gif" /> <a href="mailto:tironi@tironiasociados.com">tironi@tironiasociados.com</a></p>

<p class="copyright">
			   <br>TIRONI <a href="<?php bloginfo( 'rss2_url' ); ?>"  title="Subscribirse para <?php bloginfo( 'name' ); ?> RSS"><img src="http://web.tironi.cl/wp-content/uploads/2011/03/rss.gif" /></a> RSS Feed				</p>
				</div>
				<a href="http://www.aver.cl" target="_blank"><img src="http://web.tironi.cl/aver.gif" alt="Aver Diseño" border="0" /></a>
		  <!-- End about -->
		
		</div><!-- End container -->
	
	</div><!-- End footer -->
	
	<div id="link-back">
	
		<div class="container clearfix">
		
		<!-- End donators -->
			
			
		
		</div>
	
	</div><!-- End link-back -->
	
	<?php wp_footer(); ?>
	
</body>
</html>