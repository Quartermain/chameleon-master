<?php
/**
 * Jetpack Compatibility File
 * See: http://jetpack.me/
 *
 * @package Gallery
 */

/**
 * Add theme support for Infinite Scroll.
 * See: http://jetpack.me/support/infinite-scroll/
 */
function gallery_jetpack_setup() {
	add_theme_support( 'infinite-scroll', array(
		'container' => 'masonry',
		'footer'    => 'footer',
		'posts_per_page'    => 20,
	) );
}
add_action( 'after_setup_theme', 'gallery_jetpack_setup' );

/**
 * Add social links to nav menu
 *
 * Hooks into wp_nav_menu_items and adds social links
 * since this theme does not use widgets.
 *
 * @return $items All nav menu items
 */
function gallery_add_social_links($items, $args) {

		$facebook 	= get_theme_mod( 'jetpack-facebook' );
		$twitter 	= get_theme_mod( 'jetpack-twitter' );
		$google 	= get_theme_mod( 'jetpack-google_plus' );
		$linkedin 	= get_theme_mod( 'jetpack-linkedin' );
		$tumblr 	= get_theme_mod( 'jetpack-tumblr' );

		$social_links_inner = '';

		// Add Facebook
		if( $facebook ){
			$social_links_inner .= '<a href="' . esc_url( $facebook ) . '"><i class="genericon genericon-facebook"></i></a>';
		}

		// Add Twitter
		if( $twitter ){
			$social_links_inner .= '<a href="' . esc_url( $twitter ) . '"><i class="genericon genericon-twitter"></i></a>';
		}

		// Add Google+
		if( $google ){
			$social_links_inner .= '<a href="' . esc_url( $google ) . '"><i class="genericon genericon-googleplus"></i></a>';
		}

		// Add LinkedIn
		if( $linkedin ){
			$social_links_inner .= '<a href="' . esc_url( $linkedin ) . '"><i class="genericon genericon-linkedin"></i></a>';
		}

		// Add Tumblr
		if( $tumblr ){
			$social_links_inner .= '<a href="' . esc_url( $tumblr ) . '"><i class="genericon genericon-tumblr"></i></a>';
		}

		if( ! empty( $social_links_inner ) ){
			$items .= '<li class="social-links">' . $social_links_inner . '</li>';
		}

	return $items;
}
add_filter('wp_nav_menu_items','gallery_add_social_links', 10, 2);