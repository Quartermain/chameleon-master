<?php
/**
 * The sidebar for single posts below the author box, above the comments.
 *
 * @package Gallery
 */
?>

<?php if( is_active_sidebar('sidebar-single') ): ?>

	<aside id="sidebar-single" class="cf widget-zone count-<?php echo gallery_widget_count('single'); ?>">

		<ul class="widgets">

			<?php dynamic_sidebar('sidebar-single'); ?>

		</ul>

	</aside><!--/#sidebar-->

<?php endif; ?>