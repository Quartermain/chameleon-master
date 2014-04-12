<?php
/**
 * The sidebar template holds default widgets and a dynamic sidebar.
 *
 * @package Gallery
 */
?>

<?php if( is_active_sidebar('sidebar-footer') ): ?>

	<aside id="sidebar-footer" class="cf widget-zone count-<?php echo gallery_widget_count('footer'); ?>">

		<ul class="widgets">

			<?php dynamic_sidebar('sidebar-footer'); ?>

		</ul>

	</aside><!--/#sidebar-->

<?php endif; ?>