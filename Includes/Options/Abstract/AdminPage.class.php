<?php

/**
 * An abstract class used to setup, display, and manage tabs on a WordPress admin page.
 *
 * @since 1.0.0
 */
class XoOptionsAbstractAdminPage
{
	/**
	 * @var Xo
	 */
	protected $Xo;

	/**
	 * The H1 title shown at the top of the page.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $pageTitle;

	/**
	 * The slug of the admin page.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $pageSlug;

	/**
	 * A derived url of the current page. Useful for linking within a page such as tabs.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $baseUrl;

	/**
	 * Slug of the currently active tab.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $currentSlug;

	/**
	 * Configuration of the currently active tab.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $currentTab;

	/**
	 * Collection of tabs registered for the current admin page.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $tabs = array();

	public function __construct(Xo $Xo) {
		$this->Xo = $Xo;

		add_action('admin_init', array($this, 'InitTabs'), 10, 0);
	}

	/**
	 * Render the current view and tab.
	 *
	 * @since 1.0.0
	 */
	public function Render() {
		$this->SetCurrentTab();

		echo '<div class="wrap xo-wrap">';

		$this->RenderHeading();
		$this->RenderTabs();
		$this->RenderCurrentTab();

		echo '</div>';
	}

	/**
	 * Add a tab to the current admin page.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug URL slug used when referencing the tab.
	 * @param string $title Title shown in the tabs navigation bar.
	 * @param string $class Class to instantiate for the tab, expected to be included prior to admin_init.
	 */
	function AddTab($slug, $title, $class) {
		$this->tabs[$slug] = array(
			'slug' => $slug,
			'title' => $title,
			'class' => $class
		);
	}

	/**
	 * Iterate through the tabs and create a new instance, useful for allowing additional hooks within the tab.
	 *
	 * @since 1.0.0
	 */
	public function InitTabs() {
		$this->SetCurrentTab();

		foreach ($this->tabs as &$tab)
			if (!empty($tab['class']))
				$tab['classInstance'] = new $tab['class']($this->Xo, $this, $tab['slug']);
	}

	/**
	 * Set the currently active tab based either on the URL or default if not found or not set.
	 *
	 * @since 1.0.0
	 */
	protected function SetCurrentTab() {
		if ((!empty($_GET['tab']))
			&& (isset($this->tabs[$_GET['tab']]))) {
			$this->currentSlug = $_GET['tab'];
		} else {
			foreach ($this->tabs as $tabSlug => $tab) {
				$this->currentSlug = $tabSlug;
				break;
			}
		}

		if (isset($this->tabs[$this->currentSlug]))
			$this->currentTab = $this->tabs[$this->currentSlug];
	}

	/**
	 * Small function to render the title of the given admin page.
	 *
	 * @since 1.0.0
	 */
	protected function RenderHeading() {
		if (!$this->pageTitle)
			return;

		echo '<h1 class="xo-heading"><span class="xo-logo"></span>'
			. '<span>' . $this->pageTitle . '</span></h1>';
	}

	/**
	 * Render the tab navigation bar or nothing if there is only one or no tabs.
	 *
	 * @since 1.0.0
	 */
	protected function RenderTabs() {
		if (count($this->tabs) < 2)
			return;

		$output = '<div class="nav-tab-wrapper">';

		foreach ($this->tabs as $slug => $tab)
			$output .= '<a class="nav-tab '
			. (($slug == $this->currentSlug) ? 'nav-tab-active' : '')
			. '" href="' . $this->GetTabUrl($slug) . '">' . $tab['title'] . '</a>';

		$output .= '</div>';

		echo $output;
	}

	/**
	 * Small function to render the currently selected tab.
	 *
	 * @since 1.0.0
	 */
	protected function RenderCurrentTab() {
		if ($this->currentTab)
			$this->currentTab['classInstance']->Render();
	}

	/**
	 * Helper function to get the URL to the current or a given tab.
	 *
	 * @since 1.0.0
	 *
	 * @param string $tabSlug Slug of the tab to get the URL, blank for current tab.
	 * @return string URL of the given tab.
	 */
	public function GetTabUrl($tabSlug = '') {
		return admin_url($this->baseUrl . '&tab=' . (($tabSlug) ? $tabSlug : $this->currentSlug));
	}

	/**
	 * Helper function to get the slug used to uniquely identify a tab within a page.
	 *
	 * @since 1.2.0
	 *
	 * @param string $tabSlug Slug of the tab, blank for current tab.
	 * @return string Slug of the current page and tab.
	 */
	public function GetTabPageSlug($tabSlug = '') {
		return $this->pageSlug . '-' . (($tabSlug) ? $tabSlug : $this->currentSlug);
	}
}