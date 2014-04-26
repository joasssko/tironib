<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */

get_header(); ?>

	<div id="main-content" class="clearfix">
	
		<div class="container">
        
       
	
			<div class="col-580 left">
			<h1>DESTACADO</h1>
				<?php
					query_posts( 'showposts=1&cat=31' );
					if (have_posts()) : 
						while (have_posts()) : the_post(); $category = get_the_category();
				?>
			
				<div <?php post_class(); ?>>
			
					<div class="post-meta clearfix">
				
						<h3 class="post-title left"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
						
						
						
					</div><!-- End post-meta -->
					<div id="publicado">
                    
                 <table class="registro" width="600" border="0">
  <tr>
    <td><span class="link-autor">Publicado por  <?php the_author_posts_link(); ?> en <?php the_time( 'F j, Y' ) ?> | Categor&iacute;a: <?php the_category(', ') ?></span> 
							</td>
    <td valign="top"><div class="comentarios"><?php comments_popup_link(__( '0 ' ), __( '1 ' ), __( '% ' )); ?></div></td>
  </tr>
</table>

							
				  </div>
			  <div class="post-box">
					
						<div class="post-content">
					
						
							
							<?php if( get_post_meta( $post->ID, "image_value", true ) ) : ?>
								<div class="post-image">
								<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><img src="<?php bloginfo( 'template_directory' ); ?>/timthumb.php?src=<?php echo get_post_meta( $post->ID, "image_value", true ); ?>&amp;w=560&amp;h=340&amp;zc=1" alt="<?php the_title(); ?>" /></a>
							</div>						

							
							<?php endif; ?>
							
							<div class="post-intro">
							
								<?php the_excerpt( '' ); ?>
								
							</div><!-- End post-intro -->
							
						</div><!-- End post-content -->
						
						<div class="post-footer clearfix">
						
							<div class="continue-reading">
								<a href="<?php the_permalink() ?>#more-<?php the_ID(); ?>" rel="bookmark" title="Seguir leyendo... <?php the_title_attribute(); ?>">Seguir leyendo...</a>
							</div>
								<div id="otras">
                             <h2 >OTRAS NOTICIAS</h2></div>	
                             
					  <div class="category-menu">
														
								<div class="category clearfix">
									<div><a href="#"><span class="indicator"></span> <?php echo $category[0]->cat_name; ?></a></div>
								</div>
																
								<div class="dropdown">
								
									<ul class="cat-posts">
										<?php
											$posted = get_posts( "category=" . $category[0]->cat_ID );
											if( $posted ) :
												foreach( $posted as $post ) : setup_postdata( $posted );
										?>
										<li><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a><span><?php the_time( ' F j, Y' ) ?></span></li>
										<?php
												endforeach;
											endif;
										?>
										<li class="view-more"><a href="<?php echo get_category_link( $category[0]->cat_ID ); ?>" class="view-more">Ver m&aacute;s &raquo;</a></li>
									</ul>
									
								</div><!-- End dropdown -->
							
							</div><!-- End category -->
											
						</div><!-- End post-footer -->
					
					</div><!-- End post-box -->
					
				</div><!-- End post -->
				
				<?php
						endwhile;
					endif;
				?>
				
				<?php
					query_posts( 'showposts=4&offset=1&cat=4' );
					if (have_posts()) : $counter = 0;
						while (have_posts()) : the_post(); $category = get_the_category();
						
						if( $counter % 2 == 0 )
							$end = "";
						else
							$end = "last";
				?>
				
				<div  <?php post_class( 'single2 ' . $end ); ?>>
			
                 
            
			  <div class="post-meta clearfix">
                   
				
						<h3 class="post-title left"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
						
					</div><!-- End post-meta -->
					
					<div class="post-box">
					
						<div class="post-content">
					
						                            <table class="registro" width="275" border="0">
  <tr>
    <td><span class="link-autor">Publicado por  <?php the_author_posts_link(); ?> en <?php the_time( 'F j, Y' ) ?> | Categor&iacute;a: <?php the_category(', ') ?></span> </td>
    <td valign="top"><div class="comentarios"><?php comments_popup_link(__( '0 ' ), __( '1 ' ), __( '% ' )); ?></div></td>
  </tr>
</table>
							
							<?php if( get_post_meta( $post->ID, "image_value", true ) ) : ?>
							
							<div class="post-image">
								<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><img src="<?php bloginfo( 'template_directory' ); ?>/timthumb.php?src=<?php echo get_post_meta( $post->ID, "image_value", true ); ?>&amp;w=260&amp;h=170&amp;zc=1" alt="<?php the_title(); ?>" /></a>
							</div>
                            

							
							<?php endif; ?>
							
							<div class="post-intro">
							
								<?php the_excerpt( '' ); ?>
								
							</div><!-- End post-intro -->
							
						</div><!-- End post-content -->
						
						<div class="post-footer clearfix">
						
							<div class="continue-reading">
								<a href="<?php the_permalink() ?>#more-<?php the_ID(); ?>" rel="bookmark" title="Seguir leyendo<?php the_title_attribute(); ?>">Seguir leyendo...</a>
							</div>
																				
						</div><!-- End post-footer -->
					
					</div><!-- End post-box -->
					
				</div><!-- End post -->
                
               
				
				<?php
					// Clear the left float to allow for different heights
					if( $counter % 1 != 0 )
						echo'<div style="clear:left;"> </div>';
				?>
				
				<?php
						$counter++;
						endwhile;
					endif;
				?>
				
					
			
				
				
				
			</div><!-- End col-580 (Left Column) -->
			
			<div class="col-340 right">
			
				<ul id="sidebar">
				
					<?php get_sidebar(); ?>
					
				</ul><!-- End sidebar -->   
								
			</div><!-- End col-340 (Right Column) -->
			
		</div><!-- End container -->
		
	</div><!-- End main-content -->

<?php get_footer(); ?>
