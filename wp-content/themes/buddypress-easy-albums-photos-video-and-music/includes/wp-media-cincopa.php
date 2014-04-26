<?php
/*
Plugin Name: Post video players, slideshow albums, photo galleries and music / podcast playlist
Plugin URI: http://www.cincopa.com/media-platform/wordpress-plugin.aspx
Description: Post rich videos and photos galleries from your cincopa account
Author: Cincopa 
Version: 1.104
*/


function ea_cpmp_plugin_ver()
{
	return 'wp1.104';
}

function ea_cpmp_afc()
{
	$cincopa_afc = get_site_option('CincopaAFC');
	
	if ($cincopa_afc == '')
		return '';
	
	return '&afc='.$cincopa_afc;
}

function ea_cpmp_url()
{
	return 'http://www.cincopa.com';
}

if (strpos($_SERVER['REQUEST_URI'], 'media-upload.php') && strpos($_SERVER['REQUEST_URI'], '&type=cincopa') && !strpos($_SERVER['REQUEST_URI'], '&wrt='))
{
	header('Location: '.ea_cpmp_url().'/media-platform/start.aspx?ver='.ea_cpmp_plugin_ver().ea_cpmp_afc().'&rdt='.urlencode(ea_cpmp_selfURL()));
	exit;
}

function ea_cpmp_selfURL()
{
	$s = empty ( $_SERVER ["HTTPS"] ) ? '' : ($_SERVER ["HTTPS"] == "on") ? "s" : "";

	$protocol =  strtolower ( $_SERVER ["SERVER_PROTOCOL"] );
	$protocol =  substr($protocol, 0, strpos($protocol, "/"));
	$protocol .= $s;

	$port = ($_SERVER ["SERVER_PORT"] == "80") ? "" : (":" . $_SERVER ["SERVER_PORT"]);
	$ret = $protocol . "://" . $_SERVER ['SERVER_NAME'] . $port . $_SERVER ['REQUEST_URI'];

	return $ret;
}

function ea_cpmp_pluginURI()
{
	return get_option('siteurl').'/wp-content/plugins/'.dirname(plugin_basename(__FILE__));
}

function ea_cpmp_WpMediaCincopa_init() // constructor
{
//		load_plugin_textdomain('wp-media-cincopa', PLUGINDIR.'/'.dirname(plugin_basename(__FILE__)));

	add_action('media_buttons', 'ea_cpmp_addMediaButton', 20);

	add_action('media_upload_cincopa', 'ea_cpmp_media_upload');
	// No longer needed in WP 2.6
	if ( !function_exists('wp_enqueue_style') )
	{
		add_action('admin_head_media_upload_type_cincopa', 'media_admin_css');
	}
      
	// check auth enabled
	//if(!function_exists('curl_init') && !ini_get('allow_url_fopen')) {}
}

function ea_cpmp_addMediaButton($admin = true)
{
	global $post_ID, $temp_ID;
	$uploading_iframe_ID = (int) (0 == $post_ID ? $temp_ID : $post_ID);

	$media_upload_iframe_src = get_option('siteurl').'/wp-admin/media-upload.php?post_id=$uploading_iframe_ID';

	$media_cincopa_iframe_src = apply_filters('media_cincopa_iframe_src', "$media_upload_iframe_src&amp;type=cincopa&amp;tab=cincopa");
	$media_cincopa_title = __('Add Cincopa photo', 'wp-media-cincopa');

	echo "<a class=\"thickbox\" href=\"{$media_cincopa_iframe_src}&amp;TB_iframe=true&amp;height=500&amp;width=640\" title=\"$media_cincopa_title\"><img src=\"".ea_cpmp_pluginURI()."/media-cincopa.gif\" alt=\"$media_cincopa_title\" /></a>";
}

function ea_cpmp_modifyMediaTab($tabs)
{
	return array(
		'cincopa' =>  __('Cincopa photo', 'wp-media-cincopa'),
	);
}

function ea_cpmp_media_upload()
{
	wp_iframe('ea_cpmp_media_upload_type_cincopa');
}


function ea_cpmp_media_upload_type_cincopa()
{
	global $wpdb, $wp_query, $wp_locale, $type, $tab, $post_mime_types;
	add_filter('media_upload_tabs', 'ea_cpmp_modifyMediaTab');
?>

<br />
<br />
<h2>&nbsp;&nbsp;Please Wait...</h2>

<script>

	function ea_cpmp_cincopa_stub()
	{
		var i = location.href.indexOf("&wrt=");

		if (i > -1)
		{
			top.send_to_editor(unescape(location.href.substring(i+5)));
		}

		top.tb_remove();
	}

	window.onload = ea_cpmp_cincopa_stub;

</script>

<?php
}

ea_cpmp_WpMediaCincopa_init();

define("CINCOPA_REGEXP", "/\[cincopa ([[:print:]]+?)\]/");

function ea_cpmp_cincopa_tag($fid)
{
	return ea_cpmp_plugin_callback(array($fid));
}

function ea_cpmp_plugin_callback($match)
{
	$uni = uniqid('');
	$ret = '
<!-- Cincopa WordPress plugin '.ea_cpmp_plugin_ver().': http://www.cincopa.com/media-platform/wordpress-plugin.aspx -->
<div id="cp_widget_'.$uni.'"><img src="http://www.cincopa.com/media-platform/runtime/loading.gif" style="border:0;" alt="Cincopa WordPress plugin" /></div>
<script src="http://www.cincopa.com/media-platform/runtime/libasync.js" type="text/javascript"></script>
<script type="text/javascript">
// PLEASE CHANGE DEFAULT EXCERPT HANDLING TO CLEAN OR FULL (go to your Wordpress Dashboard/Settings/Cincopa Options ...
cp_load_widget("'.urlencode($match[0]).'", "cp_widget_'.$uni.'");
</script>
';

	$ret .= '<noscript>Click <a href="http://www.cincopa.com/media-platform/view.aspx?fid='.urlencode($match[0]).'">here</a> to open the gallery.<br>';
	$ret .= '</noscript>';

	return $ret;
}

function ea_cpmp_async_plugin_callback($match)
{
	$uni = uniqid('');
	$ret = '
<!-- Cincopa WordPress plugin '.ea_cpmp_plugin_ver().' (async engine): http://www.cincopa.com/media-platform/wordpress-plugin.aspx -->

<div id="cp_widget_'.$uni.'"><img src="http://www.cincopa.com/media-platform/runtime/loading.gif" style="border:0;" alt="Cincopa WordPress plugin" /></div>

<script type="text/javascript">
// PLEASE CHANGE DEFAULT EXCERPT HANDLING TO CLEAN OR FULL (go to your Wordpress Dashboard/Settings/Cincopa Options ...

var cpo = [];
cpo["_object"] ="cp_widget_'.$uni.'";
cpo["_fid"] = "'.urlencode($match[0]).'";

var ea_cpmp = ea_cpmp || [];
ea_cpmp.push(cpo);

(function() {
	var cp = document.createElement("script"); cp.type = "text/javascript";
	cp.async = true; cp.src = "http://www.cincopa.com/media-platform/runtime/libasync.js";
	var c = document.getElementsByTagName("script")[0];
	c.parentNode.insertBefore(cp, c);
})();

</script>

';

	$ret .= '<noscript>Click <a href="http://www.cincopa.com/media-platform/view.aspx?fid='.urlencode($match[0]).'">here</a> to open the gallery.<br>';
	$ret .= '</noscript>';

	return $ret;
}

function ea_cpmp_feed_plugin_callback($match)
{
//	$ret = '<a href="http://cincopa.com/~'.urlencode($match[1]).'"><img style="border:0;" alt="Cincopa WordPress plugin" src="http://www.cincopa.com/media-platform/api/thumb.aspx?fid='.urlencode($match[1]).'&size=large" /></a>';
	$ret = '<img style="border:0;" src="http://www.cincopa.com/media-platform/api/thumb.aspx?fid='.urlencode($match[1]).'&size=large" />';

	return $ret;
}

function ea_cpmp_plugin($content)
{
	$cincopa_async = get_site_option('CincopaAsync');
	if (strpos($_SERVER['REQUEST_URI'], 'tcapc=true'))
		$cincopa_async = 'async';
	else if (strpos($_SERVER['REQUEST_URI'], 'tcapc=false'))
		$cincopa_async = 'plain';

	if (strpos($_SERVER['REQUEST_URI'], 'cpdisable=true'))
		return $content;

	$cincopa_excerpt_rt = get_site_option('CincopaExcerpt');
	if ($cincopa_excerpt_rt == 'remove' && (is_search() || is_category() || is_archive() || is_home()))
		return preg_replace(CINCOPA_REGEXP, '', $content);
	else if ( is_feed() )
		return (preg_replace_callback(CINCOPA_REGEXP, 'ea_cpmp_feed_plugin_callback', $content));
	else if ($cincopa_async == 'async')
		return (preg_replace_callback(CINCOPA_REGEXP, 'ea_cpmp_async_plugin_callback', $content));
	else
		return (preg_replace_callback(CINCOPA_REGEXP, 'ea_cpmp_plugin_callback', $content));
}

function ea_cpmp_plugin_rss($content)
{
	return (preg_replace_callback(CINCOPA_REGEXP, 'ea_cpmp_feed_plugin_callback', $content));
}

//add_shortcode('cincopa', 'cincopa_plugin_shortcode');
add_filter('the_content', 'ea_cpmp_plugin');
add_filter('the_content_rss', 'ea_cpmp_plugin_rss');
add_filter('the_excerpt_rss', 'ea_cpmp_plugin_rss');
add_filter('comment_text', 'ea_cpmp_plugin'); 

add_action ( 'bp_get_activity_content_body', 'ea_cpmp_plugin' );
add_action ( 'bp_get_the_topic_post_content', 'ea_cpmp_plugin' );

add_action('wp_dashboard_setup', 'ea_cpmp_dashboard'); 

// Hook for adding admin menus
// http://codex.wordpress.org/Adding_Administration_Menus
add_action('admin_menu', 'ea_cpmp_mt_add_pages');

// register CincopaWidget widget
add_action('widgets_init', create_function('', 'return register_widget("CincopaWidget");'));


/////////////////////////////////
// dashboard widget
//////////////////////////////////
function ea_cpmp_dashboard()
{
	if(function_exists('wp_add_dashboard_widget'))
		wp_add_dashboard_widget('cincopa', 'Cincopa', 'ea_cpmp_dashboard_content');
}

function ea_cpmp_dashboard_content()
{

	echo "<iframe src='http://www.cincopa.com/media-platform/wordpress-dashboard-content.aspx?ver=".ea_cpmp_plugin_ver().ea_cpmp_afc()."&src=".urlencode(ea_cpmp_selfURL())."' width='100%' height='370px' scrolling='no'></iframe>";

}



// action function for above hook
function ea_cpmp_mt_add_pages() {
	// Add a new submenu under Options:
	
	add_options_page('Cincopa Options', 'Cincopa Options', 8, 'cincopaoptions', 'ea_cpmp_mt_options_page');

    // Add a new submenu under Manage:
//	add_management_page('Test Manage', 'Test Manage', 8, 'testmanage', 'ea_cpmp_mt_manage_page');

	if(function_exists('add_menu_page'))
	{
		// Add a new top-level menu (ill-advised):
		add_menu_page('CincopaEA', 'Cincopa EA', 8, __FILE__, 'ea_cpmp_mt_toplevel_page');

		// kill the first menu item that is usually the the identical to the menu itself
		add_submenu_page(__FILE__, '', '', 8, __FILE__);

		add_submenu_page(__FILE__, 'Manage Galleries', 'Manage Galleries', 8, 'sub-page', 'ea_cpmp_mt_sublevel_monitor');

		add_submenu_page(__FILE__, 'Media Library', 'Media Library', 8, 'sub-page1', 'ea_cpmp_mt_sublevel_library');

		add_submenu_page(__FILE__, 'Create Gallery', 'Create Gallery', 8, 'sub-page2', 'ea_cpmp_mt_sublevel_create');

		add_submenu_page(__FILE__, 'My Account', 'My Account', 8, 'sub-page3', 'ea_cpmp_mt_sublevel_myaccount');

		add_submenu_page(__FILE__, 'Support Forum', 'Support Forum', 8, 'sub-page4', 'ea_cpmp_mt_sublevel_forum');
	}
}

function ea_cpmp_isAdmin()
{
	return !function_exists('is_site_admin') || is_site_admin() == true;
}

function ea_cpmp_mt_options_page() {

//	if( is_site_admin() == false ) {
//		wp_die( __('You do not have permission to access this page.') );
//	}

	if (strpos($_SERVER['QUERY_STRING'], 'hide_note=welcome_notice'))
	{
		update_site_option('cincopa_welcome_notice', ea_cpmp_plugin_ver());
		echo "<script type=\"text/javascript\">	document.location.href = '".$_SERVER['HTTP_REFERER']."'; </script>";
		exit;
	}

	if (strpos($_SERVER['QUERY_STRING'], 'hide_note=premiumpress_notice'))
	{
		update_site_option('premiumpress_notice', ea_cpmp_plugin_ver());
	}

	$cincopa_afc = get_site_option('CincopaAFC');
	$cincopa_excerpt = get_site_option('CincopaExcerpt');
	$cincopa_async = get_site_option('CincopaAsync');

	if ( isset($_POST['submit']) )
	{
		if (ea_cpmp_isAdmin())
		{
			if (isset($_POST['cincopaafc']))
			{
				$cincopa_afc = $_POST['cincopaafc'];
				update_site_option('CincopaAFC', $cincopa_afc);
			}

			if (isset($_POST['asyncRel']))
			{
				$cincopa_async = $_POST['asyncRel'];
				update_site_option('CincopaAsync', $cincopa_async);			
			}
		}

		if (isset($_POST['embedRel']))
		{
			$cincopa_excerpt = $_POST['embedRel'];
			update_site_option('cincopaexcerpt', $cincopa_excerpt);			
		}
		
		echo "<div id=\"updatemessage\" class=\"updated fade\"><p>Cincopa settings updated.</p></div>\n";
		echo "<script type=\"text/javascript\">setTimeout(function(){jQuery('#updatemessage').hide('slow');}, 3000);</script>";	
	}

	$disp_excerpt2 = $cincopa_excerpt == 'clean' ? 'checked="checked"' : '';
	$disp_excerpt3 = $cincopa_excerpt == 'full' ? 'checked="checked"' : '';
	$disp_excerpt4 = $cincopa_excerpt == 'remove' ? 'checked="checked"' : '';
	$disp_excerpt1 = $cincopa_excerpt == '' || $cincopa_excerpt == 'nothing' ? 'checked="checked"' : '';

	$disp_async2 = $cincopa_async == 'async' ? 'checked="checked"' : '';
	$disp_async1 = $cincopa_async == '' || $cincopa_async == 'plain' ? 'checked="checked"' : '';


?>
	<div class="wrap">
		<h2>Cincopa Configuration</h2>
		<div class="postbox-container">
			<div class="metabox-holder">
				<div class="meta-box-sortables">
					<form action="" method="post" id="cincopa-conf">
						<div id="cincopa_settings" class="postbox">
							<div class="handlediv" title="Click to toggle">
								<br />
							</div>
							<h3 class="hndle">
								<span>Cincopa Settings</span>
							</h3>
							<div class="inside" style="width:600px;">
								<table class="form-table">

									<tr style="width:100%;">
										<th valign="top" scrope="row">
											<label for="cincopaafc">
												Excerpt Handling (<a target="_blank" href="http://help.cincopa.com/entries/448859-wordpress-plugin-settings-page#excerpt">what?</a>):
											</label>
										</th>
										<td valign="top">

											<input type="radio" <?php echo $disp_excerpt1; ?> id="embedCustomization0" name="embedRel" value="nothing"/>
											<label for="embedCustomization0">Do nothing (default Wordpress behavior)</label>
											<br/>
											<input type="radio" <?php echo $disp_excerpt2; ?> id="embedCustomization1" name="embedRel" value="clean"/>
											<label for="embedCustomization1">Clean excerpt (do not show gallery)</label>
											<br/>
											<input type="radio" <?php echo $disp_excerpt4; ?> id="embedCustomization3" name="embedRel" value="remove"/>
											<label for="embedCustomization3">Remove gallery (do not show gallery in all non post pages)</label>
											<br/>
											<input type="radio" <?php echo $disp_excerpt3; ?> id="embedCustomization2" name="embedRel" value="full"/>
											<label for="embedCustomization2">Full excerpt (show gallery)</label>
											<br/>

										</td>
									</tr>


									<?php

if (ea_cpmp_isAdmin())
{
?>


									<tr style="width:100%;">
										<th valign="top" scrope="row">
											<label for="cincopaafc">
												Cincopa AFC (<a target="_blank" href="http://help.cincopa.com/entries/448859-wordpress-plugin-settings-page#afc">what?</a>):
											</label>
										</th>
										<td valign="top">
											<input id="cincopaafc" name="cincopaafc" type="text" size="20" value="<?php echo $cincopa_afc; ?>"/>
										</td>
									</tr>





									<tr style="width:100%;">
										<th valign="top" scrope="row">
											<label for="cincopaasync">
												Async Engine (<a target="_blank" href="http://help.cincopa.com/entries/448859-wordpress-plugin-settings-page#async">what?</a>):
											</label>
										</th>
										<td valign="top">

											<input type="radio" <?php echo $disp_async1; ?> id="asyncCustomization0" name="asyncRel" value="plain"/>
											<label for="asyncCustomization0">Plain Sync</label>
											<br/>
											<input type="radio" <?php echo $disp_async2; ?> id="asyncCustomization1" name="asyncRel" value="async"/>
											<label for="asyncCustomization1">Advanced Async </label>
											<br/>


										</td>
									</tr>




									<?php
}

?>


									<tr style="width:100%;">
										<th valign="top" scrope="row" colspan=2>
											Note:
<ol>
<li>Use this PHP code to add a gallery directly to your template : <br>&nbsp;&nbsp;&nbsp; <i>&lt;?php echo ea_cpmp_cincopa_tag("GALLERY ID"); ?&gt;</i></li>
</ol>
										</th>
									</tr>

<?php						if (get_site_option('premiumpress_notice') != ea_cpmp_plugin_ver())
									{	?>
										<div id="message" class="updated fade">
											<p>
												All our products are integrated with Premium Press business themes, <a target="_blank" href="http://www.premiumpress.com/?piwik_campaign=WordpressPlugin&piwik_kwd=cincopa">click here for details</a>.
											</p>
											<p>

												<input type="button" class="button" value="View premiumpress themes" onclick="window.open('http://www.premiumpress.com/?piwik_campaign=WordpressPlugin&piwik_kwd=cincopa');" />

												<input type="button" class="button" value="Hide this message" onclick="document.location.href = 'options-general.php?page=cincopaoptions&amp;hide_note=premiumpress_notice';" />
											</p>

										</div>
<?php							} ?>

								</table>
							</div>
						</div>
						<div class="submit">
							<input type="submit" class="button-primary" name="submit" value="Update &raquo;" />
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
<?php
    
    
}
/*
// ea_cpmp_mt_manage_page() displays the page content for the Test Manage submenu
function ea_cpmp_mt_manage_page() {
    echo "<h2>Test Manage</h2>";
}
*/

function ea_cpmp_mt_toplevel_page() {
    echo "<iframe src='http://www.cincopa.com/media-platform/start.aspx?ver=".ea_cpmp_plugin_ver().ea_cpmp_afc()."&src=".urlencode(ea_cpmp_selfURL())."' width='98%' height='2000px'></iframe>";
}

function ea_cpmp_mt_sublevel_create() {
    echo "<iframe src='http://www.cincopa.com/media-platform/wizard_name.aspx?ver=".ea_cpmp_plugin_ver().ea_cpmp_afc()."&src=".urlencode(ea_cpmp_selfURL())."' width='98%' height='2000px'></iframe>";
}

function ea_cpmp_mt_sublevel_monitor() {
    echo "<iframe src='http://www.cincopa.com/media-platform/wizard_edit.aspx?ver=".ea_cpmp_plugin_ver().ea_cpmp_afc()."&src=".urlencode(ea_cpmp_selfURL())."' width='98%' height='2000px'></iframe>";
}

function ea_cpmp_mt_sublevel_library() {
    echo "<iframe src='http://www.cincopa.com/media-platform/wizard2/library.aspx?ver=".ea_cpmp_plugin_ver().ea_cpmp_afc()."&src=".urlencode(ea_cpmp_selfURL())."' width='98%' height='2000px'></iframe>";
}

function ea_cpmp_mt_sublevel_myaccount() {
    echo "<iframe src='http://www.cincopa.com/cincopaManager/ManageAccount.aspx?ver=".ea_cpmp_plugin_ver().ea_cpmp_afc()."&src=".urlencode(ea_cpmp_selfURL())."' width='98%' height='2000px'></iframe>";
}

function ea_cpmp_mt_sublevel_forum() {
    echo "<iframe src='http://www.cincopa.com/redirect_to_help.aspx' width='98%' height='2000px'></iframe>";
}



if (!class_exists('CincopaWidget')) {

	/**
	 * CincopaWidget Class
	 */
	class CincopaWidget extends WP_Widget {
			/** constructor */
			function CincopaWidget() {
					parent::WP_Widget(false, $name = 'Cincopa Gallery Widget');	
			}

			/** @see WP_Widget::widget */
			function widget($args, $instance) {		
					extract( $args );

					if (strpos($instance['galleryid'], 'cincopa'))
						$gallery = ea_cpmp_plugin($instance['galleryid']);
					else
						$gallery = ea_cpmp_plugin('[cincopa '.$instance['galleryid'].']');

					echo $gallery;
			}

			/** @see WP_Widget::update */
			function update($new_instance, $old_instance) {				
					return $new_instance;
			}

			/** @see WP_Widget::form */
			function form($instance) {				
					$galleryid = esc_attr($instance['galleryid']);
					?>
	<p>
		<label for=""
			<?php echo $this->get_field_id('galleryid'); ?>"><?php _e('Gallery ID:'); ?> <a target="_blank" href="http://help.cincopa.com/entries/405593-how-do-i-add-a-gallery-to-my-wordpress-sidebar">what?</a> <input class="widefat" id=""<?php echo $this->get_field_id('galleryid'); ?>" name="<?php echo $this->get_field_name('galleryid'); ?>" type="text" value="<?php echo $galleryid; ?>" />
		</label>
	</p>
	<?php 
			}

	} // class CincopaWidget

}

// http://www.aaronrussell.co.uk/blog/improving-wordpress-the_excerpt/

function ea_cpmp_improved_trim_excerpt($text)
{
	global $post;
	if ( '' == $text ) {
		$text = get_the_content('');

		$cincopa_excerpt_rt = get_site_option('CincopaExcerpt');

		if ($cincopa_excerpt_rt == 'clean')
			$text = preg_replace(CINCOPA_REGEXP, '', $text);

		$text = apply_filters('the_content', $text);

		if ($cincopa_excerpt_rt == 'full')
			return $text;

		$text = str_replace(']]>', ']]&gt;', $text);
		$text = preg_replace('@<script[^>]*?>.*?</script>@si', '', $text);

		$text = strip_tags($text, '<'.'p'.'>');
		$excerpt_length = 80;
		$words = explode(' ', $text, $excerpt_length + 1);
		if (count($words)> $excerpt_length) 
		{
			array_pop($words);
			array_push($words, '[...]');
			$text = implode(' ', $words);
		}
	}
			
	return $text;
}

$cincopa_excerpt_rt = get_site_option('CincopaExcerpt');
if ($cincopa_excerpt_rt == 'full' || $cincopa_excerpt_rt == 'clean')
{
	remove_filter('get_the_excerpt', 'wp_trim_excerpt');
	//	remove_all_filters('get_the_excerpt');
	add_filter('get_the_excerpt', 'ea_cpmp_improved_trim_excerpt');
}

function ea_cpmp_activation_notice()
			{ ?>
			<div id="message" class="updated fade">
				<p style="line-height: 150%">
					<strong>Welcome to Cincopa Rich Media Plugin</strong> - the most popular way to add videos, photo galleries, slideshows, Cooliris gallery, podcast and music to your site.
				</p>
				<p>
					On every post page (above the text box) you'll find this <img src="<?php echo ea_cpmp_pluginURI() ?>/media-cincopa.gif"  /> icon, click on it to start or use sidebar Widgets (Appearance menu).
				</p>
				<p>
		
<input type="button" class="button" value="Cincopa Options Page" onclick="document.location.href = 'options-general.php?page=cincopaoptions';" />

<input type="button" class="button" value="Hide this message" onclick="document.location.href = 'options-general.php?page=cincopaoptions&amp;hide_note=welcome_notice';" />

<input type="button" class="button" value="Video Tutorial" onclick="window.open('http://help.cincopa.com/entries/488838-how-to-use-the-wordpress-plugin');" />

				</p>

			</div>


			<?php

	if (get_site_option('cincopa_installed') != 'true')
	{
		update_site_option('cincopa_installed', 'true');
		echo "<img src='http://goo.gl/HqSz' width=0 height=0 />";
	}
}



if (get_site_option('cincopa_welcome_notice') != ea_cpmp_plugin_ver())
	add_action( 'admin_notices', 'ea_cpmp_activation_notice' );



?>