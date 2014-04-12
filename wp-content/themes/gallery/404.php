<?php
/**
 * The template for displaying a 404 message.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Gallery
 */

get_header(); ?>

	<div id="container" class="cf">

		<div id="content">

			<?php get_template_part( 'content', 'none' ); ?>

		</div><!-- /#content -->

	</div><!-- /#container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>