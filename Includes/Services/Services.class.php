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
	protected $Xo;

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
	public $IndexBuilder;

	/**
	 * @var XoServiceRouteGenerator
	 */
	public $RouteGenerator;

	/**
	 * @var XoServiceSitemapGenerator
	 */
	public $SitemapGenerator;

	public function __construct($Xo) {
		$this->Xo = $Xo;

		$this->Includes();
		$this->Init();
	}

	protected function Init() {
		$this->Options = new XoServiceOptions($this->Xo);
		$this->AngularJson = new XoServiceAngularJson();
		$this->TemplateReader = new XoServiceTemplateReader($this->Xo);
		$this->IndexBuilder = new XoServiceIndexBuilder($this->Xo);
		$this->RouteGenerator = new XoServiceRouteGenerator($this->Xo);
		$this->SitemapGenerator = new XoServiceSitemapGenerator($this->Xo);
	}

	protected function Includes() {
		$this->Xo->RequireOnce('Includes/Services/Classes/Options.class.php');
		$this->Xo->RequireOnce('Includes/Services/Classes/AngularJson.class.php');
		$this->Xo->RequireOnce('Includes/Services/Classes/TemplateReader.class.php');
		$this->Xo->RequireOnce('Includes/Services/Classes/IndexBuilder.class.php');
		$this->Xo->RequireOnce('Includes/Services/Classes/RouteGenerator.class.php');
		$this->Xo->RequireOnce('Includes/Services/Classes/SitemapGenerator.class.php');
		$this->Xo->RequireOnce('Includes/Services/Classes/AdminNotice.class.php');
	}
}