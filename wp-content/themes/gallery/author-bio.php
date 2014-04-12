<?php
/**
 * The template for displaying Author bios
 *
 * @package Gallery
 */
?>

<div class="author-info">
	<div class="author-avatar">
		<?php
		/**
		 * Filter the author bio avatar size.
		 *
		 * @param int $size The avatar height and width size in pixels.
		 */
		$author_bio_avatar_size = apply_filters( 'gallery_author_bio_avatar_size', 120 );
		echo get_avatar( get_the_author_meta( 'user_email' ), $author_bio_avatar_size );
		?>
	</div><!-- .author-avatar -->
	<div class="author-description">
		<?php if( !is_archive() ) { ?>
		<h2 class="author-title"><?php printf( __( 'About %s', 'photolia' ), get_the_author() ); ?></h2>
		<p class="author-bio">
			<?php echo wp_kses_post( get_the_author_meta( 'description' ) ); ?>
		</p>
		<p>
			<a class="author-link" href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" rel="author">
				<?php printf( __( 'View all posts by %s <span class="meta-nav">&rarr;</span>', 'gallery' ), get_the_author() ); ?>
			</a>
		</p>
		<?php } else { ?>
		<h2 class="author-title"><?php printf( __( 'Posts by %s', 'photolia' ), get_the_author() ); ?></h2>
		<p class="author-bio">
			<?php echo wp_kses_post( get_the_author_meta( 'description' ) ); ?>
		</p>
		<?php } ?>

	</div><!-- .author-description -->
</div><!-- .author-info -->