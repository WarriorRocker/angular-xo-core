<?php

/**
 * An abstract class which holds a tab placed within an admin page.
 *
 * @since 1.0.0
 */
class XoOptionsAbstractTab
{
	/**
	 * @var Xo
	 */
	var $Xo;

	/**
	 * Reference to the admin page hosting a given tab.
	 *
	 * @since 1.0.0
	 *
	 * @var XoOptionsAbstractAdminPage
	 */
	var $SettingsPage;

	/**
	 * Slug of the respective tab.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	var $tabPageSlug;

	/**
	 * Generate a basic tab.
	 *
	 * @since 1.0.0
	 *
	 * @param XoOptionsAbstractAdminPage $SettingsPage Reference to the containing admin page.
	 * @param string $slug Slug URL slug used when referencing the tab.
	 */
	function __construct(XoOptionsAbstractAdminPage $SettingsPage, $slug) {
		$this->Xo = $SettingsPage->Xo;

		$this->SettingsPage = $SettingsPage;
		$this->tabPageSlug = $this->SettingsPage->pageSlug . '-' . $slug;
		$this->Init();
	}

	/**
	 * Overridable function called when the tab is initialized.
	 *
	 * @since 1.0.0
	 */
	public function Init() { }

	/**
	 * Overridable function called when the tab is rendered.
	 * 
	 * @since 1.0.0
	 */
	public function Render() { }
}