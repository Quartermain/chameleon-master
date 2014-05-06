<?php
/**
 * The Template for displaying all single posts.
 *
 * @package Gallery
 */

get_header(); ?>

	<div id="container" class="cf">

		<div id="content">

		<?php if( have_posts() ): while ( have_posts() ) : the_post(); ?>

			<?php get_template_part( 'content', 'single' ); ?>

		<?php endwhile; else: ?>

			<?php get_template_part( 'content', 'none' ); ?>

		<?php endif; ?>

		</div><!-- /#content -->

	</div><!-- /#container -->
<?php get_sidebar(); ?>
<?php get_footer(); ?>