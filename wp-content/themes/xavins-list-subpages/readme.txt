=== Xavin's List Subpages ===
Contributors: XavinNydek
Donate link: http://www.jonathanspence.com/software/wordpress-plugins/xavins-list-subpages/
Tags: page, pages, post, formatting, list, shortcode
Requires at least: 2.5.1
Tested up to: 2.6.2
Stable tag: 1.3

Adds a tag that you can use in your pages to display a list of it's subpages.

== Description ==

This plugin adds the ability to put a tag in your page entry and have it display a list of pages. By deafult it will
show the subpages of the page that it is on, but it will accept any of the options the 
[`wp_list_pages`](http://codex.wordpress.org/Template_Tags/wp_list_pages) template tag
will except `title_li` and `echo`. It is very simple to use, just put `[xls]` in the entry for your page where you want the list to appear. 

There is one option in addition to the `wp_list_pages` ones. Specifying a `css_class` will override the default of 
`xls_list` for the class of the surrounding `ul`. 

It supports multiple uses of the tag with different options on one page.

Here are a few examples of valid tags:

`[xls depth=2]`
Shows two levels of the subpages of the current page.

`[xls child_of=10 exclude="13, 15, 33"]`
Shows all children of the page with id 10, excluding pages 13, 15, and 33.

`[xls child_of="0" show_date="modified"]`
Shows all pages, and shows the last modified date of each page.

Check the [`wp_list_pages`](http://codex.wordpress.org/Template_Tags/wp_list_pages) documentation for all of the possible options.

== Installation ==

1. Upload `xavins-list-subpages.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Place `[xls]` in your pages. Check the [plugin homepage](http://www.jonathanspence.com/software/xavins-list-subpages/) for
detailed options and more complex uses.

== Frequently Asked Questions ==

Check the [plugin homepage](http://www.jonathanspence.com/software/xavins-list-subpages/)
