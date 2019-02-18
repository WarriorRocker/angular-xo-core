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
	var $Xo;

	var $apiEndpoint = '/xo-api';
	var $classQueryVar = 'xo_api_class';
	var $methodQueryVar = 'xo_api_method';

	var $controllers = array();

	function __construct(Xo $Xo) {
		$this->Xo = $Xo;

		add_action('init', array($this, 'Init'), 10, 0);
		add_action('parse_request', array($this, 'ApiQuery'), 0, 0);
	}

	function Init() {
		global $wp;

		$wp->add_query_var($this->classQueryVar);
		$wp->add_query_var($this->methodQueryVar);

		$this->AddRewrites();
	}

	function AddRewrites() {
		$apiEndpoint = ltrim($this->apiEndpoint, '/');

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

	function AddController($endpoint, $class) {
		$class = apply_filters('xo/api/router/controllers/add', $class, $endpoint);
		$this->controllers[$endpoint] = $class;
	}

	function ApiQuery() {
		global $wp;

		if (empty($wp->query_vars[$this->classQueryVar]))
			return;

		header('Content-Type: application/json');

		$endpoint = $wp->query_vars[$this->classQueryVar];
		$class = apply_filters('xo/api/router/controllers/get', $this->controllers[$endpoint], $endpoint);

		if (!$class)
			return $this->ReturnError(__('No class defined for endpoint.', 'xo'));

		if (!class_exists($class))
			return $this->ReturnError(sprintf(__('Class %s was not found.', 'xo'), $class));

		$method = ((!empty($wp->query_vars[$this->methodQueryVar])) ? ucfirst($wp->query_vars[$this->methodQueryVar]) : 'Index');
		$method = apply_filters('xo/api/router/methods/get', $method, $class, $endpoint);

		if (!$method)
			return $this->ReturnError(__('No method determined for endpoint.', 'xo'));

		if (!is_callable(array($class, $method)))
			return $this->ReturnError(sprintf(__('Method %s in class %s was not found or private.', 'xo'), $method, $class));

		if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS')
			exit;

		$request = json_decode(file_get_contents("php://input"), true);

		if (is_object($request))
			$request = get_object_vars($request);

		$request = (((is_array($request)) && count($request)) ? $request : $_REQUEST);
		$request = apply_filters('xo/api/router/request/get', $request, $method, $class, $endpoint);

		$reflect = new ReflectionMethod($class, $method);
		if (($params = $reflect->getParameters()) && (!$params[0]->isOptional()) && (!$request))
			$this->ReturnError(sprintf(__('Method %s in class %s expects %u parameters.', 'xo'), $method, $class, count($params)));

		$response = call_user_func(array(new $class($this->Xo), $method), $request);

		$response = apply_filters('xo/api/router/response', $response, $request, $method, $class, $endpoint);

		$this->ReturnResponse($response);
	}

	function ReturnResponse($response) {
		echo json_encode($response);
		die();
	}

	function ReturnError($message) {
		header('HTTP/1.0 404 Not Found');
		$this->ReturnResponse(new XoApiAbstractResponse(false, $message));
	}
}