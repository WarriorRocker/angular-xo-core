<?php

/**
 * A class which extends admin page to construct a top level admin page in WordPress.
 * 
 * @since 1.0.0
 */
class XoOptionsMenuPage extends XoOptionsAbstractAdminPage
{
	function __construct(Xo $Xo, $slug, $pageTitle, $titleTag, $menuTitle, $capability = 'manage_options', $icon = '', $position = NULL) {
		parent::__construct($Xo);

		$this->pageSlug = $slug;
		$this->pageTitle = $pageTitle;

		$this->baseUrl = 'admin.php?page=' . $this->pageSlug;

		add_action('admin_menu', function () use ($slug, $titleTag, $menuTitle, $capability, $icon, $position) {
			add_menu_page(
				$titleTag,
				$menuTitle,
				$capability,
				$slug,
				array($this, 'Render'),
				$icon,
				$position
			);
		}, 10, 0);
	}
}