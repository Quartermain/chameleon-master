<?php
/**
 * The template for displaying the footer.
 *
 * Contains footer content and the closing of the
 * #wrapper div element.
 *
 * @package Gallery
 */
?>

	</div><!--/#wrapper .hfeed-->
	<div id="footer">
		<div id="siteinfo">
			<span><?php echo gallery_get_footertext(); ?></span>
		</div><!--/#siteinfo-->
	</div><!--/#footer-->
<?php wp_footer(); ?>
</body>
</html>