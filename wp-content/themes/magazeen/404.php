<?php
/**
 * @package WordPress
 * @subpackage Magazeen_Theme
 */

get_header();
?>

<div id="main-content" class="clearfix">
	
		<div class="container">
	
			<div class="col-580 left">
						
				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			
					<div class="post-meta clearfix">
				
						<h3 style="font-size:26px;" class="post-title left">Esta p&aacute;gina no existe</h3>
						
						<p style="font-size:16px; clear:both; padding-top:10px;">
							<span><strong>Error 404</strong></span>
						</p>
						
					</div><!-- End post-meta -->
					
					<div class="post-box">
					
						<div class="post-content">
					
							
							<p>Utilice el buscador que se encuentra en la barra lateral.</p>
							
							
							
							
								
							
														
						</div><!-- End post-content -->
											
					</div><!-- End post-box -->
					
				</div><!-- End post -->

			</div><!-- End col-580 (Left Column) -->
			
			<div class="col-340 right">
			
				<ul id="sidebar">
				
					<?php get_sidebar(); ?>
					
				</ul><!-- End sidebar -->   
								
			</div><!-- End col-340 (Right Column) -->
			
		</div><!-- End container -->
		
	</div><!-- End main-content -->

<?php get_footer(); ?>
