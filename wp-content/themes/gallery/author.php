<?php
/**
 * The template for displaying Author Archive pages.
 *
 * Used to display author archive-type pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Gallery
 */

get_header(); ?>

	<div id="container" class="cf">

		<div id="content">

			<?php get_template_part('author-bio'); ?>

			<div id="masonry">

				<?php if( have_posts() ): while ( have_posts() ) : the_post(); ?>

				<?php get_template_part( 'content', get_post_format() ); ?>

				<?php endwhile; else: ?>

				<?php get_template_part( 'content', 'none' ); ?>

				<?php endif; ?>

			</div><!-- /#masonry -->

		</div><!-- /#content -->

		<?php gallery_content_nav( 'nav-below' ); ?>

	</div><!-- /#container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>