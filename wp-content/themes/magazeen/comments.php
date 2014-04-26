<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */

// Do not delete these lines
	if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
		die ('Please do not load this page directly. Thanks!');

	if ( post_password_required() ) { ?>
		<p class="nocomments">This post is password protected. Enter the password to view comments.</p>
	<?php
		return;
	}
?>

	<br />

<div id="comments" class="post-box">
					
	<div class="comment-content">
						
		

		<?php if ( have_comments() ) : ?>
			<ol class="commentlist clearfix">
				<?php wp_list_comments( 'callback=magazeen_comment' ); ?>
			</ol>
		<?php else : ?>
			<?php if ('open' == $post->comment_status) : ?>

			<?php else : ?>
				<p class="nocomments">Comentarios cerrados.</p>
			<?php endif; ?>
		<?php endif; ?>
		
	</div>
	
</div>
	

	
	<?php echo $page ?>
	
<?php if( get_previous_comments_link() || get_next_comments_link() ) : ?>
	
<div class="navigation arial clearfix">
	<div class="alignleft"><?php previous_comments_link(); ?></div>
	<div class="alignright"><?php next_comments_link(); ?></div>
</div>

<?php endif; ?>

<?php if ('open' == $post->comment_status) : ?>

<div id="respond" <?php post_class( ); ?> style="margin-top:10px; padding:0px;">

	<div class="post-meta clearfix">
				
		<h3 class="post-title-small left">Deje un comentario</h3>
		
		<p class="post-info right">
			<small><?php cancel_comment_reply_link(); ?></small>
		</p>
						
	</div><!-- End post-meta -->

	<div class="post-box">
					
		<div class="page-content">
		
			<?php if ( get_option('comment_registration') && !$user_ID ) : ?>
			<p>You must be <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?redirect_to=<?php echo urlencode(get_permalink()); ?>">logged in</a> to post a comment.</p>
			<?php else : ?>
		
				<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">
				
					<?php if ( $user_ID ) : ?>
					
						<p>Conectado como <a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a>. <a href="<?php echo wp_logout_url(get_permalink()); ?>" title="Log out of this account">Desconectado &raquo;</a></p>
					
					<?php else : ?>
				
						<p><input type="text" name="author" id="author" value="<?php echo $comment_author; ?>" size="32" tabindex="1" class="input" <?php if ($req) echo "aria-required='true'"; ?> />
						<label for="author"><small>Nombre <?php if ($req) echo "(requerido)"; ?></small></label>
						</p>
						
						<p><input type="text" name="email" id="email" value="<?php echo $comment_author_email; ?>" size="32" tabindex="2" class="input" <?php if ($req) echo "aria-required='true'"; ?> />
						<label for="email"><small>Email (no ser&aacute; publicado) <?php if ($req) echo "(requerido)"; ?></small></label>
						</p>
						
						
					
					<?php endif; ?>
				
					<p><textarea name="comment" id="comment" cols="" rows="10" tabindex="4" class="input" style="width:98%; display:inline;"></textarea></p>
					
					<p><input name="submit" type="submit" class="submit-comment" id="submit" tabindex="5" value="Enviar comentario" />
					<?php comment_id_fields(); ?>
					</p>
					<?php do_action('comment_form', $post->ID); ?>
				
				</form>
				
				<br />
			
			<?php endif; // If registration required and not logged in ?>
			
		</div>
	
	</div>	
		
</div>
*Todos los post pueden ser comentados libremente. Sólo serán moderados aquellos que utilicen un lenguaje ofensivo o persigan fines diferentes al diálogo de ideas.
<?php endif; // if you delete this the sky will fall on your head ?>
