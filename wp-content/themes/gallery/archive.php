<?php
/**
 * The template for displaying Archive pages.
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * If you'd like to further customize these archive views, you may create a
 * new template file for each specific one. For example, Gallery already
 * has tag.php for Tag archives, category.php for Category archives, and
 * author.php for Author archives.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Gallery
 */

get_header(); ?>

	<div id="container" class="cf">

		<div id="content">

			<header class="archive-header">
				<h1 class="archive-title">
					<?php
						if ( is_day() ) :
							printf( __( 'Daily Archives: %s', 'gallery' ), get_the_date() );

						elseif ( is_month() ) :
							printf( __( 'Monthly Archives: %s', 'gallery' ), get_the_date( _x( 'F Y', 'monthly archives date format', 'gallery' ) ) );

						elseif ( is_year() ) :
							printf( __( 'Yearly Archives: %s', 'gallery' ), get_the_date( _x( 'Y', 'yearly archives date format', 'gallery' ) ) );

						elseif( is_tag() ) :
							printf( __( '#%s', 'gallery' ), single_tag_title( '', false ) );

						elseif( is_category() ) :
							printf( __( 'Category Archives: %s', 'gallery' ), single_cat_title( '', false ) );

						elseif ( is_tax( 'post_format', 'post-format-aside' ) ) :
							_e( 'Asides', 'gallery' );

						elseif ( is_tax( 'post_format', 'post-format-image' ) ) :
							_e( 'Images', 'gallery' );

						elseif ( is_tax( 'post_format', 'post-format-video' ) ) :
							_e( 'Videos', 'gallery' );

						elseif ( is_tax( 'post_format', 'post-format-audio' ) ) :
							_e( 'Audio', 'gallery' );

						elseif ( is_tax( 'post_format', 'post-format-quote' ) ) :
							_e( 'Quotes', 'gallery' );

						elseif ( is_tax( 'post_format', 'post-format-status' ) ) :
							_e( 'Status Updates', 'gallery' );

						elseif ( is_tax( 'post_format', 'post-format-link' ) ) :
							_e( 'Links', 'gallery' );

						elseif ( is_tax( 'post_format', 'post-format-chat' ) ) :
							_e( 'Chats', 'gallery' );

						elseif ( is_tax( 'post_format', 'post-format-gallery' ) ) :
							_e( 'Galleries', 'gallery' );

						else :
							_e( 'Archives', 'gallery' );

						endif;
					?></h1>

					<?php
					// Show an optional term description.
					$term_description = term_description();
					if ( ! empty( $term_description ) ) :
						printf( '<div class="taxonomy-description">%s</div>', $term_description );
					endif; ?>

			</header><!-- .archive-header -->

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