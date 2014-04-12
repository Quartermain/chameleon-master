<?php
/**
 * The template for displaying chat content. Used within any masonry/archive/index template.
 *
 * @package Gallery
 */
?>

	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

		<?php gallery_before_post(); ?>

		<div class="panel">

			<i class="genericon genericon-<?php echo get_post_format(); ?>"></i>

			<?php if( get_the_title() ){ ?>
			<h3 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
			<?php } ?>

			<?php the_content(); ?>

		</div>

	</article><!--/.post-->