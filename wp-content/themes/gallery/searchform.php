<?php
/**
 * The template for displaying search forms in gallery
 *
 * @package Gallery
 */
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
   
	<label class="screen-reader-text"><span style="">KEEP UPDTED WITH US</span></label>

	<input type="text" class="search-field" placeholder="<?php echo esc_attr_x( 'Search', 'placeholder', 'gallery' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s">

	<button type="submit"><img class="sb-icon-search" src="<?php echo get_template_directory_uri(); ?>/assets/img/search.png" alt=""></button>

</form>