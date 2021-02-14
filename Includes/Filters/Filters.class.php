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
	 * @var XoFilterPostUpdate
	 */
	public $PostUpdate;

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

	function __construct($Xo) {
		$this->Xo = $Xo;

		$this->Includes();
		$this->Init();
	}

	protected function Init() {
		$this->PluginSettings = new XoFilterPluginSettings($this->Xo);

		$this->PostStates = new XoFilterPostStates($this->Xo);
		$this->PostTemplates = new XoFilterPostTemplates($this->Xo);
		$this->PostPreview = new XoFilterPostPreview($this->Xo);
		$this->PostUpdate = new XoFilterPostUpdate($this->Xo);

		$this->EditorOptions = new XoFilterEditorOptions($this->Xo);

		$this->NavMenus = new XoFiltersNavMenus($this->Xo);

		$this->LiveIndex = new XoFilterLiveIndex($this->Xo);
	}

	protected function Includes() {
		$this->Xo->RequireOnce('Includes/Filters/External/PluginSettings.class.php');

		$this->Xo->RequireOnce('Includes/Filters/External/PostStates.class.php');
		$this->Xo->RequireOnce('Includes/Filters/External/PostTemplates.class.php');
		$this->Xo->RequireOnce('Includes/Filters/External/PostPreview.class.php');
		$this->Xo->RequireOnce('Includes/Filters/External/PostUpdate.class.php');

		$this->Xo->RequireOnce('Includes/Filters/External/EditorOptions.class.php');

		$this->Xo->RequireOnce('Includes/Filters/External/NavMenus.class.php');

		$this->Xo->RequireOnce('Includes/Filters/Internal/LiveIndex.class.php');
	}
}