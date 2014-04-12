<?php
/**
 * The template for displaying link content. Used within any masonry/archive/index template.
 *
 * @package Gallery
 */
?>

	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

		<?php gallery_before_post(); ?>

		<a class="post-image" href="<?php the_permalink(); ?>"><?php the_post_thumbnail($post->ID,'grid'); ?></a>

		<div class="panel">

			<i class="genericon genericon-<?php echo get_post_format(); ?>"></i>

			<?php if( get_the_title() ){ ?>
			<h3 class="entry-title"><a href="<?php echo gallery_get_link_url(); ?>"><?php the_title(); ?></a></h3>
			<?php } ?>

			<?php the_excerpt(); ?>

		</div>

	</article><!--/.post-->