<?php
/**
 * The template for displaying Search results.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Gallery
 */

get_header(); ?>

  <div id="container" class="cf">

	<div id="content">

			<h1 class="page-title"><?php printf( __( 'Search Results for: %s', 'gallery' ), '<span>' . get_search_query() . '</span>' ); ?></h1>
		

		<?php echo get_search_form(); ?>

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