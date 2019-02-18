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
	var $Xo;

	/**
	 * @var XoApiClassRouter
	 */
	var $Router;

	function __construct($Xo) {
		$this->Xo = $Xo;

		$this->Includes();
		$this->Init();
	}

	function Init() {
		$this->Router = new XoApiClassRouter($this->Xo);

		$this->Router->AddController('config', 'XoApiControllerConfig');
		$this->Router->AddController('routes', 'XoApiControllerRoutes');
		$this->Router->AddController('posts', 'XoApiControllerPosts');
		$this->Router->AddController('terms', 'XoApiControllerTerms');
		$this->Router->AddController('menus', 'XoApiControllerMenus');
		$this->Router->AddController('options', 'XoApiControllerOptions');
		$this->Router->AddController('comments', 'XoApiControllerComments');
	}

	function Includes() {
		// Include abstract interface classes used for fully formed objects
		$this->IncludeAbstractObjects();

		// Include abstract interface classes use for API responses
		$this->IncludeAbstractResponses();

		// Include the base services required by the Xo API
		$this->IncludeApiBaseServices();

		// Include the standard API controllers
		$this->IncludeApiControllers();
	}

	function IncludeAbstractObjects() {
		$this->Xo->RequireOnce('Includes/Api/Abstract/Objects/Post.class.php');
		$this->Xo->RequireOnce('Includes/Api/Abstract/Objects/Menu.class.php');
		$this->Xo->RequireOnce('Includes/Api/Abstract/Objects/Term.class.php');
		$this->Xo->RequireOnce('Includes/Api/Abstract/Objects/Route.class.php');
	}

	function IncludeAbstractResponses() {
		$this->Xo->RequireOnce('Includes/Api/Abstract/Response.class.php');

		$this->Xo->RequireOnce('Includes/Api/Abstract/Responses/IndexResponse.class.php');
		$this->Xo->RequireOnce('Includes/Api/Abstract/Responses/ConfigResponse.class.php');
		$this->Xo->RequireOnce('Includes/Api/Abstract/Responses/RoutesResponse.class.php');
		$this->Xo->RequireOnce('Includes/Api/Abstract/Responses/PostsGetResponse.class.php');
		$this->Xo->RequireOnce('Includes/Api/Abstract/Responses/PostsFilterResponse.class.php');
		$this->Xo->RequireOnce('Includes/Api/Abstract/Responses/MenusResponse.class.php');
		$this->Xo->RequireOnce('Includes/Api/Abstract/Responses/TermsResponse.class.php');
		$this->Xo->RequireOnce('Includes/Api/Abstract/Responses/OptionsResponse.class.php');
	}

	function IncludeApiBaseServices() {
		$this->Xo->RequireOnce('Includes/Api/Classes/Router.class.php');
		$this->Xo->RequireOnce('Includes/Api/Classes/Reflector.class.php');
	}

	function IncludeApiControllers() {
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