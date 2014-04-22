<?php
/**
 * The sidebar template holds default widgets and a dynamic sidebar.
 *
 * @package Gallery
 */
?>

<?php if( is_active_sidebar('sidebar-footer') ): ?>

	<aside id="sidebar-footer" class="cf widget-zone count-<?php echo gallery_widget_count('footer'); ?>">
		<div class="content-footer">
			<div class="logo-footer">
                            <a href="<?php echo get_site_url(); ?>" title="<?php bloginfo('name') ?>">
                                <img  src="<?php echo get_bloginfo('template_directory');?>/assets/img/logo-footer.png"/>
                            </a>
			</div>

			<ul class="widgets">
				<?php dynamic_sidebar('sidebar-footer'); ?>

			</ul>
			<div class="contact-footer">
				<h2 class="widgettitle">Pages</h2>
				<ul>
					<li class="page_item page-item-2"><a>6 Cecil Plac e, Prahran 3181</a></li>
					<li class="page_item page-item-32"><a>P +61 3 9510 1188</a></li>
					<li class="page_item page-item-34"><a>F +61 3 9510 9922</a></li>
					<li class="page_item page-item-30"><a href="casting@chameleon.net.au">E casting@chameleon.net.au</a></li>
				</ul>
			</div>
		</div>

	</aside><!--/#sidebar-->

<?php endif; ?>