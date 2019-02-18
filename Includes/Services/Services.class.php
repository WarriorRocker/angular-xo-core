<?php

/**
 * Base Xo Services resource loader.
 *
 * @since 1.0.0
 */
class XoServices
{
	/**
	 * @var Xo
	 */
	var $Xo;

	/**
	 * @var XoServiceOptions
	 */
	public $Options;

	/**
	 * @var XoServiceAngularJson
	 */
	public $AngularJson;

	/**
	 * @var XoServiceTemplateReader
	 */
	public $TemplateReader;

	/**
	 * @var XoServiceIndexBuilder
	 */
	var $IndexBuilder;

	/**
	 * @var XoServiceRouteGenerator
	 */
	var $RouteGenerator;

	function __construct($Xo) {
		$this->Xo = $Xo;

		$this->Includes();
		$this->Init();
	}

	function Init() {
		$this->Options = new XoServiceOptions($this->Xo);
		$this->AngularJson = new XoServiceAngularJson();
		$this->TemplateReader = new XoServiceTemplateReader($this->Xo);
		$this->IndexBuilder = new XoServiceIndexBuilder($this->Xo);
		$this->RouteGenerator = new XoServiceRouteGenerator($this->Xo);
	}

	function Includes() {
		$this->Xo->RequireOnce('Includes/Services/Classes/Options.class.php');
		$this->Xo->RequireOnce('Includes/Services/Classes/AngularJson.class.php');
		$this->Xo->RequireOnce('Includes/Services/Classes/TemplateReader.class.php');
		$this->Xo->RequireOnce('Includes/Services/Classes/IndexBuilder.class.php');
		$this->Xo->RequireOnce('Includes/Services/Classes/RouteGenerator.class.php');
		$this->Xo->RequireOnce('Includes/Services/Classes/AdminNotice.class.php');
	}
}