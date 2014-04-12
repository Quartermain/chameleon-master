<?php
/**
 * The template for displaying quote content. Used within any masonry/archive/index template.
 *
 * @package Gallery
 */
?>

	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

		<?php gallery_before_post(); ?>

	    <a class="frame" href="<?php the_permalink(); ?>">

			<i class="genericon genericon-<?php echo get_post_format(); ?>"></i>

	    	<?php the_content(); ?>

	    </a>

	</article><!--/.post-->