<?php

/**
 * Base Xo API resource loader.
 *
 * @since 1.0.0
 */
class XoApi
{
	/**
	 * @var Xo
	 */
	protected $Xo;

	/**
	 * @var XoApiClassRouter
	 */
	public $Router;

	public function __construct($Xo) {
		$this->Xo = $Xo;

		$this->Includes();
		$this->Init();
	}

	protected function Init() {
		$this->Router = new XoApiClassRouter($this->Xo);

		$this->Router->AddController('config', 'XoApiControllerConfig');
		$this->Router->AddController('routes', 'XoApiControllerRoutes');
		$this->Router->AddController('posts', 'XoApiControllerPosts');
		$this->Router->AddController('terms', 'XoApiControllerTerms');
		$this->Router->AddController('menus', 'XoApiControllerMenus');
		$this->Router->AddController('options', 'XoApiControllerOptions');
		$this->Router->AddController('comments', 'XoApiControllerComments');
	}

	protected function Includes() {
		// Include abstract interface classes used for fully formed objects
		$this->IncludeAbstractObjects();

		// Include abstract interface classes use for API responses
		$this->IncludeAbstractResponses();

		// Include the base services required by the Xo API
		$this->IncludeApiBaseServices();

		// Include the standard API controllers
		$this->IncludeApiControllers();
	}

	protected function IncludeAbstractObjects() {
		$this->Xo->RequireOnce('Includes/Api/Abstract/Objects/Post.class.php');
		$this->Xo->RequireOnce('Includes/Api/Abstract/Objects/Menu.class.php');
		$this->Xo->RequireOnce('Includes/Api/Abstract/Objects/Term.class.php');
		$this->Xo->RequireOnce('Includes/Api/Abstract/Objects/Route.class.php');
		$this->Xo->RequireOnce('Includes/Api/Abstract/Objects/SitemapEntry.class.php');
	}

	protected function IncludeAbstractResponses() {
		$this->Xo->RequireOnce('Includes/Api/Abstract/Response.class.php');

		$this->Xo->RequireOnce('Includes/Api/Abstract/Responses/IndexResponse.class.php');
		$this->Xo->RequireOnce('Includes/Api/Abstract/Responses/ConfigResponse.class.php');
		$this->Xo->RequireOnce('Includes/Api/Abstract/Responses/RoutesGetResponse.class.php');
		$this->Xo->RequireOnce('Includes/Api/Abstract/Responses/RoutesSitemapResponse.class.php');
		$this->Xo->RequireOnce('Includes/Api/Abstract/Responses/PostsGetResponse.class.php');
		$this->Xo->RequireOnce('Includes/Api/Abstract/Responses/PostsFilterResponse.class.php');
		$this->Xo->RequireOnce('Includes/Api/Abstract/Responses/PostsConfigResponse.class.php');
		$this->Xo->RequireOnce('Includes/Api/Abstract/Responses/MenusResponse.class.php');
		$this->Xo->RequireOnce('Includes/Api/Abstract/Responses/TermsGetResponse.class.php');
		$this->Xo->RequireOnce('Includes/Api/Abstract/Responses/TermsFilterResponse.class.php');
		$this->Xo->RequireOnce('Includes/Api/Abstract/Responses/OptionsResponse.class.php');
	}

	protected function IncludeApiBaseServices() {
		$this->Xo->RequireOnce('Includes/Api/Classes/Router.class.php');
		$this->Xo->RequireOnce('Includes/Api/Classes/Reflector.class.php');
	}

	protected function IncludeApiControllers() {
		$this->Xo->RequireOnce('Includes/Api/Abstract/Controller.class.php');
		$this->Xo->RequireOnce('Includes/Api/Abstract/IndexController.class.php');

		$this->Xo->RequireOnce('Includes/Api/Controllers/ConfigController.class.php');
		$this->Xo->RequireOnce('Includes/Api/Controllers/RoutesController.class.php');
		$this->Xo->RequireOnce('Includes/Api/Controllers/PostsController.class.php');
		$this->Xo->RequireOnce('Includes/Api/Controllers/TermsController.class.php');
		$this->Xo->RequireOnce('Includes/Api/Controllers/OptionsController.class.php');
		$this->Xo->RequireOnce('Includes/Api/Controllers/MenusController.class.php');
		$this->Xo->RequireOnce('Includes/Api/Controllers/CommentsController.class.php');
	}
}