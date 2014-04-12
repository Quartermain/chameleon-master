<?php

/**
 * Attaches featured media to gallery_before_post hook
 *
 * @uses gallery_get_featured_media()
 */
function gallery_attach_media_to_posts(){
	echo gallery_get_featured_media();
}

add_action('gallery_before_post','gallery_attach_media_to_posts');

/**
 * Grab the first shortcode from a post matching specified name.
 *
 * Because sometimes we want to display embedded media on masonry pages.
 *
 * @global $post WordPress post object
 * @param string $shortcode_name Accepts any valid shortcode name.
 *
 * @return $oembed_html HTML for first embedded object
 */
function gallery_get_shortcode($shortcode_name = 'video') {
	global $post;

	$pattern = get_shortcode_regex();

	preg_match('/'.$pattern.'/s', $post->post_content, $matches);

	if (is_array($matches) && isset($matches[2]) && $matches[2] == $shortcode_name) {
		$shortcode = $matches[0];
		return do_shortcode($shortcode);
	}
}

/**
 * Checks for an Instagram embed that isn't playing nicely
 *
 * Replaces the default oembed markup with a simple img element
 * so that our Instagram posts won't break.
 *
 * @global $post WordPress post object
 *
 * @return string $oembed HTML embed code
 */

function gallery_replace_instagram_html( $oembed ){
	global $post;

	$insta_regex = "#(?:http://)instagr(?:am.com|.am)/p/(.*?)/#i";

	if( $oembed == '' ){

		preg_match_all($insta_regex, $post->post_content, $matches);

		if( !empty( $matches[1][0] ) ){
			$oembed = '<a href="' . get_permalink( get_the_ID() ) . '"><img src="http://instagr.am/p/' . $matches[1][0] . '/media/"></a>';
		}
	}

	return $oembed;
}

add_filter('oembed_html','gallery_replace_instagram_html');

/**
 * Grab the first oembed object from post.
 *
 * Because sometimes we want to display embedded media on masonry pages.
 *
 * @return $oembed_html HTML for first embedded object
 */
function gallery_get_first_oembed() {

    $meta = get_post_custom( get_the_ID() );

	$html = '';

    foreach ($meta as $key => $value){
        if (false !== strpos($key, 'oembed')){

			if( ! is_singular() ) { // are we on a single post/page?

				// Grab the HTML we need to extract height and width from
				$oembed_html = $value[0];

				//echo "String: $string\n\n";
				$html = preg_replace_callback(gallery_height_width_regex(), "gallery_modify_height_width_atts", $oembed_html);

			}
        }
    }

    return apply_filters('oembed_html',$html);
}

/**
 * Returns a regular expression that captures the height and width of any element inside
 */

function gallery_height_width_regex(){
	return "/(width|height)=('|\")(\d+)('|\")([^>]+)(width|height)=('|\")(\d+)('|\")/";
}

/**
 * Parse the HTML and return a new height/width attributes for oembed objects.
 *
 * Because sometimes we want to display embedded media on masonry pages.
 *
 * @return $size_atts Size attributes for embedded media
 */
function gallery_modify_height_width_atts($matches) {

	$key1 = $matches[1];
	$val1 = $matches[3];
	$key2 = $matches[6];
	$val2 = $matches[8];

	$init_width = 0;
	$init_height = 0;

	$target_width = apply_filters('gallery_item_width',300);

	if(strtolower($key1) == 'width') {
		$init_width = $val1;
		$init_height = $val2;
	} else {
		$init_width = $val2;
		$init_height = $val1;
	}

	$size_atts = 'width="'. $target_width .'"' . $matches[5] . ' height="' . ($target_width * $init_height) / $init_width . '"';

	return $size_atts;

}

/**
 * Returns first image from post content
 *
 * Scans the post content for an <img> element and returns
 * the first match.
 *
 * @return $first_img First matched image URL
 */
function gallery_get_first_image() {
	global $post, $posts;

	$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);

	if( isset( $matches[1][0] ) ){
		$first_img = $matches[1][0];
		return $first_img;
	} else {
		return false;
	}

}

/**
 * Get the featured media for post
 *
 * Checks for featured image, then displays media based on
 */
function gallery_get_featured_media(){
	global $post;

	$post_format = get_post_format( $post->ID );

	if( ! is_singular() ):

		if( ! has_post_thumbnail( $post->ID ) && gallery_get_first_image() && ! is_singular() ): ?>

			<a href="<?php the_permalink(); ?>"><img src="<?php echo gallery_get_first_image(); ?>" alt="<?php the_title(); ?>"></a><?php

		elseif( has_post_thumbnail( $post->ID ) ): ?>

			<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail($post->ID,'grid'); ?></a><?php

		elseif( $oembed = gallery_get_first_oembed() ):

			echo $oembed;

		elseif( in_array( $post_format, array('video', 'audio', 'gallery') ) && $media_html = gallery_get_shortcode( $post_format ) ):

			$resized_html = preg_replace_callback(gallery_height_width_regex(), "gallery_modify_height_width_atts", $media_html);
			echo $resized_html;

		endif;

	endif;

}