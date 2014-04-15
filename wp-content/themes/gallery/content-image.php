<?php
/**
 * The template for displaying image content. Used within any masonry/archive/index template.
 *
 * @package Gallery
 */
?>

	<article id="post-<?php the_ID(); ?>" <?php post_class('vertical flip-container'); ?>>

		<div class="flipper">

			<div class="front">
				<?php gallery_before_post(); ?>
				<div class="txt-category"><?php echo the_category(' '); ?></div>
				<h2 class="entry-title"><?php the_title(); ?></h2>
			</div>

			<a class="back" href="<?php the_permalink(); ?>">
				<div class="logo-hover">
					<img  src="<?php echo get_bloginfo('template_directory');?>/assets/img/logo-hover.png"/>
				</div>
				<div class="vertical-center">
					<h2 class="entry-title"><?php the_title(); ?></h2>
					<?php if ( get_the_excerpt() ){ ?><div class="entry-summary"><?php the_excerpt(); ?></div><?php } ?>
				</div>
			</a>

		</div>

	</article><!--/.post-->