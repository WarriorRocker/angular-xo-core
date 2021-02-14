<?php

/**
 * A class which extends admin page to construct a child admin page in WordPress.
 *
 * @since 1.0.0
 */
class XoOptionsSubMenuPage extends XoOptionsAbstractAdminPage
{
	function __construct($Xo, $slug, $parentSlug, $pageTitle, $titleTag, $menuTitle, $capability) {
		parent::__construct($Xo);

		$this->pageSlug = $slug;
		$this->pageTitle = $pageTitle;

		$this->baseUrl = 'admin.php?page=' . $this->pageSlug;

		add_action('admin_menu', function () use ($slug, $parentSlug, $titleTag, $menuTitle, $capability) {
			add_submenu_page(
				$parentSlug,
				$titleTag,
				$menuTitle,
				$capability,
				$slug,
				array($this, 'Render')
			);
		}, 10, 0);
	}
}