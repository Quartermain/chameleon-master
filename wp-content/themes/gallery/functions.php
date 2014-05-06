<?php
/**
 * Gallery functions and definitions.
 *
 * Sets up the theme and provides some helper functions, which are used
 * in the theme as custom template tags. Others are attached to action and
 * filter hooks in WordPress to change core functionality.
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development and
 * http://codex.wordpress.org/Child_Themes), you can override certain functions
 * (those wrapped in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before the parent
 * theme's file, so the child theme functions would be used.
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are instead attached
 * to a filter or action hook.
 *
 * For more information on hooks, actions, and filters, see http://codex.wordpress.org/Plugin_API.
 *
 * @package Gallery
 */

if( !defined('IS_WPCOM') && file_exists( get_template_directory() . '/inc/UpThemes_Theme_Updater.php' ) ){

	require_once( get_template_directory() . '/inc/UpThemes_Theme_Updater.php' );

	// Define variables for our theme updates
	define('UPTHEMES_LICENSE_KEY','gallery_theme');
	define('UPTHEMES_ITEM_NAME', 'Gallery Theme');
	define('UPTHEMES_STORE_URL', 'https://upthemes.com');

	function amplify_theme_update_check(){
		$upthemes_license = trim( get_option( UPTHEMES_LICENSE_KEY ) );

		$edd_updater = new UpThemes_Theme_Updater(
			array(
				'remote_api_url'	=> UPTHEMES_STORE_URL,	// Our store URL that is running EDD
				'license'			=> $upthemes_license,	// The license key (used get_option above to retrieve from DB)
				'item_name'			=> UPTHEMES_ITEM_NAME,	// The name of this theme
				'author'			=> 'UpThemes'
			)
		);
	}
	add_action('admin_init','amplify_theme_update_check',1);

}

if( !isset( $content_width ) ){
	$content_width = 640;
}

/**
 * Adds support for a custom header image.
 */
require_once( get_template_directory() . '/inc/custom-header.php' );

/**
 * Adds support for custom gallery sliders
 */
require_once( get_template_directory() . '/inc/gallery-slider.php' );

/**
 * Media-related functions for theme
 */
require_once( get_template_directory() . '/inc/media.php' );

/**
 * Adds support for Jetpack features
 */
require_once( get_template_directory() . '/inc/jetpack.php' );

/**
 * Template functions for this theme
 */
require_once( get_template_directory() . '/inc/template-tags.php' );

/**
 * Sets up theme defaults and registers the various WordPress features that
 * Gallery supports.
 *
 * @uses load_theme_textdomain() For translation/localization support.
 * @uses add_editor_style() To add a Visual Editor stylesheet.
 * @uses add_theme_support() To add support for post thumbnails, automatic feed links,
 * 	custom background, and post formats.
 * @uses register_nav_menu() To add support for navigation menus.
 * @uses add_image_size() Defines custom image sizes.
 */
function gallery_setup() {
	/*
	 * Makes Gallery available for translation.
	 *
	 * Translations can be added to the /languages/ directory.
	 * If you're building a theme based on Gallery, use a find and replace
	 * to change 'gallery' to the name of your theme in all the template files.
	 */
	load_theme_textdomain( 'gallery', get_template_directory() . '/languages' );

	// This theme styles the visual editor with editor-style.css to match the theme style.
	add_editor_style( array( 'editor-style.css', gallery_fonts_url(), get_template_directory_uri() . '/assets/fonts/league-gothic/stylesheet.css' ) );

	// This theme uses a custom image size for featured images, displayed on "standard" posts.
	add_theme_support( 'post-thumbnails' );

	// Adds RSS feed links to <head> for posts and comments.
	add_theme_support( 'automatic-feed-links' );

	// This theme supports a variety of post formats.
	add_theme_support( 'post-formats', array( 'aside', 'audio', 'video', 'image', 'link', 'quote', 'status', 'gallery' ) );

	// Add support for social links
	add_theme_support( 'social-links', array(
		'facebook', 'twitter', 'linkedin', 'google_plus', 'tumblr'
	) );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menu( 'primary_nav', __( 'Primary Menu', 'gallery' ) );

	add_image_size('full-width',700,99999,false);
	add_image_size('full-width-2x',1400,99999,false);
	add_image_size('grid',300,9999,false);
	add_image_size('grid-2x',600,9999,false);

	/*
	 * This theme supports custom background color and image, and here
	 * we also set up the default background color.
	 */
	add_theme_support( 'custom-background', array(
		'default-color' => 'ffffff',
	) );

}
add_action( 'after_setup_theme', 'gallery_setup' );

/**
 * Register sidebars
 *
 * Creates the sidebars for this theme.
 */
function gallery_register_sidebars(){

	register_sidebar( array(
		'name'			=> __( 'Global Sidebar Above Footer', 'gallery' ),
		'id'			=> 'sidebar-footer',
		'description'	=> __('Widgets in this area will be shown above the footer on all pages except single posts.','gallery'),
		'before_widget'	=> '<li id="%1$s" class="widget %2$s">',
		'after_widget'	=> '</li>',
		'before_title'	=> '<h2 class="widgettitle">',
		'after_title'	=> '</h2>'
	) );

	register_sidebar( array(
		'name'			=> __( 'Sidebar on Single Posts', 'gallery' ),
		'id'			=> 'sidebar-single',
		'description'	=> __('Widgets in this area will be shown below posts but above comments on single posts.','gallery'),
		'before_widget'	=> '<li id="%1$s" class="widget %2$s">',
		'after_widget'	=> '</li>',
		'before_title'	=> '<h2 class="widgettitle">',
		'after_title'	=> '</h2>'
	) );

}

add_action('widgets_init','gallery_register_sidebars');

/**
 * Return custom excerpt length
 */
function gallery_excerpt_length( $length ) {
	return 20;
}
add_filter( 'excerpt_length', 'gallery_excerpt_length', 999 );

/**
 * Return custom more ellipsis
 */
function gallery_excerpt_more( $more ) {
	return ' &hellip;';
}
add_filter('excerpt_more', 'gallery_excerpt_more');

/**
 * Count the number of widgets in a sidebar
 */
function gallery_widget_count( $sidebar_index ) {
    global $wp_registered_sidebars;

    $index = "sidebar-{$sidebar_index}";

    $sidebars_widgets = wp_get_sidebars_widgets();
    if ( empty($wp_registered_sidebars[$index]) || !array_key_exists($index, $sidebars_widgets) || !is_array($sidebars_widgets[$index]) || empty($sidebars_widgets[$index]) )
        return 0;

    return count( (array) $sidebars_widgets[$index] );
}

/**
 * Filters the post class
 *
 * Adds a class for 'has-featured-image' if a featured
 * image has been applied.
 *
 * @return $classes All post classes
 */
function gallery_if_featured_image_class( $classes ) {
	if ( has_post_thumbnail( get_the_ID() ) ) {
		$classes[] = 'has-featured-image';
	}

	return $classes;
}
add_filter('post_class', 'gallery_if_featured_image_class' );

/**
 * Filters the body class
 *
 * Adds a class for 'has-header-image' if a header
 * image has been applied.
 *
 * @return $classes All body classes
 */
function gallery_custom_header_body_class($classes){
	if ( get_header_image() != '' ){
		$classes[] = 'has-header-image';
	}

	return $classes;
}
add_filter('body_class','gallery_custom_header_body_class');

/**
 * Enqueues scripts and styles for front-end.
 */
function gallery_scripts_styles() {

	/*
	 * Adds JavaScript to pages with the comment form to support
	 * sites with threaded comments (when in use).
	 */
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ){
		wp_enqueue_script( 'comment-reply' );
	}

	// Sticky Plugin
	wp_enqueue_script( 'gallery-sticky', get_template_directory_uri() . '/assets/js/jquery.sticky.js', array('jquery') );

	// Masonry
	wp_enqueue_script( 'jquery-masonry', get_template_directory_uri() . '/assets/js/jquery.masonry.js', array('jquery') );

	// Fitvids.js for responsive video embeds
	wp_enqueue_script( 'gallery-fitvids', get_template_directory_uri() . '/assets/js/fitvids.js' );

	// Flexslider for tiny galleries inside Masonry layouts
	wp_enqueue_script( 'gallery-flexslider', get_template_directory_uri() . '/assets/js/jquery.flexslider.js', array('jquery') );

	// Global styles for theme
	wp_enqueue_script( 'gallery-global', get_template_directory_uri() . '/assets/js/global.js', array('jquery','gallery-sticky','gallery-flexslider','gallery-fitvids') );

	// Custom Fonts
	wp_enqueue_style( 'gallery-fonts', gallery_fonts_url(), false );

	// Icons
	wp_enqueue_style( 'genericons', get_template_directory_uri() . '/assets/fonts/genericons/genericons.css', false );

	/*
	 * Loads our main stylesheet.
	 */
	wp_enqueue_style( 'gallery-style', get_stylesheet_uri() );
}
add_action( 'wp_enqueue_scripts', 'gallery_scripts_styles' );

/**
 * Enqueues League Gothic font.
 */
function gallery_enqueue_custom_font() {
	/* Translators: If there are characters in your language that are not
	 * supported by Inconsolata, translate this to 'off'. Do not translate into your
	 * own language.
	 */
	$league_gothic = _x( 'on', 'League Gothic font: on or off', 'gallery' );

	if( $league_gothic != 'off' ){
		wp_enqueue_style( 'gallery-league-gothic-font', get_template_directory_uri() . '/assets/fonts/league-gothic/stylesheet.css', false );
	}
}
add_action( 'wp_enqueue_scripts', 'gallery_enqueue_custom_font' );

/**
 * Makes our wp_nav_menu() fallback -- wp_page_menu() -- show a home link.
 */
function gallery_page_menu_args( $args ) {
	if ( ! isset( $args['show_home'] ) )
		$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'gallery_page_menu_args' );

/**
 * Returns the Google font stylesheet URL, if available.
 *
 * The use of Source Sans Pro and Inconsolata by default is localized. For languages
 * that use characters not supported by the font, the font can be disabled.
 *
 * @return string Font stylesheet or empty string if disabled.
 */
function gallery_fonts_url() {
	$fonts_url = '';

	/* Translators: If there are characters in your language that are not
	 * supported by Source Sans Pro, translate this to 'off'. Do not translate
	 * into your own language.
	 */
	$source_sans_pro = _x( 'on', 'Source Sans Pro font: on or off', 'gallery' );

	/* Translators: If there are characters in your language that are not
	 * supported by Inconsolata, translate this to 'off'. Do not translate into your
	 * own language.
	 */
	$inconsolata = _x( 'on', 'Inconsolata font: on or off', 'gallery' );

	if ( 'off' !== $source_sans_pro || 'off' !== $open_sans ) {
		$font_families = array();

		if ( 'off' !== $source_sans_pro )
			$font_families[] = 'Source+Sans+Pro:300,400,700,400italic';

		if ( 'off' !== $inconsolata )
			$font_families[] = 'Inconsolata';

		$query_args = array(
			'family' => urlencode( implode( '|', $font_families ) ),
			'subset' => urlencode( 'latin,latin-ext' ),
		);
		$fonts_url = add_query_arg( $query_args, "//fonts.googleapis.com/css" );
	}

	return $fonts_url;
}

/**
 * Fallback page menu
 *
 * @uses wp_page_menu() with custom artguments to display a nicer menu for
 * our users
 *
 * Adds a home link titled 'Home' and adds a 'menu' class for styling purposes.
 */
function gallery_page_menu() {
	wp_page_menu(array(
		'show_home' => __('Home','gallery'),
		'menu_class' => 'menu'
	));
}

/**
 * Add search box to nav menu.
 *
 * Hooks into wp_nav_menu_items and adds a search box
 * since this theme does not use widgets.
 *
 * @return $items All nav menu items
 */

function gallery_add_search_box($items, $args) {

		$items .= '<li  class="search-box-wrapper">' . get_search_form(false) . '</li>';
		$items .= '<li  class="mail-chip"></li>';
	return $items;
}
add_filter('wp_nav_menu_items','gallery_add_search_box', 10, 2);