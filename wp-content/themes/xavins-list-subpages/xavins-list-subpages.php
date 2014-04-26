<?php
/*
Plugin Name: Xavin's List Subpages
Plugin URI: http://www.jonathanspence.com/software/wordpress-plugins/xavins-list-subpages/
Description: Adds a shortcode tag [xls] to display a list of the subpages of the current page.
Version: 1.3
Author: Jonathan 'Xavin' Spence
Author URI: http://www.jonathanspence.com/
*/

/*  Copyright 2008  Jonathan Spence  (email : gpl@jonathanspence.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// gets the list HTML
function xls_generate($args = '') {
		
	$r = shortcode_atts( array(
		'depth'       => 0,
		'show_date'   => '',
		'date_format' => get_option('date_format'),
		'child_of'    => -1,
		'exclude'     => '',
		'echo'		  => 0,
		'title_li'    => '',
		'authors'     => '',
		'sort_column' => 'menu_order, post_title',
		'css_class'   => 'xls_list',
		'escape'	  => 'false'), $args );
	
	if ($r['escape'] != 'true') {
		global $wp_query;
		$postID = $wp_query->post->ID;	
		if ($r['child_of'] == -1) 	
			$r['child_of'] = $postID;
			
		$r['echo'] = 0;
		$r['title_li'] = '';

		$children = wp_list_pages($r);
		
		$content = '<ul class="'.$r['css_class'].'">'.wp_list_pages($r).'</ul>';
		
	} else {
		// escape is true, we want to show the shortcode instead of processing it
		$content = '[xls';
		foreach ($args as $key => $value) {
			if ($key != 'escape')
				$content .= ' '.$key.'="'.$value.'"';
		}
		$content .= ']';
	}
	
	return $content;	
}

add_shortcode('xls', 'xls_generate');
?>