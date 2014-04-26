<?php
/**
 * @package WordPress
 * @subpackage Magazeen_Theme
 */

/*
Template Name: noticias
*/
?>

<?php get_header(); ?>

	<div id="main-content" class="clearfix">
	
		<div class="container">
        
       
	
			<div class="col-580 left">
            
            <h3 class="post-title left" style="margin-bottom:15px;">EN QU&Eacute; ESTAMOS</h3>
			
<?php if (have_posts()) : ?>
     <?php $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
            query_posts("category_name=En que estamos&paged=$paged"); ?>
        <?php while (have_posts()) : the_post(); ?>
			<div class="post" id="post-<?php the_ID(); ?>">
				
                
				<div class="entry">
					<div class="both">
               <div class="post-image2">
								<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><img src="<?php bloginfo( 'template_directory' ); ?>/timthumb.php?src=<?php echo get_post_meta( $post->ID, "image_value", true ); ?>&amp;w=165&amp;h=130&amp;zc=1" alt="<?php the_title(); ?>" /></a>
							</div>
                  
                  
                  
					<div class="breves">
                    <table border="0">
  <tr>
    <td valign="top"><h3 class="post-title left" style="clear:both;"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3></td>
  </tr>
</table>
<table class="registro2"  border="0">
  <tr>
    <td><span class="link-autor">Publicado por  <?php the_author_posts_link(); ?> en <?php the_time( 'F j, Y' ) ?> | Categor&iacute;a: <?php the_category(', ') ?></span> </td>
    <td valign="top"></td>
  </tr>
</table>

					<?php the_content_rss('', TRUE, '', 20); ?><a style="font-size:14px;" href="<?php the_permalink() ?>" class="links"> Seguir leyendo</a></div>
					</div>
				</div>
			</div>
			<?php endwhile; endif; ?>
          
          <?php wp_pagenavi(); ?>
				
			
				
			</div><!-- End col-580 (Left Column) -->
			
			<div class="col-340 right">
			
				<ul id="sidebar">
				
				<?php include (TEMPLATEPATH . '/sidebar_noticias.php'); ?>
					
				</ul><!-- End sidebar -->   
								
			</div><!-- End col-340 (Right Column) -->
			
		</div><!-- End container -->
		
	</div><!-- End main-content -->

<?php get_footer(); ?>
