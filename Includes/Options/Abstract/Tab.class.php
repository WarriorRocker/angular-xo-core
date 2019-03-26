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
	protected $Xo;

	/**
	 * Reference to the admin page hosting a given tab.
	 *
	 * @since 1.0.0
	 *
	 * @var XoOptionsAbstractAdminPage
	 */
	protected $SettingsPage;

	/**
	 * Url of the respective tab.
	 *
	 * @since 1.2.0
	 *
	 * @var string
	 */
	protected $tabPageUrl;

	/**
	 * Slug of the respective tab.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $tabPageSlug;

	/**
	 * Generate a basic tab.
	 *
	 * @since 1.0.0
	 *
	 * @param Xo $Xo Reference to Xo main class.
	 * @param XoOptionsAbstractAdminPage $SettingsPage Reference to the containing admin page.
	 * @param string $slug Slug of the respective tab.
	 */
	public function __construct(Xo $Xo, XoOptionsAbstractAdminPage $SettingsPage, $slug) {
		$this->Xo = $Xo;
		$this->SettingsPage = $SettingsPage;

		$this->tabPageUrl = $SettingsPage->GetTabUrl($slug);
		$this->tabPageSlug = $SettingsPage->GetTabPageSlug($slug);

		$this->Init();
	}

	/**
	 * Overridable function called when the tab is initialized.
	 *
	 * @since 1.0.0
	 */
	protected function Init() { }

	/**
	 * Overridable function called when the tab is rendered.
	 *
	 * @since 1.0.0
	 */
	public function Render() { }
}