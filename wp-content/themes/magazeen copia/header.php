<?php
/**
 * @package WordPress
 * @subpackage Magazeen_Theme
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

<head profile="http://gmpg.org/xfn/11">
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />

	<title><?php wp_title('&laquo;', true, 'right'); ?> <?php bloginfo('name'); ?></title>
   



	<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
    
        <!--[if IE 7]>
<link rel="stylesheet" href="<?php bloginfo( 'template_directory' ); ?>/ie.css" type="text/css" />
<![endif]-->

	<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
	<link rel="alternate" type="application/atom+xml" title="<?php bloginfo('name'); ?> Atom Feed" href="<?php bloginfo('atom_url'); ?>" />
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
	
	<script src="<?php bloginfo( 'template_directory' ); ?>/js/pngfix.js"></script>
	<script src="<?php bloginfo( 'template_directory' ); ?>/js/jquery-latest.js"></script>
	<script src="<?php bloginfo( 'template_directory' ); ?>/js/effects.core.js"></script>
	<script src="<?php bloginfo( 'template_directory' ); ?>/js/functions.js"></script>
    
    <script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/superfish.js"></script> 
	
	<?php if ( is_singular() ) wp_enqueue_script( 'comment-reply' ); ?>

	<?php wp_head(); ?>
</head>

<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-4770680-3']);
  _gaq.push(['_setDomainName', 'none']);
  _gaq.push(['_setAllowLinker', true]);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>



<body>

	<div id="header">

		
		<div class="container clearfix">
		
			<div id="logo">
		<a href="<?php bloginfo( 'url' ); ?>" title="<?php bloginfo( 'name' ); ?>"><img src="<?php bloginfo( 'template_directory' ); ?>/images/logo.gif" /></a>
			
			
				
			</div><!-- End logo -->
            	<div id="navigation">
                
                <?php wp_nav_menu( array( 'container' => 'none', 'menu_class' => 'nav1 clearfix nav pages sf-menu' , 'theme_location' => 'primary' ) ); ?>
	
			<!--<ul class="pages">
				<li<?php if( is_home() ) : ?> class="current_page_item"<?php endif; ?>><a href="<?php bloginfo( 'url' ); ?>" title="<?php bloginfo( 'title' ); ?>">INICIO</a></li>
				<?php wp_list_pages( 'title_li=&exclude= 363,368,371,373,375,377,379,381,383,385,387,389,391,393,395,397,399,401,403,405,407,409,411,413,415,417,419,421,423,425,427,429,431,433,435,437,439,441,443,445,447,449,451&depth=1' ); ?>
			</ul> -->
            </div>
			
		
		
		</div><!-- End Container -->
		
	</div><!-- End header -->
	
	
	
		<div class="container clearfix">
	
			
			
			
		</div><!-- End container -->
		
<!-- End navigation -->
	
	<div id="latest-dock">
	
		<div class="dock-back container clearfix">
		
		
		
			<ul id="dock">
				<?php
					$dock = new WP_Query();
					$dock->query( 'showposts=9' );
					while( $dock->have_posts() ) : $dock->the_post();
				?>
				<li>
					<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>">
					<img src="<?php bloginfo( 'template_directory' ); ?>/timthumb.php?src=<?php echo get_post_meta( $post->ID, "image_value", true ); ?>&amp;w=69&amp;h=54&amp;zc=1" alt="<?php the_title(); ?>" />
					</a>
					<span><?php the_title(); ?></span>
				</li>
				<?php
					endwhile;
				?>
			</ul>
					
		</div><!-- End container -->
	
	</div><!-- End latest-dock -->