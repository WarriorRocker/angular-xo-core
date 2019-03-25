<?php

/**
 * Main entry point for all requests to the Xo API responsible for instantiating an API controller and returning the response.
 *
 * @since 1.0.0
 */
class XoApiClassRouter
{
	/**
	 * @var Xo
	 */
	protected $Xo;

	/**
	 * WordPress rewrite class matching query var.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $classQueryVar = 'xo_api_class';

	/**
	 * WordPress rewrite method matching query var.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $methodQueryVar = 'xo_api_method';

	/**
	 * Collection of endpoints and controllers defined for the Xo API.
	 *
	 * @since 1.0.0
	 *
	 * @var string[]
	 */
	public $controllers = array();

	public function __construct(Xo $Xo) {
		$this->Xo = $Xo;

		add_action('init', array($this, 'Init'), 10, 0);
		add_action('parse_request', array($this, 'ApiQueryByParseRequest'), 0, 0);
	}

	public function Init() {
		global $wp;

		$wp->add_query_var($this->classQueryVar);
		$wp->add_query_var($this->methodQueryVar);

		$this->AddRewrites();
	}

	/**
	 * Conditionally add rewrites used by the Xo API at the desired endpoint.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function AddRewrites() {
		if ((!$this->Xo->Services->Options->GetOption('xo_api_enabled', false))
			|| (!$apiEndpoint = $this->Xo->Services->Options->GetOption('xo_api_endpoint')))
			return;

		$apiEndpoint = ltrim($apiEndpoint, '/');

		add_rewrite_rule(
			'^' . $apiEndpoint . '\/$',
			'index.php?api_route=/',
			'top'
		);

		add_rewrite_rule(
			'^' . $apiEndpoint . '\/(\w*)$',
			'index.php?' . $this->classQueryVar . '=$matches[1]',
			'top'
		);

		add_rewrite_rule(
			'^' . $apiEndpoint . '\/(\w*)\/(\w*)$',
			'index.php?' . $this->classQueryVar . '=$matches[1]&'
				. $this->methodQueryVar . '=$matches[2]',
			'top'
		);
	}

	/**
	 * Add a new controller at the desired endpoint to the Xo API.
	 *
	 * @since 1.0.0
	 *
	 * @param string $endpoint Endpoint slug used to encapsulate methods provided by the class.
	 * @param string $class Name of the class to instantiate when called by the endpoint.
	 * @return void
	 */
	public function AddController($endpoint, $class) {
		$class = apply_filters('xo/api/router/controllers/add', $class, $endpoint);
		$this->controllers[$endpoint] = $class;
	}

	/**
	 * Attempt to parse the incomming request as an Xo API endpoint.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function ApiQueryByParseRequest() {
		global $wp;

		// Return to normal operation if the Xo API is disabled
		if (!$this->Xo->Services->Options->GetOption('xo_api_enabled', false))
			return;

		// Return to normal if the query does not match the class query var
		if (empty($wp->query_vars[$this->classQueryVar]))
			return;

		// Determine the class configured for the current endpoint
		$endpoint = $wp->query_vars[$this->classQueryVar];

		// Determine the desired method within the endpoint
		$method = ((!empty($wp->query_vars[$this->methodQueryVar])) ? ucfirst($wp->query_vars[$this->methodQueryVar]) : 'Index');

		// Set the header as json output
		header('Content-Type: application/json');

		// Exit and return no data if the request method is options, likely a CORS precursor
		if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS')
			exit;

		$request = $this->GetRequestFromPhpAndRequest();

		$response = $this->ApiQueryByEndpoint($endpoint, $method, $request);

		// Return the generated response object
		die(json_encode($response));
	}

	protected function GetRequestFromPhpAndRequest() {
		// Decode the body of the request, expected to be a json blob
		$request = json_decode(file_get_contents("php://input"), true);

		// Extract the object vars from the request if its an object
		if (is_object($request))
			$request = get_object_vars($request);

		// If the body was empty use the PHP combined $_REQUEST object instead
		$request = (((is_array($request)) && count($request)) ? $request : $_REQUEST);

		return $request;
	}

	protected function ApiQueryByEndpoint($endpoint, $method, $request) {
		if (empty($this->controllers[$endpoint]))
			return new XoApiAbstractResponse(false, __('No controller defined for endpoint.', 'xo'));

		$class = $this->controllers[$endpoint];

		// Return an error if the class does not exist or was not included
		if (!class_exists($class))
			new XoApiAbstractResponse(false, sprintf(__('Class %s was not found.', 'xo'), $class));

		// Return an error if the requested method is not public
		if (!is_callable(array($class, $method)))
			new XoApiAbstractResponse(false, sprintf(__('Method %s in class %s was not found or private.', 'xo'), $method, $class));

		// Create a class reflection to check if method requires params
		$reflect = new ReflectionMethod($class, $method);

		// Get the parameters of the method and check if they are optional
		if (($params = $reflect->getParameters()) && (!$params[0]->isOptional()) && (!$request)) {
			// Return an error if the parameters were expected by the method
			new XoApiAbstractResponse(false, sprintf(__('Method %s in class %s expects %u parameters.', 'xo'), $method, $class, count($params)));
		}

		// Call the requested method in a new instance of the requested class using the request object
		$response = call_user_func(array(new $class($this->Xo), $method), $request);

		$response = apply_filters('xo/api/router/response', $response, $endpoint, $method, $request);

		return $response;
	}
}