<?php
/**
 * Internationalization and translation functions.  Because Hybrid Core is a framework made up of various 
 * extensions with different textdomains, it must filter 'gettext' so that a single translation file can 
 * handle all translations.
 *
 * @package HybridCore
 * @subpackage Functions
 * @author Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2008 - 2012, Justin Tadlock
 * @link http://themehybrid.com/supreme-core
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */
/**
 * Checks if a textdomain's translation files have been loaded.  This function behaves differently from 
 * WordPress core's is_textdomain_loaded(), which will return true after any translation function is run over 
 * a text string with the given domain.  The purpose of this function is to simply check if the translation files 
 * are loaded.
 *
 * @since 1.3.0
 * @access private This is only used internally by the framework for checking translations.
 * @param string $domain The textdomain to check translations for.
 */
function supreme_is_textdomain_loaded( $domain ) {
	global $supreme;
	return ( isset( $supreme->textdomain_loaded[$domain] ) && true === $supreme->textdomain_loaded[$domain] ) ? true : false;
}
/**
 * Loads the framework's translation files.  The function first checks if the parent theme or child theme 
 * has the translation files housed in their '/languages' folder.  If not, it sets the translation file the the 
 * framework '/languages' folder.
 *
 * @since 1.3.0
 * @access private
 * @uses load_textdomain() Loads an MO file into the domain for the framework.
 * @param string $domain The name of the framework's textdomain.
 * @return true|false Whether the MO file was loaded.
 */
function supreme_load_framework_textdomain( $domain ) {
	/* Get the WordPress installation's locale set by the user. */
	$locale = get_locale();
	/* Check if the mofile is located in parent/child theme /languages folder. */
	//$mofile = locate_template( array( "languages/{$domain}-{$locale}.mo" ) );
	$mofile = locate_template( array( "languages/{$locale}.mo" ) );
	/* If no mofile was found in the parent/child theme, set it to the framework's mofile. */
	if ( empty( $mofile ) )
		$mofile = trailingslashit( SUPREME_LANGUAGES ) . "{$locale}.mo";
		//$mofile = trailingslashit( SUPREME_LANGUAGES ) . "{$domain}-{$locale}.mo";
	return load_theme_textdomain( $domain, $mofile );
}
/**
 * @since 0.7.0
 * @deprecated 1.3.0
 */
function supreme_get_textdomain() {
	_deprecated_function( __FUNCTION__, '1.3.0', 'supreme_get_parent_textdomain' );
	return supreme_get_parent_textdomain();
}
/**
 * Gets the parent theme textdomain. This allows the framework to recognize the proper textdomain of the 
 * parent theme.
 *
 * Important! Do not use this for translation functions in your theme.  Hardcode your textdomain string.  Your 
 * theme's textdomain should match your theme's folder name.
 *
 * @since 1.3.0
 * @access private
 * @uses get_template() Defines the theme textdomain based on the template directory.
 * @global object $supreme The global Hybrid object.
 * @return string $supreme->textdomain The textdomain of the theme.
 */
function supreme_get_parent_textdomain() {
	global $supreme;
	/* If the global textdomain isn't set, define it. Plugin/theme authors may also define a custom textdomain. */
	if ( empty( $supreme->parent_textdomain ) )
		$supreme->parent_textdomain = sanitize_key( apply_filters( supreme_prefix() . '_parent_textdomain', get_template() ) );
	/* Return the expected textdomain of the parent theme. */
	return $supreme->parent_textdomain;
}
/**
 * Gets the child theme textdomain. This allows the framework to recognize the proper textdomain of the 
 * child theme.
 *
 * Important! Do not use this for translation functions in your theme.  Hardcode your textdomain string.  Your 
 * theme's textdomain should match your theme's folder name.
 *
 * @since 1.2.0
 * @access private
 * @uses get_stylesheet() Defines the child theme textdomain based on the stylesheet directory.
 * @global object $supreme The global Hybrid object.
 * @return string $supreme->child_theme_textdomain The textdomain of the child theme.
 */
function supreme_get_child_textdomain() {
	global $supreme;
	/* If a child theme isn't active, return an empty string. */
	if ( !is_child_theme() )
		return '';
	/* If the global textdomain isn't set, define it. Plugin/theme authors may also define a custom textdomain. */
	if ( empty( $supreme->child_textdomain ) )
		$supreme->child_textdomain = sanitize_key( apply_filters( supreme_prefix() . '_child_textdomain', get_stylesheet() ) );
	/* Return the expected textdomain of the child theme. */
	return $supreme->child_textdomain;
}
/**
 * Filters the 'load_textdomain_mofile' filter hook so that we can change the directory and file name 
 * of the mofile for translations.  This allows child themes to have a folder called /languages with translations
 * of their parent theme so that the translations aren't lost on a parent theme upgrade.
 */
function supreme_load_textdomain_mofile( $mofile, $domain ) {
	/* If the $domain is for the parent or child theme, search for a $domain-$locale.mo file. */
	if ( $domain == supreme_get_parent_textdomain() || $domain == supreme_get_child_textdomain() ) {
		/* Check for a $domain-$locale.mo file in the parent and child theme root and /languages folder. */
		$locale = get_locale();
		$locate_mofile = locate_template( array( "languages/{$locale}.mo", "{$locale}.mo" ) );
		//$locate_mofile = locate_template( array( "languages/supreme-en_EN.mo", "supreme-en_EN.mo" ) );
		//$locate_mofile = locate_template( array( "languages/theme+supreme.mo", "theme+supreme.mo" ) );
		/* If a mofile was found based on the given format, set $mofile to that file name. */
		if ( !empty( $locate_mofile ) )
			$mofile = $locate_mofile;
	}
	/* Return the $mofile string. */
	return $mofile;
}
/**
 * Filters 'gettext' to change the translations used for the THEME_DOMAIN textdomain.  This filter makes it possible 
 * for the theme's MO file to translate the framework's text strings.
 */
function supreme_gettext( $translated, $text, $domain ) {
	/* Check if THEME_DOMAIN is the current textdomain, there's no mofile for it, and the theme has a mofile. */
	if ( THEME_DOMAIN == $domain && !supreme_is_textdomain_loaded( THEME_DOMAIN ) && supreme_is_textdomain_loaded( supreme_get_parent_textdomain() ) ) {
		/* Get the translations for the theme. */
		$translations = &get_translations_for_domain( supreme_get_parent_textdomain() );
		/* Translate the text using the theme's translation. */
		$translated = $translations->translate( $text );
	}
	return $translated;
}
/**
 * Filters 'gettext' to change the translations used for the each of the extensions' textdomains.  This filter 
 * makes it possible for the theme's MO file to translate the framework's extensions.
 */
function supreme_extensions_gettext( $translated, $text, $domain ) {
	/* Check if the current textdomain matches one of the framework extensions. */
	if ( in_array( $domain, array( 'breadcrumb-trail', 'custom-field-series', 'post-stylesheets', 'theme-layouts' ) ) ) {
		/* If the theme supports the extension, switch the translations. */
		if ( current_theme_supports( $domain ) ) {
			/* If the framework mofile is loaded, use its translations. */
			if ( supreme_is_textdomain_loaded( THEME_DOMAIN ) )
				$translations = &get_translations_for_domain( THEME_DOMAIN );
			/* If the theme mofile is loaded, use its translations. */
			elseif ( supreme_is_textdomain_loaded( supreme_get_parent_textdomain() ) )
				$translations = &get_translations_for_domain( supreme_get_parent_textdomain() );
			/* If translations were found, translate the text. */
			if ( !empty( $translations ) )
				$translated = $translations->translate( $text );
		}
	}
	return $translated;
}
?>