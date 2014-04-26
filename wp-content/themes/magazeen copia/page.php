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
						while (have_posts()) : the_post(); $category = get_the_category();
				?>
			
				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			
					<div class="post-meta clearfix">
				
						<h3 class="post-title left"><?php the_title(); ?></h3>
						
					
						
					</div><!-- End post-meta -->
					
					<div class="post-box">
					
						<div class="page-content">
					
							<?php the_content( '' ); ?>
							
						</div><!-- End post-content -->
											
					</div><!-- End post-box -->
					
				</div><!-- End post -->
				
				<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
				
				<?php
						endwhile;
					endif;
					wp_reset_query();
				?>
				
			</div><!-- End col-580 (Left Column) -->
			
			<div class="col-340 right">
			
				<ul id="sidebar">
				
					<?php include (TEMPLATEPATH . '/sidebar_interno.php'); ?>
					
				</ul><!-- End sidebar -->   
								
			</div><!-- End col-340 (Right Column) -->
			
		</div><!-- End container -->
		
	</div><!-- End main-content -->

<?php get_footer(); ?>
