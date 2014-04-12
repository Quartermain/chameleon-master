<?php
/**
 * The default template for displaying content. Used for masonry layouts and any index/archive/search template.
 *
 * @package Gallery
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

<?php if( is_singular() ): ?>

	<?php if( ( get_post_format() != 'image' && has_post_thumbnail() ) || ( get_post_format() === 'image' & ! gallery_get_first_image() ) ): ?>
	<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail($post->ID,'full-width'); ?></a>
	<?php endif; ?>

	<header class="entry-header">
		<div class="category-wrapper">
			<span class="category"><?php echo the_category(' '); ?></span>
		</div>

		<h1 class="entry-title"><?php the_title(); ?></h1>

		<div class="post-meta before-content">
			<?php if( is_multi_author() ){ ?>
				<?php $author_link = '<a href="' . get_author_posts_url( get_the_author_meta('ID') ) . '">' . get_the_author() . '</a>'; ?>
				<span class="author"><?php echo sprintf( __('Posted by %s on %s','gallery' ), $author_link, get_the_date() ); ?></span>
			<?php } else { ?>
				<span class="author"><?php the_date(); ?></span>
			<?php } ?>
		</div><!--/.post-meta-->

	</header><!--/.entry-header-->

	<div class="post-content">
		<?php the_content(); ?>
	</div><!--/.post-content-->

	<?php wp_link_pages( array( 'before' => '<div class="page-links">' . __( 'Pages:', 'gallery' ), 'after' => '</div>' ) ); ?>

	<div class="post-meta after-content">
	  <div class="tags"><?php the_tags( ' <span class="hashtag">#','</span><span class="hashtag">#','</span>'); ?></div>
	</div><!--/.post-meta-->

	<?php get_template_part('author-bio'); ?>

	<?php get_sidebar('single'); ?>

	<?php comments_template(); ?>

<?php else: ?>

	<a class="post-image" href="<?php the_permalink(); ?>"><?php the_post_thumbnail($post->ID,'grid'); ?></a>

	<div class="panel">

		<i class="genericon genericon-standard"></i>

		<?php if( get_the_title() ){ ?>
		<h3 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<?php } ?>

		<?php the_excerpt(); ?>

	</div>

<?php endif; ?>

</article><!--/.post-->