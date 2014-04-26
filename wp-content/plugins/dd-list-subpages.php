<?php
/*
Plugin Name: List Subpages
Plugin URI: http://www.dagondesign.com/articles/list-subpages-plugin-for-wordpress
Description: Generates a list of subpages you can display in the page, or anywhere in the theme.
Author: Dagon Design
Version: 1.0
Author URI: http://www.dagondesign.com
*/

$ddlsp_ver = '1.0';


// Set defaults if options do not exist
add_option('ddlsp_before_list', '<ul>');
add_option('ddlsp_after_list', '</ul>');
add_option('ddlsp_sort_column', 'post_title');
add_option('ddlsp_sort_order', 'asc');
add_option('ddlsp_exclude', '');
add_option('ddlsp_include', '');
add_option('ddlsp_depth', 1);
add_option('ddlsp_show_date', TRUE);
add_option('ddlsp_date_format', 'l, F j, Y');
add_option('ddlsp_title_li', '');


function ddlsp_add_option_pages() {
	if (function_exists('add_options_page')) {
		add_options_page('List Subpages', 'DDListSubpages', 8, __FILE__, 'ddlsp_options_page');
	}		
}


function ddlsp_options_page() {

	global $ddlsp_ver;

	if (isset($_POST['set_defaults'])) {

		echo '<div id="message" class="updated fade"><p><strong>';

		update_option('ddlsp_before_list', '<ul>');
		update_option('ddlsp_after_list', '</ul>');
		update_option('ddlsp_sort_column', 'post_title');
		update_option('ddlsp_sort_order', 'asc');
		update_option('ddlsp_exclude', '');
		update_option('ddlsp_include', '');
		update_option('ddlsp_depth', 1);
		update_option('ddlsp_show_date', TRUE);
		update_option('ddlsp_date_format', 'l, F j, Y');
		update_option('ddlsp_title_li', '');

		echo 'Default Options Loaded!';
		echo '</strong></p></div>';

	} else	if (isset($_POST['info_update'])) {

		echo '<div id="message" class="updated fade"><p><strong>';

		update_option('ddlsp_before_list', stripslashes((string)$_POST['ddlsp_before_list']));
		update_option('ddlsp_after_list', stripslashes((string)$_POST['ddlsp_after_list']));
		update_option('ddlsp_sort_column', stripslashes((string)$_POST['ddlsp_sort_column']));
		update_option('ddlsp_sort_order', stripslashes((string)$_POST['ddlsp_sort_order']));
		update_option('ddlsp_exclude', stripslashes((string)$_POST['ddlsp_exclude']));
		update_option('ddlsp_include', stripslashes((string)$_POST['ddlsp_include']));
		update_option('ddlsp_depth', stripslashes((string)$_POST['ddlsp_depth']));
		update_option('ddlsp_show_date', (bool)$_POST['ddlsp_show_date']);
		update_option('ddlsp_date_format', stripslashes((string)$_POST['ddlsp_date_format']));
		update_option('ddlsp_title_li', stripslashes((string)$_POST['ddlsp_title_li']));

		echo 'Configuration Updated!';
		echo '</strong></p></div>';
	}


	?>

	<div class=wrap>

	<h2>List Subpages <?php echo $ddlsp_ver; ?></h2>

	<p>To check for new versions or get more information, visit <a href="http://www.dagondesign.com/articles/list-subpages-plugin-for-wordpress">this plugin's page</a>.</p>

	<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
	<input type="hidden" name="info_update" id="info_update" value="true" />


	<fieldset class="options"> 
	<legend>Usage</legend>

	<p>There are two ways you can use this plugin:</p>
	<ol>
	<li>Add the trigger text <strong>&lt;!-- ddlsp --&gt;</strong> to a page</li>
	<li>Call the function from a template file: <strong>&lt;?php echo ddlsp_generate(); ?&gt;</strong></li>
	</ol>

	</fieldset>



	<fieldset class="options"> 
	<legend>Options</legend>

	<table width="100%" border="0" cellspacing="0" cellpadding="6">


	<tr valign="top"><td width="25%" align="right">
		<strong>Before List</strong>
	</td><td align="left">
		<input name="ddlsp_before_list" type="text" size="40" value="<?php echo htmlspecialchars(get_option('ddlsp_before_list')); ?>"/>
		<br /><i>Added before the list of pages</i>
	</td></tr>

	<tr valign="top"><td width="25%" align="right">
		<strong>After List</strong>
	</td><td align="left">
		<input name="ddlsp_after_list" type="text" size="40" value="<?php echo htmlspecialchars(get_option('ddlsp_after_list')); ?>"/>
		<br /><i>Added after the list of pages</i>
	</td></tr>

	<tr><th width="25%" valign="top" align="right" scope="row">Sort Column</th><td valign="top">
	<input name="ddlsp_sort_column" type="radio" value="post_title" <?php if (get_option('ddlsp_sort_column') == "post_title") echo "checked='checked'"; ?> />&nbsp;&nbsp;
	Page Title<br />
	<input name="ddlsp_sort_column" type="radio" value="menu_order" <?php if (get_option('ddlsp_sort_column') == "menu_order") echo "checked='checked'"; ?> />&nbsp;&nbsp;
	Menu Order<br />
	<input name="ddlsp_sort_column" type="radio" value="post_date" <?php if (get_option('ddlsp_sort_column') == "post_date") echo "checked='checked'"; ?> />&nbsp;&nbsp;
	Page Date<br />
	<input name="ddlsp_sort_column" type="radio" value="post_modified" <?php if (get_option('ddlsp_sort_column') == "post_modified") echo "checked='checked'"; ?> />&nbsp;&nbsp;
	Page Modified<br />
	<input name="ddlsp_sort_column" type="radio" value="ID" <?php if (get_option('ddlsp_sort_column') == "ID") echo "checked='checked'"; ?> />&nbsp;&nbsp;
	Page ID<br />
	<input name="ddlsp_sort_column" type="radio" value="post_author" <?php if (get_option('ddlsp_sort_column') == "post_author") echo "checked='checked'"; ?> />&nbsp;&nbsp;
	Page Author<br />
	<input name="ddlsp_sort_column" type="radio" value="post_name" <?php if (get_option('ddlsp_sort_column') == "post_name") echo "checked='checked'"; ?> />&nbsp;&nbsp;
	Page Name (slug)<br />
	</td></tr>


	<tr><th width="25%" valign="top" align="right" scope="row">Sort Order</th><td valign="top">
	<input name="ddlsp_sort_order" type="radio" value="asc" <?php if (get_option('ddlsp_sort_order') == "asc") echo "checked='checked'"; ?> />&nbsp;&nbsp;
	Ascending<br />
	<input name="ddlsp_sort_order" type="radio" value="desc" <?php if (get_option('ddlsp_sort_order') == "desc") echo "checked='checked'"; ?> />&nbsp;&nbsp;
	Descending<br />
	</td></tr>

	<tr valign="top"><td width="25%" align="right">
		<strong>Excluded Items</strong>
	</td><td align="left">
		<input name="ddlsp_exclude" type="text" size="40" value="<?php echo htmlspecialchars(get_option('ddlsp_exclude')); ?>"/>
		<br /><i>Comma-separated list of page IDs to exclude from the list</i>
	</td></tr>

	<tr valign="top"><td width="25%" align="right">
		<strong>Included Items</strong>
	</td><td align="left">
		<input name="ddlsp_include" type="text" size="40" value="<?php echo htmlspecialchars(get_option('ddlsp_include')); ?>"/>
		<br /><i>Comma-separated list of page IDs to <strong>only</strong> include</i>
	</td></tr>

	<tr valign="top"><td width="25%" align="right">
		<strong>Page Depth</strong>
	</td><td align="left">
		<input name="ddlsp_depth" type="text" size="10" value="<?php echo get_option('ddlsp_depth'); ?>"/>
		<br /><i>(use 0 for no limit)</i>
	</td></tr>

	<tr valign="top"><td width="25%" align="right">
		<strong>Show Date</strong>
	</td><td align="left">
		<input type="checkbox" name="ddlsp_show_date" value="checkbox" <?php if (get_option('ddlsp_show_date')) echo "checked='checked'"; ?>/>&nbsp;&nbsp;
		If enabled, the date is shown after each page listed
	</td></tr>

	<tr valign="top"><td width="25%" align="right">
		<strong>Date Format</strong>
	</td><td align="left">
		<input name="ddlsp_date_format" type="text" size="40" value="<?php echo get_option('ddlsp_date_format'); ?>"/>
		<br /><i>If showing the date, this is the <a href="http://us2.php.net/date">PHP date format</a> that will be used</i>
	</td></tr>

	<tr valign="top"><td width="25%" align="right">
		<strong>List Title</strong>
	</td><td align="left">
		<input name="ddlsp_title_li" type="text" size="40" value="<?php echo get_option('ddlsp_title_li'); ?>"/>
		<br /><i>This is an optional header for the list of pages</i>
	</td></tr>

	</table> 
	</fieldset>

	<div class="submit">
		<input type="submit" name="set_defaults" value="<?php _e('Load Default Options'); ?> &raquo;" />
		<input type="submit" name="info_update" value="<?php _e('Update options'); ?> &raquo;" />
	</div>

	</form>
	</div><?php
}



function ddlsp_generate() {

	$before_list = get_option('ddlsp_before_list');
	$after_list = get_option('ddlsp_after_list');
	$sort_column = get_option('ddlsp_sort_column');
	$sort_order = get_option('ddlsp_sort_order');
	$exclude = get_option('ddlsp_exclude');
	$include = get_option('ddlsp_include');
	$depth = get_option('ddlsp_depth');
	$show_date = get_option('ddlsp_show_date');
	$date_format = get_option('ddlsp_date_format');
	$title_li = get_option('ddlsp_title_li');

	$t_out .= '';

	if (is_page()) {

		global $wp_query;
		$thePostID = $wp_query->post->ID;

		$opt = array();

		$opt[] = "echo=0";

		$opt[] = "child_of=" . $thePostID;

		$opt[] = "sort_column=" . $sort_column;

		$opt[] = "sort_order=" . $sort_order;

		if ($exclude != '') {
			$opt[] = "exclude=" . $exclude;
		}

		if ($include != '') {
			$opt[] = "include=" . $include;
		}

		if ($depth == '') $depth = 1;
			
		$opt[] = "depth=" . $depth;

		if ($show_date) {
			$opt[] = "show_date=true";
			if ($date_format != '') {
				$opt[] = "date_format=" . $date_format;
			}
		}

		$opt[] = "title_li=" . $title_li; 

		$opt_string = implode('&', $opt);	

		$t_out .= wp_list_pages($opt_string);

		if (trim($t_out) != '') {
			$t_out = $before_list . $t_out . $after_list;
		}

	}

	return $t_out;

}



function ddlsp_process($content) {


	if (strpos($content, "<!-- ddlsp -->") !== FALSE) {
		$content = preg_replace('/<p>\s*<!--(.*)-->\s*<\/p>/i', "<!--$1-->", $content); 
		$content = str_replace("<!-- ddlsp -->", ddlsp_generate(), $content);
	}

	return $content;

}


add_filter('the_content', 'ddlsp_process');
add_action('admin_menu', 'ddlsp_add_option_pages');

?>