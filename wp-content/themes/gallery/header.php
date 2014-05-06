<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="wrapper">
 *
 * @package Gallery
 */
?><!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?> class="no-js">
<!--<![endif]-->
<head>
<script>document.getElementsByTagName('html')[0].className = 'js';</script>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width" />
<title><?php wp_title( '|', true, 'right' ); ?></title>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<link rel="shortcut icon" href="<?php echo get_stylesheet_directory_uri(); ?>/favicon.ico" />
<?php wp_head(); ?>
</head>

<body <?php body_class('loading'); ?>>

	<div id="header">

	<div class="shade cf">

		<div id="branding">
			<div id="blog-title">
                                <a href="<?php echo get_site_url(); ?>" title="<?php bloginfo('name') ?>" rel="home">
                                    <img  src="<?php echo get_bloginfo('template_directory');?>/assets/img/logo.png"/>
                                </a>
                        
                        </div>
			<div id="blog-description"><?php bloginfo('description'); ?></div>
		</div><!-- /#branding -->

	</div><!-- .shade -->

	</div><!-- #header-->
	<div id="access">
		<div class="menu-top">
			<nav>
				
				<?php
				 if( function_exists( 'mc4wp_form' ) ) {
    mc4wp_form();
}
				wp_nav_menu(array(
					'theme_location'  => 'primary_nav',
					'container_class' => 'menu',
					'menu_class'      => false,
					'fallback_cb'     => 'gallery_page_menu'
				));
				?>
			</nav>
	  	</div><!-- #access -->
	</div>
  <div id="wrapper" class="hfeed">
      <script>
          jQuery(document).ready(function(){
              jQuery( ".mc4wp-form" ).appendTo( ".menu .mail-chip" );
          })
  </script>