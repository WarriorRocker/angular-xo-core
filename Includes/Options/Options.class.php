<?php

/**
 * Base Xo Options resource loader.
 *
 * @since 1.0.0
 */
class XoOptions
{
	/**
	 * @var Xo
	 */
	protected $Xo;

	/**
	 * @var XoOptionsMenuPage
	 */
	public $MainSettingsPage;

	/**
	 * @var XoOptionsSubMenuPage
	 */
	public $GeneralSettingsPage;

	/**
	 * @var XoOptionsSubMenuPage
	 */
	public $ToolsPage;

	public function __construct($Xo) {
		$this->Xo = $Xo;

		$this->Includes();
		$this->Init();

		add_action('admin_enqueue_scripts', array($this, 'AddAdminStyles'), 10, 0);
	}

	protected function Init() {
		$this->InitGeneralSettingsPage();

		$this->InitMainSettingsPage();
		$this->InitMainSettingsPageTabs();

		$this->InitToolsPage();
		$this->InitToolsPageTabs();
	}

	public function AddAdminStyles() {
		$adminCssFile = $this->Xo->GetFile('assets/css/admin.css', true);

		if ($adminCssFile)
			wp_enqueue_style('admin-styles', $adminCssFile);
	}

	protected function InitMainSettingsPage() {
		$this->MainSettingsPage = new XoOptionsMenuPage(
			$this->Xo,
			'angular-xo',
			__('General Options', 'xo'),
			$this->Xo->name,
			$this->Xo->name,
			'manage_options',
			$this->GetImageAsSvgBase64('assets/svg/angular-xo-white.svg'),
			98.6
		);
	}

	protected function InitMainSettingsPageTabs() {
		$this->MainSettingsPage->AddTab('index', __('Index', 'xo'), 'XoOptionsTabIndex');
		$this->MainSettingsPage->AddTab('api', __('API', 'xo'), 'XoOptionsTabApi');
		$this->MainSettingsPage->AddTab('posts', __('Posts', 'xo'), 'XoOptionsTabPosts');
		$this->MainSettingsPage->AddTab('routing', __('Routing', 'xo'), 'XoOptionsTabRouting');
		$this->MainSettingsPage->AddTab('templates', __('Templates', 'xo'), 'XoOptionsTabTemplates');

		if (class_exists('ACF'))
			$this->MainSettingsPage->AddTab('acf', __('ACF', 'xo'), 'XoOptionsTabAcf');
	}

	protected function InitGeneralSettingsPage() {
		$this->GeneralSettingsPage = new XoOptionsSubMenuPage(
			$this->Xo,
			'angular-xo',
			'angular-xo',
			__('General Options', 'xo'),
			__('General Options', 'xo'),
			__('General Options', 'xo'),
			'manage_options'
		);
	}

	protected function InitToolsPage() {
		$this->ToolsPage = new XoOptionsSubMenuPage(
			$this->Xo,
			'angular-xo-tools',
			'angular-xo',
			__('Tools', 'xo'),
			__('Tools', 'xo'),
			__('Tools', 'xo'),
			'manage_options'
		);
	}

	protected function InitToolsPageTabs() {
		$this->ToolsPage->AddTab('tools', __('Tools', 'xo'), 'XoOptionsTabTools');
		$this->ToolsPage->AddTab('profile', __('Profile', 'xo'), 'XoOptionsTabProfile');
		$this->ToolsPage->AddTab('export', __('Export', 'xo'), 'XoOptionsTabExport');
	}

	protected function Includes() {
		// Include abstract classes for admin pages and tabs
		$this->IncludeAbstractClasses();

		// Include tabs used on the General Settings page
		$this->IncludeGeneralTabs();

		// Include tabs used on the Tools page
		$this->IncludeToolsTabs();
	}

	protected function IncludeAbstractClasses() {
		$this->Xo->RequireOnce('Includes/Options/Abstract/Tab.class.php');
		$this->Xo->RequireOnce('Includes/Options/Abstract/FieldsTab.class.php');
		$this->Xo->RequireOnce('Includes/Options/Abstract/SettingsTab.class.php');
		$this->Xo->RequireOnce('Includes/Options/Abstract/AdminPage.class.php');

		$this->Xo->RequireOnce('Includes/Options/Classes/MenuPage.class.php');
		$this->Xo->RequireOnce('Includes/Options/Classes/SubMenuPage.class.php');
	}

	protected function IncludeGeneralTabs() {
		$this->Xo->RequireOnce('Includes/Options/Tabs/General/IndexTab.class.php');
		$this->Xo->RequireOnce('Includes/Options/Tabs/General/ApiTab.class.php');
		$this->Xo->RequireOnce('Includes/Options/Tabs/General/PostsTab.class.php');
		$this->Xo->RequireOnce('Includes/Options/Tabs/General/RoutingTab.class.php');
		$this->Xo->RequireOnce('Includes/Options/Tabs/General/TemplatesTab.class.php');
		$this->Xo->RequireOnce('Includes/Options/Tabs/General/AcfTab.class.php');
	}

	protected function IncludeToolsTabs() {
		$this->Xo->RequireOnce('Includes/Options/Tabs/Tools/ToolsTab.class.php');
		$this->Xo->RequireOnce('Includes/Options/Tabs/Tools/ProfileTab.class.php');
		$this->Xo->RequireOnce('Includes/Options/Tabs/Tools/ExportTab.class.php');
	}

	/**
	 * Helper function used to embed an svg image file within the WordPress admin
	 *
	 * @since 1.0.0
	 *
	 * @param string $file Relative path to file within the Xo plugin folder.
	 * @return string Base64 encoded image data.
	 */
	public function GetImageAsSvgBase64($file) {
		$svgFile = $this->Xo->GetFile($file);
		if (!$svgFile)
			return false;

		ob_start();
		echo file_get_contents($svgFile);
		$data = ob_get_clean();

		return 'data:image/svg+xml;base64,' . base64_encode($data);
	}
}