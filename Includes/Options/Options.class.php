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
	var $Xo;

	/**
	 * @var XoOptionsMenuPage
	 */
	var $MainSettingsPage;

	/**
	 * @var XoOptionsSubMenuPage
	 */
	var $GeneralSettingsPage;

	/**
	 * @var XoOptionsSubMenuPage
	 */
	var $ToolsPage;

	function __construct($Xo) {
		$this->Xo = $Xo;

		$this->Includes();
		$this->Init();
	}

	function Init() {
		$this->InitGeneralSettingsPage();

		$this->InitMainSettingsPage();
		$this->InitMainSettingsPageTabs();

		$this->InitToolsPage();
		$this->InitToolsPageTabs();
	}

	function InitMainSettingsPage() {
		$this->MainSettingsPage = new XoOptionsMenuPage(
			$this->Xo,
			'angular-xo',
			$this->Xo->name,
			$this->Xo->name,
			$this->Xo->name,
			'manage_options',
			$this->GetImageAsSvgBase64('assets/svg/angular-xo-white.svg'),
			98.6
		);
	}

	function InitMainSettingsPageTabs() {
		$this->MainSettingsPage->AddTab('index', __('Index', 'xo'), 'XoOptionsTabIndex');
		$this->MainSettingsPage->AddTab('posts', __('Posts', 'xo'), 'XoOptionsTabPosts');
		$this->MainSettingsPage->AddTab('routing', __('Routing', 'xo'), 'XoOptionsTabRouting');
		$this->MainSettingsPage->AddTab('templates', __('Templates', 'xo'), 'XoOptionsTabTemplates');

		if (class_exists('ACF'))
			$this->MainSettingsPage->AddTab('acf', __('ACF', 'xo'), 'XoOptionsTabAcf');
	}

	function InitGeneralSettingsPage() {
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

	function InitToolsPage() {
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

	function InitToolsPageTabs() {
		$this->ToolsPage->AddTab('tools', __('Tools', 'xo'), 'XoOptionsTabTools');
		$this->ToolsPage->AddTab('profile', __('Profile', 'xo'), 'XoOptionsTabProfile');
	}

	function Includes() {
		$this->Xo->RequireOnce('Includes/Options/Abstract/Tab.class.php');
		$this->Xo->RequireOnce('Includes/Options/Abstract/FieldsTab.class.php');
		$this->Xo->RequireOnce('Includes/Options/Abstract/SettingsTab.class.php');
		$this->Xo->RequireOnce('Includes/Options/Abstract/AdminPage.class.php');

		$this->Xo->RequireOnce('Includes/Options/Tabs/General/IndexTab.class.php');
		$this->Xo->RequireOnce('Includes/Options/Tabs/General/PostsTab.class.php');
		$this->Xo->RequireOnce('Includes/Options/Tabs/General/RoutingTab.class.php');
		$this->Xo->RequireOnce('Includes/Options/Tabs/General/TemplatesTab.class.php');
		$this->Xo->RequireOnce('Includes/Options/Tabs/General/AcfTab.class.php');

		$this->Xo->RequireOnce('Includes/Options/Tabs/Tools/ToolsTab.class.php');
		$this->Xo->RequireOnce('Includes/Options/Tabs/Tools/ProfileTab.class.php');

		$this->Xo->RequireOnce('Includes/Options/Classes/MenuPage.class.php');
		$this->Xo->RequireOnce('Includes/Options/Classes/SubMenuPage.class.php');
	}

	public function GetImageAsSvgBase64($file) {
		ob_start();
		$this->Xo->RequireOnce($file, false);
		$data = ob_get_clean();

		return 'data:image/svg+xml;base64,' . base64_encode($data);
	}
}