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
	 * @var XoApiControllerConfig
	 */
	public $Config;

	/**
	 * @var XoApiControllerRoutes
	 */
	public $Routes;

	/**
	 * @var XoApiControllerPosts
	 */
	public $Posts;

	/**
	 * @var XoApiControllerTerms
	 */
	public $Terms;

	/**
	 * @var XoApiControllerMenus
	 */
	public $Menus;

	/**
	 * @var XoApiControllerOptions
	 */
	public $Options;

	public function __construct(Xo $Xo) {
		$this->Xo = $Xo;

		$this->Includes();
		$this->Init();
	}

	protected function Init() {
		$this->Config = new XoApiControllerConfig($this->Xo);
		$this->Routes = new XoApiControllerRoutes($this->Xo);
		$this->Posts = new XoApiControllerPosts($this->Xo);
		$this->Terms = new XoApiControllerTerms($this->Xo);
		$this->Menus = new XoApiControllerMenus($this->Xo);
		$this->Options = new XoApiControllerOptions($this->Xo);

		//$this->AddDefaultControllers();
	}

	protected function AddDefaultControllers() {
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

		// Include the standard API controllers
		$this->IncludeApiControllers();
	}

	protected function IncludeAbstractObjects() {
		$this->Xo->RequireOnce('Includes/Api/Abstract/PostObject.class.php');
		$this->Xo->RequireOnce('Includes/Api/Abstract/Objects/Post.class.php');
		$this->Xo->RequireOnce('Includes/Api/Abstract/Objects/Menu.class.php');
		$this->Xo->RequireOnce('Includes/Api/Abstract/Objects/Term.class.php');
		$this->Xo->RequireOnce('Includes/Api/Abstract/Objects/Route.class.php');
		$this->Xo->RequireOnce('Includes/Api/Abstract/Objects/SitemapEntry.class.php');
	}

	protected function IncludeAbstractResponses() {
		$this->Xo->RequireOnce('Includes/Api/Abstract/Response.class.php');

		$this->Xo->RequireOnce('Includes/Api/Abstract/Responses/IndexResponse.class.php');

		$this->Xo->RequireOnce('Includes/Api/Abstract/Responses/ConfigGetResponse.class.php');

		$this->Xo->RequireOnce('Includes/Api/Abstract/Responses/RoutesGetResponse.class.php');
		$this->Xo->RequireOnce('Includes/Api/Abstract/Responses/RoutesBreadcrumbsResponse.class.php');
		$this->Xo->RequireOnce('Includes/Api/Abstract/Responses/RoutesSitemapResponse.class.php');

		$this->Xo->RequireOnce('Includes/Api/Abstract/Responses/PostsGetResponse.class.php');
		$this->Xo->RequireOnce('Includes/Api/Abstract/Responses/PostsFilterResponse.class.php');
		$this->Xo->RequireOnce('Includes/Api/Abstract/Responses/PostsConfigResponse.class.php');

		$this->Xo->RequireOnce('Includes/Api/Abstract/Responses/MenusGetResponse.class.php');

		$this->Xo->RequireOnce('Includes/Api/Abstract/Responses/TermsGetResponse.class.php');
		$this->Xo->RequireOnce('Includes/Api/Abstract/Responses/TermsFilterResponse.class.php');

		$this->Xo->RequireOnce('Includes/Api/Abstract/Responses/OptionsGetResponse.class.php');
	}

	protected function IncludeApiControllers() {
		$this->Xo->RequireOnce('Includes/Api/Abstract/Controller.class.php');

		$this->Xo->RequireOnce('Includes/Api/Controllers/ConfigController.class.php');
		$this->Xo->RequireOnce('Includes/Api/Controllers/RoutesController.class.php');
		$this->Xo->RequireOnce('Includes/Api/Controllers/PostsController.class.php');
		$this->Xo->RequireOnce('Includes/Api/Controllers/TermsController.class.php');
		$this->Xo->RequireOnce('Includes/Api/Controllers/OptionsController.class.php');
		$this->Xo->RequireOnce('Includes/Api/Controllers/MenusController.class.php');
		$this->Xo->RequireOnce('Includes/Api/Controllers/CommentsController.class.php');
	}

	public function RestRequest($method = 'GET', $endpoint = '', $query = [], $body = []) {
		$request = new WP_REST_Request($method, $endpoint);
	
		if (!empty($query))
			$request->set_query_params($query);
	
		if (!empty($body))
			$request->set_body_params($body);
	
		$response = rest_do_request($request);
		$server = rest_get_server();
	
		return $server->response_to_data($response, false);
	}
}