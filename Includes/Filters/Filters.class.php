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
	var $Xo;

	/**
	 * @var XoFilterModRewrite
	 */
	var $ModRewrite;

	/**
	 * @var XoFilterPluginSettings
	 */
	var $PluginSettings;

	/**
	 * @var XoFilterPostStates
	 */
	var $PostStates;

	/**
	 * @var XoFilterPostTemplates
	 */
	var $PostTemplates;

	/**
	 * @var XoFilterPostPreview
	 */
	var $PostPreview;

	/**
	 * @var XoFilterEditorOptions
	 */
	var $EditorOptions;

	/**
	 * @var XoFiltersNavMenus
	 */
	var $NavMenus;

	/**
	 * @var XoFilterRebuildIndex
	 */
	var $RebuildIndex;

	function __construct($Xo) {
		$this->Xo = $Xo;

		$this->Includes();
		$this->Init();
	}

	function Init() {
		$this->ModRewrite = new XoFilterModRewrite($this->Xo);

		$this->PluginSettings = new XoFilterPluginSettings($this->Xo);

		$this->PostStates = new XoFilterPostStates($this->Xo);
		$this->PostTemplates = new XoFilterPostTemplates($this->Xo);
		$this->PostPreview = new XoFilterPostPreview($this->Xo);

		$this->EditorOptions = new XoFilterEditorOptions($this->Xo);

		$this->NavMenus = new XoFiltersNavMenus($this->Xo);

		$this->RebuildIndex = new XoFilterRebuildIndex($this->Xo);
	}

	function Includes() {
		$this->Xo->RequireOnce('Includes/Filters/External/ModRewrite.class.php');

		$this->Xo->RequireOnce('Includes/Filters/External/PluginSettings.class.php');

		$this->Xo->RequireOnce('Includes/Filters/External/PostStates.class.php');
		$this->Xo->RequireOnce('Includes/Filters/External/PostTemplates.class.php');
		$this->Xo->RequireOnce('Includes/Filters/External/PostPreview.class.php');
		$this->Xo->RequireOnce('Includes/Filters/External/PostUpdate.class.php');

		$this->Xo->RequireOnce('Includes/Filters/External/EditorOptions.class.php');

		$this->Xo->RequireOnce('Includes/Filters/External/NavMenus.class.php');

		$this->Xo->RequireOnce('Includes/Filters/External/RebuildIndex.class.php');
	}
}