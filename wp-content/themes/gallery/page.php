<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package Gallery
 */

get_header(); ?>

<div id="container" class="cf">

	<div id="content">

		<?php if( have_posts() ): while ( have_posts() ) : the_post(); ?>

			<?php get_template_part( 'content', 'page' ); ?>

		<?php endwhile; else: ?>

			<?php get_template_part( 'content', 'none' ); ?>

		<?php endif; ?>

	</div><!-- /#content -->

</div><!-- /#container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>