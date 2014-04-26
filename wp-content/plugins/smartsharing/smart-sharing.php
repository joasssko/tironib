<?php
/*
Plugin Name: Smart Sharing
Plugin URI: http://www.wpbeginner.com
Description: Smart Sharing adds a floating box on your single post page with social media icons. This technique has proven to increase the number of retweets, facebook shares, and other votes.
Version: 1.0
Author: Syed Balkhi
Author URI: http://www.uzzz.net/


/--------------------------------------------------------------------\
|                                                                    |
| License: Proprietary                                               |
|                                                                    |
| Smart Sharing - adds a floating box on single posts page to        |
| increase the number of retweets, facebook shares and othervotes.   |
| Copyright (C) Syed Balkhi, Uzzz Productions                        |
| http://www.uzzz.net/ and http://www.wpbeginner.com                 |
| All rights reserved.                                               |
|                                                                    |
| This program is free software; It is only meant for WPBeginner     |
| email subscribers. You may not sell, or redistribute this plugin   |
| in any shape or form. Free for personal and Commercial use.        |
|                                                                    |
| This program is distributed in the hope that it will be useful,    |
| but WITHOUT ANY WARRANTY; without even the implied warranty of     |
| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
\--------------------------------------------------------------------/

*/
/* Version check */
function smart_share_url( $path = '' ) {
	global $wp_version;
	if ( version_compare( $wp_version, '2.8', '<' ) ) { // Using WordPress 2.7
		$folder = dirname( plugin_basename( __FILE__ ) );
		if ( '.' != $folder )
			$path = path_join( ltrim( $folder, '/' ), $path );

		return plugins_url( $path );
	}
	return plugins_url( $path, __FILE__ );
}



add_action('wp_head', 'ss_css');
function ss_css(){ ?>
<style type="text/css">
.sharepost{
border:<?php if (get_option('border_width')) { echo get_option('border_width'); } else {echo '1';} ?>px solid #<?php if (get_option('border_color')) { echo get_option('border_color'); } else {echo 'E8E8E8';} ?>;
position:fixed;
background:#<?php if (get_option('bgcolor')) { echo get_option('bgcolor'); } else {echo 'fff';} ?>; 
width: <?php if (get_option('box_width')) { echo get_option('box_width'); } else {echo '60';} ?>px; 
<?php if (get_option('icon_alignment') == "true") {?>left: 0; margin: 0 0px 0 <?php if (get_option('edge_distance')) { echo get_option('edge_distance'); } else {echo '20'; } ?>px;<?php } elseif (get_option('icon_alignment') == "false") { ?>right: 0; margin: 0 <?php if (get_option('edge_distance')) { echo get_option('edge_distance'); } else {echo '20'; } ?>px 0 0;<?php } else {?> left: 0; margin: 0 0px 0 <?php if (get_option('edge_distance')) { echo get_option('edge_distance'); } else {echo '20'; } ?>px;<?php } ?> 
top: <?php if (get_option('top_distance')) { echo get_option('top_distance'); } else {echo '100';} ?>px; }
.sharer{
	padding: 5px;
	border-bottom: 1px solid #e8e8e8;
}
</style>
<?php }
add_action( 'wp_footer', 'add_smartsharing' );
function add_smartsharing() {
if (is_single()) {
global $wp_query;
$postid = $wp_query->post->ID;
if (get_post_meta( $postid, '_mcf_override', true ) == "yes") {
?>
<div class="sharepost">
<?php 
global $wp_query;
$postid = $wp_query->post->ID;
if (get_post_meta( $postid, '_mcf_retweet', true ) == "yes") { ?>
<div class="sharer">
<script type="text/javascript">
tweetmeme_source = '<?php echo get_option('tweetmeme_source'); ?>'; tweetmeme_service = 'bit.ly'; tweetmeme_url = '<?php the_permalink() ?>';
</script>
<script type="text/javascript" src="http://tweetmeme.com/i/scripts/button.js"></script>
</div>
<?php } else {echo '';} ?>
<?php if (get_post_meta( $postid, '_mcf_facebook', true ) == "yes") { ?>
<div class="sharer">
<script src="<?php echo smart_share_url('js/fshare.js'); ?>"></script>
</div>
<?php } else {echo '';} ?>	
<?php 
if (get_post_meta( $postid, '_mcf_digg', true ) == "yes") {
?>
<div class="sharer">
<script src="http://widgets.digg.com/buttons.js" type="text/javascript"></script>
<a class="DiggThisButton DiggMedium"></a>
</div>
<?php } else {echo '';} ?>
<?php 
if (get_post_meta( $postid, '_mcf_stumble', true ) == "yes") {
?>
<div class="sharer">
<script src="http://www.stumbleupon.com/hostedbadge.php?s=5&amp;r=<?php the_permalink(); ?>"></script>
</div>
<?php } else {echo '';} ?>
<?php if (get_post_meta( $postid, '_mcf_custom' )) { echo get_post_meta( $postid, '_mcf_custom', true); } else {echo '';} ?>

</div>


<?php } else { ?>
<div class="sharepost">
<?php 
global $wp_query;
$postid = $wp_query->post->ID;
if (get_option('tweetmeme') == "true") { ?>
<div class="sharer">
<script type="text/javascript">
tweetmeme_source = '<?php echo get_option('tweetmeme_source'); ?>'; tweetmeme_service = 'bit.ly'; tweetmeme_url = '<?php the_permalink() ?>';
</script>
<script type="text/javascript" src="http://tweetmeme.com/i/scripts/button.js"></script>
</div>
<?php } else {echo '';} ?>
<?php if (get_option('fshare') == "true") { ?>
<div class="sharer">
<script src="<?php echo smart_share_url('js/fshare.js'); ?>"></script>
</div>
<?php } else {echo '';} ?>	
<?php 
if (get_option('digg') == "true") { ?>
<div class="sharer">
<script src="http://widgets.digg.com/buttons.js" type="text/javascript"></script>
<a class="DiggThisButton DiggMedium"></a>
</div>
<?php } else {echo '';} ?>
<?php 
if (get_option('stumbleupon') == "true") { ?>
<div class="sharer">
<script src="http://www.stumbleupon.com/hostedbadge.php?s=5&amp;r=<?php the_permalink(); ?>"></script>
</div>
<?php } else {echo '';} ?>

<?php if (get_option('custom')) { echo get_option('custom'); }  else {echo '';} ?>

</div>

<?php } } }
// create custom plugin settings menu
add_action('admin_menu', 'smart_sharing_settings');

function smart_sharing_settings() {

	//create new top-level menu
	add_menu_page('Smart Sharing Settings', 'Smart Sharing', 'administrator', __FILE__, 'smartsharing_settings_page',plugins_url('/images/icon.png', __FILE__));

	//call register settings function
	add_action( 'admin_init', 'register_smartsharingsettings' );
}


function register_smartsharingsettings() {
	//register our settings
	register_setting( 'smartsharing-settings-group', 'icon_alignment' );
	register_setting( 'smartsharing-settings-group', 'edge_distance' );
	register_setting( 'smartsharing-settings-group', 'top_distance' );
	register_setting( 'smartsharing-settings-group', 'bgcolor' );
	register_setting( 'smartsharing-settings-group', 'box_width' );
	register_setting( 'smartsharing-settings-group', 'border_color' );
	register_setting( 'smartsharing-settings-group', 'border_width' );
	register_setting( 'smartsharing-settings-group', 'tweetmeme' );
	register_setting( 'smartsharing-settings-group', 'tweetmeme_source' );
	register_setting( 'smartsharing-settings-group', 'fshare' );
	register_setting( 'smartsharing-settings-group', 'digg' );
	register_setting( 'smartsharing-settings-group', 'stumbleupon' );
	register_setting( 'smartsharing-settings-group', 'custom' );
}

function smartsharing_settings_page() {

?>
<div style="width: 200px; right: 0; float: right; position: fixed; margin: 30px 10px 20px 0; background: #fff; border: 1px solid #e9e9e9; padding: 5px 5px 5px 5px; color: #666; font-size: 11px;">
<h3 style="margin: 0 0 10px 0; border-bottom: 1px dashed #666;">Donate</h3>
If you like this plugin and want WPBeginner to release more cool products, then please consider making a donation.
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" style="margin: 10px 0 20px 0;">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="KXE7F3TEK9Z5Y">
<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>
<h3 style="margin: 0 0 10px 0; border-bottom: 1px dashed #666;">Check us out:</h3>
Check our main site <a href="http://www.wpbeginner.com">WPBeginner</a> for WordPress tutorials. Don't forget to <a href="http://www.twitter.com/wpbeginner">follow us on twitter</a> and <a href="http://facebook.com/wpbeginner">join our facebook fan page</a>.

<h3 style="margin: 10px 0 10px 0; border-bottom: 1px dashed #666;">Read Me:</h3>
<a href="http://www.wpbeginner.com/smart-sharing-plugin-for-wordpress/">See this for Any Questions Regarding this Plugin</a>

</div>
<div class="wrap">
<h2>Smart Sharing</h2>

<form method="post" action="options.php">
    <?php settings_fields( 'smartsharing-settings-group' ); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">Icons Alignment</th>
        <td>
        <input type="radio" id="icon_alignment_yes" name="icon_alignment" value="true" <?php if (get_option('icon_alignment') == "true") { _e('checked="checked"', "icon_alignment"); } ?> /> Left&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="icon_alignment_no" name="icon_alignment" value="false" <?php if (get_option('icon_alignment') == "false") { _e('checked="checked"', "icon_alignment"); } ?>/> Right</td>
        </tr>
        
         <tr valign="top">
        <th scope="row">Icons Distance From The Edge</th>
        <td><input type="text" name="edge_distance" value="<?php echo get_option('edge_distance'); ?>" style="width: 50px;" /> px (Default 20px)</td>
        </tr>
         
        <tr valign="top">
        <th scope="row">Icons Distance From Top</th>
        <td><input type="text" name="top_distance" value="<?php echo get_option('top_distance'); ?>" style="width: 50px;" /> px (Default 100px)</td>
        </tr>
        
        <tr valign="top">
        <th scope="row">Retweet Button</th>
        <td><input type="checkbox" name="tweetmeme" value="true" <?php if (get_option('tweetmeme') == "true") { _e('checked="checked"', "tweetmeme"); }?> /></td>
        </tr>
        
        <tr valign="top">
        <th scope="row">Your Twitter Account</th>
        <td>
        <input type="text" name="tweetmeme_source" value="<?php echo get_option('tweetmeme_source'); ?>" /> Must enter one, if you have the Retweet button checked.</td>
        </tr>
        
        <tr valign="top">
        <th scope="row">Facebook Button</th>
        <td><input type="checkbox" name="fshare" value="true" <?php if (get_option('fshare') == "true") { _e('checked="checked"', "fshare"); }?> /></td>
        </tr>
        
        <tr valign="top">
        <th scope="row">Digg Button</th>
        <td><input type="checkbox" name="digg" value="true" <?php if (get_option('digg') == "true") { _e('checked="checked"', "digg"); }?> /></td>
        </tr>
        
        <tr valign="top">
        <th scope="row">Stumbleupon Button</th>
        <td><input type="checkbox" name="stumbleupon" value="true" <?php if (get_option('stumbleupon') == "true") { _e('checked="checked"', "stumbleupon"); }?> /></td>
        </tr>
        
        <tr valign="top">
        <th scope="row">Custom Codes</th>
        <td>
         <textarea name="custom" id="custom" cols="60" rows="10"><?php echo stripslashes(htmlentities (get_option('custom'))); ?></textarea>
        </td>
        </tr>
        
        
    </table>
    
    <h2>Styling:</h2>
    
    <table class="form-table">
    	<tr valign="top">
        <th scope="row">Box Background Color</th>
        <td><input type="text" name="bgcolor" value="<?php echo get_option('bgcolor'); ?>" /> Hex Value: FFFFFF</td>
        </tr>
        <tr valign="top">
        <th scope="row">Box Width</th>
        <td><input type="text" name="box_width" value="<?php echo get_option('box_width'); ?>" style="width: 50px;" />px Default: 60px</td>
        </tr>
        <tr valign="top">
        <th scope="row">Box Border Color</th>
        <td><input type="text" name="border_color" value="<?php echo get_option('border_color'); ?>" /> Hex Value: E8E8E8</td>
        </tr>
          <tr valign="top">
        <th scope="row">Box Border Width</th>
        <td><input type="text" name="border_width" value="<?php echo get_option('border_width'); ?>" style="width: 50px;" />px Default: 1px</td>
        </tr>
    </table>
    
    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>

</form>
</div>

<?php } ?>
<?php
if ( !class_exists('mySmartSharing') ) {

	class mySmartSharing {
		/**
		* @var  string  $prefix  The prefix for storing custom fields in the postmeta table
		*/
		var $prefix = '_mcf_';
		/**
		* @var  array  $customFields  Defines the custom fields available
		*/
		var $customFields =	array(
			array(
				"name"			=> "override",
				"title"			=> "Override Global Settings",
				"description"	=> "",
				"type"			=> "checkbox",
				"scope"			=>	array( "post" ),
				"capability"	=> "manage_options"
			),
			array(
				"name"			=> "retweet",
				"title"			=> "Twitter",
				"description"	=> "",
				"type"			=> "checkbox",
				"scope"			=>	array( "post" ),
				"capability"	=> "manage_options"
			),
			array(
				"name"			=> "facebook",
				"title"			=> "Facebook",
				"description"	=> "",
				"type"			=> "checkbox",
				"scope"			=>	array( "post" ),
				"capability"	=> "manage_options"
			),
			array(
				"name"			=> "digg",
				"title"			=> "Digg",
				"description"	=> "",
				"type"			=> "checkbox",
				"scope"			=>	array( "post" ),
				"capability"	=> "manage_options"
			),
			array(
				"name"			=> "stumble",
				"title"			=> "Stumbleupon",
				"description"	=> "",
				"type"			=> "checkbox",
				"scope"			=>	array( "post" ),
				"capability"	=> "manage_options"
			),
			array(
				"name"			=> "custom",
				"title"			=> "Custom Codes",
				"description"	=> "",
				"type"			=>	"textarea",
				"scope"			=>	array( "post" ),
				"capability"	=> "edit_posts"
			),
		);
		/**
		* PHP 4 Compatible Constructor
		*/
		function mySmartSharing() { $this->__construct(); }
		/**
		* PHP 5 Constructor
		*/
		function __construct() {
			add_action( 'admin_menu', array( &$this, 'createCustomFields' ) );
			add_action( 'save_post', array( &$this, 'saveCustomFields' ), 1, 2 );
		}

		/**
		* Create the new Custom Fields meta box
		*/
		function createCustomFields() {
			if ( function_exists( 'add_meta_box' ) ) {
				add_meta_box( 'my-custom-fields', 'Smart Sharing Options', array( &$this, 'displayCustomFields' ), 'post', 'normal', 'high' );
			}
		}
		/**
		* Display the new Custom Fields meta box
		*/
		function displayCustomFields() {
			global $post;
			?>
			<div class="form-wrap">
				<?php
				wp_nonce_field( 'my-custom-fields', 'my-custom-fields_wpnonce', false, true );
				foreach ( $this->customFields as $customField ) {
					// Check scope
					$scope = $customField[ 'scope' ];
					$output = false;
					foreach ( $scope as $scopeItem ) {
						switch ( $scopeItem ) {
							case "post": {
								// Output on any post screen
								if ( basename( $_SERVER['SCRIPT_FILENAME'] )=="post-new.php" || $post->post_type=="post" )
									$output = true;
								break;
							}
							case "page": {
								// Output on any page screen
								if ( basename( $_SERVER['SCRIPT_FILENAME'] )=="page-new.php" || $post->post_type=="page" )
									$output = true;
								break;
							}
						}
						if ( $output ) break;
					}
					// Check capability
					if ( !current_user_can( $customField['capability'], $post->ID ) )
						$output = false;
					// Output if allowed
					if ( $output ) { ?>
						<div class="form-field form-required">
							<?php
							switch ( $customField[ 'type' ] ) {
								case "checkbox": {
									// Checkbox
									echo '<label for="' . $this->prefix . $customField[ 'name' ] .'" style="display:inline;"><b>' . $customField[ 'title' ] . '</b></label>&nbsp;&nbsp;';
									echo '<input type="checkbox" name="' . $this->prefix . $customField['name'] . '" id="' . $this->prefix . $customField['name'] . '" value="yes"';
									if ( get_post_meta( $post->ID, $this->prefix . $customField['name'], true ) == "yes" )
										echo ' checked="checked"';
									echo '" style="width: auto; float: left; margin: 0 0 10px 0;" />';
									break;
								}
								case "textarea": {
									// Text area
									echo '<label for="' . $this->prefix . $customField[ 'name' ] .'"><b>' . $customField[ 'title' ] . '</b></label>';
									echo '<textarea name="' . $this->prefix . $customField[ 'name' ] . '" id="' . $this->prefix . $customField[ 'name' ] . '" columns="30" rows="3">' . htmlspecialchars( get_post_meta( $post->ID, $this->prefix . $customField[ 'name' ], true ) ) . '</textarea>';
									break;
								}
								default: {
									// Plain text field
									echo '<label for="' . $this->prefix . $customField[ 'name' ] .'"><b>' . $customField[ 'title' ] . '</b></label>';
									echo '<input type="text" name="' . $this->prefix . $customField[ 'name' ] . '" id="' . $this->prefix . $customField[ 'name' ] . '" value="' . htmlspecialchars( get_post_meta( $post->ID, $this->prefix . $customField[ 'name' ], true ) ) . '" />';
									break;
								}
							}
							?>
							<?php if ( $customField[ 'description' ] ) echo '<p>' . $customField[ 'description' ] . '</p>'; ?>
						</div>
					<?php
					}
				} ?>
			</div>
			<?php
		}
		/**
		* Save the new Custom Fields values
		*/
		function saveCustomFields( $post_id, $post ) {
			if ( !wp_verify_nonce( $_POST[ 'my-custom-fields_wpnonce' ], 'my-custom-fields' ) )
				return;
			if ( !current_user_can( 'edit_post', $post_id ) )
				return;
			if ( $post->post_type != 'page' && $post->post_type != 'post' )
				return;
			foreach ( $this->customFields as $customField ) {
				if ( current_user_can( $customField['capability'], $post_id ) ) {
					if ( isset( $_POST[ $this->prefix . $customField['name'] ] ) && trim( $_POST[ $this->prefix . $customField['name'] ] ) ) {
						update_post_meta( $post_id, $this->prefix . $customField[ 'name' ], $_POST[ $this->prefix . $customField['name'] ] );
					} else {
						delete_post_meta( $post_id, $this->prefix . $customField[ 'name' ] );
					}
				}
			}
		}

	} // End Class

} // End if class exists statement

// Instantiate the class
if ( class_exists('mySmartSharing') ) {
	$mySmartSharing_var = new mySmartSharing();
}


add_action('wp_dashboard_setup', 'my_custom_dashboard_widgets');

function my_custom_dashboard_widgets() {
   global $wp_meta_boxes;

   wp_add_dashboard_widget('wpbeginnerdbwidget', 'Latest from WPBeginner', 'db_widget');
}
		function text_limit( $text, $limit, $finish = ' [&hellip;]') {
			if( strlen( $text ) > $limit ) {
		    	$text = substr( $text, 0, $limit );
				$text = substr( $text, 0, - ( strlen( strrchr( $text,' ') ) ) );
				$text .= $finish;
			}
			return $text;
		}

		function db_widget() {
			$options = get_option('wpbeginnerdbwidget');
			$wpbeginnerlogo = WP_PLUGIN_URL . '/smart-sharing/images/wpbeginner.gif';
			$emaillogo = WP_PLUGIN_URL . '/smart-sharing/images/email.gif';
			require_once(ABSPATH.WPINC.'/rss.php');
			if ( $rss = fetch_rss( 'http://wpbeginner.com/feed/' ) ) { ?>
				<div class="rss-widget">
                
				<a href="http://www.wpbeginner.com/" title="Go to WPBeginner.com"><img src="<?php echo $wpbeginnerlogo ?>"  class="alignright" alt="WPBeginner"/></a>			
				<ul>
                <?php 
				$rss->items = array_slice( $rss->items, 0, 5 );
				foreach ( (array) $rss->items as $item ) {
					echo '<li>';
					echo '<a class="rsswidget" href="'.clean_url( $item['link'], $protocolls=null, 'display' ).'">'. ($item['title']) .'</a> ';
					echo '<span class="rss-date">'. date('F j, Y', strtotime($item['pubdate'])) .'</span>';
					
					echo '</li>';
				}
				?> 
				</ul>
				<div style="border-top: 1px solid #ddd; padding-top: 10px; text-align:center;">
				<a href="http://feeds2.feedburner.com/wpbeginner"><img src="<?php get_bloginfo('wpurl') ?>/wp-includes/images/rss.png" alt=""/> Subscribe with RSS</a>
				&nbsp; &nbsp; &nbsp;
				<a href="http://www.wpbeginner.com/wordpress-newsletter/"><img src="<?php echo $emaillogo ?>" alt=""/> Subscribe by email</a>
				</div>
				</div>
			<?php }
		}


?>