<?php
/**
 * Implements an optional custom header for Gallery.
 * See http://codex.wordpress.org/Custom_Headers
 *
 * @package Gallery
 */

/**
 * Sets up the WordPress core custom header arguments and settings.
 *
 * @uses add_theme_support() to register support for 3.4 and up.
 * @uses gallery_header_style() to style front-end.
 * @uses gallery_admin_header_style() to style wp-admin form.
 * @uses gallery_admin_header_image() to add custom markup to wp-admin form.
 */
function gallery_custom_header_setup() {
	$args = array(
		// Text color and image (empty to use none).
		'default-text-color'     => '444',

		// Set height and width, with a maximum value for the width.
		'height'                 => 400,
		'width'                  => 1800,
		'max-width'              => 1800,

		// Support flexible height and width.
		'flex-height'            => true,
		'flex-width'             => true,

		// Random image rotation off by default.
		'random-default'         => false,

		// Callbacks for styling the header and the admin preview.
		'wp-head-callback'       => 'gallery_header_style',
		'admin-head-callback'    => 'gallery_admin_header_style',
		'admin-preview-callback' => 'gallery_admin_header_image',
	);

	add_theme_support( 'custom-header', $args );

	// Default custom headers packaged with the theme. %s is a placeholder for the theme template directory URI.
	register_default_headers(
		array(
			'capitol' => array(
				'url' => '%s/assets/img/capitol.jpg',
				'thumbnail_url' => '%s/assets/img/capitol-thumb.jpg',
				/* translators: header image description */
				'description' => __( 'Capitol', 'photolia' )
			)
		)
	);
}
add_action( 'after_setup_theme', 'gallery_custom_header_setup' );

/**
 * Styles the header text displayed on the blog.
 *
 * get_header_textcolor() options: 444 is default, hide text (returns 'blank'), or any hex value.
 */
function gallery_header_style() {

	$header_image = get_header_image();
	$text_color = get_header_textcolor();

	// Check for retina version, then replace the extension with @2x
	$header_image_retina = preg_replace("/\.(jpeg|jpg|png|gif)$/","@2x.$1",$header_image);

	if( function_exists('getimagesize') && is_array( @getimagesize( $header_image_retina ) ) ){
		$retina = true;
	} else{
		$retina = false;
	}

	if( $header_image ){
?>
	<style type="text/css" id="custom-header-bg-image">

	#header {
	  background-image: url('<?php echo $header_image; ?>');
	}

<?php
		if( $retina ){
?>

	@media all and (-webkit-min-device-pixel-ratio: 1.5) {
	  #header {
	    background-image: url('<?php echo $header_image_retina; ?>');
	  }
	}

<?php
		}
?>

	</style>

<?php

	}

	// If no custom options for text are set, let's bail
	if ( $text_color == get_theme_support( 'custom-header', 'default-text-color' ) )
		return;

	// If we get this far, we have custom styles.
	?>
	<style type="text/css">
	<?php
		// Has the text been hidden?
		if ( ! display_header_text() ) :
	?>
		#blog-title,
		#blog-description {
			visibility: hidden;
		}
	<?php
		// If the user has set a custom color for the text, use that.
		else :
	?>
		#header #blog-title a,
		.has-header-image #header #blog-title a {
			color: <?php echo '#' . get_header_textcolor(); ?>;
		}

		#header #blog-description,
		.has-header-image #header #blog-description {
			color: <?php echo '#' . get_header_textcolor(); ?>;
		}
	<?php endif; ?>
	</style>
	<?php
}

add_action('wp_head','gallery_header_style');

/**
 * Styles the header image displayed on the Appearance > Header admin panel.
 */
function gallery_admin_header_style() {
?>
	<style type="text/css">
	@import url('<?php echo get_template_directory_uri(); ?>/assets/fonts/league-gothic/stylesheet.css');
	@import url('<?php echo gallery_fonts_url(); ?>');

	.cf:before,
	.cf:after {
	    content: " "; /* 1 */
	    display: table; /* 2 */
	}

	.cf:after {
	    clear: both;
	}

	.cf {
	    *zoom: 1;
	}

	.appearance_page_custom-header #header {
		border: none;
	}

	#header {
		margin-bottom: 60px;
		font-size: 14px;
		text-align: center;
		z-index: 9999;
		background-color: white;
	}

	#header a {
		text-decoration: none;
	}

	.has-header-image {
		background-size: cover;
	}

	#header .shade {
		padding: 7em 0 0;
	}

	#header.has-header-image .shade {
		background-color: rgba(0,0,0,.2);
	}

	#header #blog-title {
		position: relative;
		font-size: 3em;
		text-transform: uppercase;
		font-weight: 400;
		letter-spacing: 4px;
		font-family: "League Gothic";
		margin-bottom: .1em;
	}

	#header #blog-title a,
	.has-header-image #header #blog-title a {
		color: <?php echo '#' . get_header_textcolor(); ?>;
	}

	#header #blog-description,
	.has-header-image #header #blog-description {
		font-size: 1.2em;
		margin-bottom: 6em;
		text-transform: lowercase;
		font-family: "Inconsolata";
		color: <?php echo '#' . get_header_textcolor(); ?>;
	}

	<?php
		// Has the text been hidden?
		if ( ! display_header_text() ) {
	?>
		#header #blog-title,
		#header #blog-description {
			visibility: hidden;
		}
	<?php } ?>
	</style>
<?php
}

/**
 * Outputs markup to be displayed on the Appearance > Header admin panel.
 * This callback overrides the default markup displayed there.
 */
function gallery_admin_header_image() {
	$class = '';
	$header_style = '';
	$text_style = '';

	if ( display_header_text() ){
		$text_style = ' style="color:#' . get_header_textcolor() . ';"';
		$class = 'show-header-text';
	}

	if( get_header_image() ) {
		$class .= ' has-header-image';
	}

	if( $class != '' )
		$class = " class='$class'";

	if( get_header_image() )
		$header_style = ' style="background-image: url(' . esc_attr( get_header_image() ) . ');"';
?>

  <div id="header"<?php echo $class . $header_style; ?>>
    <div class="shade cf">

		<div id="branding">
			<div id="blog-title"<?php echo $text_style; ?>><span><a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php bloginfo('name') ?>" rel="home"><?php bloginfo('name') ?></a></span></div>
			<div id="blog-description"<?php echo $text_style; ?>><?php bloginfo('description'); ?></div>
		</div><!-- /#branding -->

    </div><!-- .shade -->
  </div><!-- #header-->
<?php }