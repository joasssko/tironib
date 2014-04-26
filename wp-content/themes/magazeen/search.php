<?php
/**
 * @package WordPress
 * @subpackage Magazeen_Theme
 */

get_header(); ?>

	<div id="main-content" class="clearfix">
	
		<div class="container">
	
			<div class="col-580 left">
				
				<?php
					if (have_posts()) :
				?>
				
				<div <?php post_class(); ?>>
			
					<div class="post-meta clearfix">
				
						<h3 class="post-title">Resultados de b&uacute;squeda para <?php the_search_query(); ?></h3>
						
						<p class="post-info right">
							
						</p>
						
					</div><!-- End post-meta -->
					
					<div class="post-box">
					
						<div class="post-content">
						
							
								
						</div><!-- End post-content -->
						
						
					
					</div><!-- End post-box -->
					
				</div><!-- End post -->
					
				<?php		
					while (have_posts()) : the_post(); $category = get_the_category();
				?>
				
				<div <?php post_class( ); ?>>
			
					<div class="post-meta clearfix">
				
						<h3 class="post-title-small left"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
						
						<table class="registro" width="565" border="0" style="clear:both; border-bottom: 1px dotted #666666;margin-bottom: 10px;">
  <tr>
    <td><span class="link-autor">Publicado por  <?php the_author_posts_link(); ?></span> en
							<?php the_time( 'F j, Y' ) ?></td>
    <td align="right" valign="top"></td>
  </tr>
</table>
						
					</div><!-- End post-meta -->
					
				</div><!-- End archive -->
				
				<?php
						endwhile;
				?>
					<div class="navigation clearfix">
						    <?php wp_pagenavi(); ?>
					</div>
				<?php
					else : 
				?>
				
					<div <?php post_class(); ?>>
			
						<div class="post-meta clearfix">
					
							<h3 class="post-title">No hay resultados para esta b&uacute;squeda</h3>
							
							<p style="font-size:14px;" >
								Usted est&aacute; buscando <?php the_search_query(); ?>. Por favor int&eacute;ntelo nuevamente.
							</p>
							
						</div><!-- End post-meta -->
						
						<div class="post-box">
						
							<div class="post-content">
							
								
								
							
								
							
									
							</div><!-- End post-content -->
							
							<div class="post-footer">
							
							
								
							</div>
						
						</div><!-- End post-box -->
						
					</div><!-- End post -->
				
				<?php
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