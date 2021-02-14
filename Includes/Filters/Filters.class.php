<?php

/**
 * Base Xo Filters resource loader.
 *
 * @since 1.0.0
 */
class XoFilters
{
	/**
	 * @var Xo
	 */
	protected $Xo;

	/**
	 * @var XoFilterModRewrite
	 */
	public $ModRewrite;

	/**
	 * @var XoFilterPluginSettings
	 */
	public $PluginSettings;

	/**
	 * @var XoFilterPostStates
	 */
	public $PostStates;

	/**
	 * @var XoFilterPostTemplates
	 */
	public $PostTemplates;

	/**
	 * @var XoFilterPostPreview
	 */
	public $PostPreview;

	/**
	 * @var XoFilterEditorOptions
	 */
	public $EditorOptions;

	/**
	 * @var XoFiltersNavMenus
	 */
	public $NavMenus;

	/**
	 * @var XoFilterLiveIndex
	 */
	public $LiveIndex;

	/**
	 * @var XoFilterEntrypoints
	 */
	public $Entrypoints;

	function __construct($Xo) {
		$this->Xo = $Xo;

		$this->Includes();
		$this->Init();
	}

	protected function Init() {
		$this->ModRewrite = new XoFilterModRewrite($this->Xo);

		$this->PluginSettings = new XoFilterPluginSettings($this->Xo);

		$this->PostStates = new XoFilterPostStates($this->Xo);
		$this->PostTemplates = new XoFilterPostTemplates($this->Xo);
		$this->PostPreview = new XoFilterPostPreview($this->Xo);

		$this->EditorOptions = new XoFilterEditorOptions($this->Xo);

		$this->NavMenus = new XoFiltersNavMenus($this->Xo);

		$this->LiveIndex = new XoFilterLiveIndex($this->Xo);

		$this->Entrypoints = new XoFilterEntrypoints($this->Xo);
	}

	protected function Includes() {
		$this->Xo->RequireOnce('Includes/Filters/External/ModRewrite.class.php');

		$this->Xo->RequireOnce('Includes/Filters/External/PluginSettings.class.php');

		$this->Xo->RequireOnce('Includes/Filters/External/PostStates.class.php');
		$this->Xo->RequireOnce('Includes/Filters/External/PostTemplates.class.php');
		$this->Xo->RequireOnce('Includes/Filters/External/PostPreview.class.php');
		$this->Xo->RequireOnce('Includes/Filters/External/PostUpdate.class.php');

		$this->Xo->RequireOnce('Includes/Filters/External/EditorOptions.class.php');

		$this->Xo->RequireOnce('Includes/Filters/External/NavMenus.class.php');

		$this->Xo->RequireOnce('Includes/Filters/Internal/LiveIndex.class.php');

		$this->Xo->RequireOnce('Includes/Filters/Internal/Entrypoints.class.php');
	}
}