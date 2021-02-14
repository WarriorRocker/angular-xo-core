<?php

class XoServicePrerender
{
	/**
	 * @var Xo
	 */
	protected $Xo;

	var $publicApiEndpoint = '';
	var $adminApiEndpoint = 'https://prerender.io';
	var $serviceEndpoint = 'http://service.prerender.io';

	function __construct(Xo $Xo) {
		$this->Xo = $Xo;

		$this->Init();

		add_action('admin_init', array($this, 'AdminInit'), 10, 0);
	}

	protected function Init() {
	}

	public function AdminInit() {
		
	}

	public function GetPrerenderOutput($url) {
		$serviceEndpoint = $this->Xo->Services->Options->GetOption('xo_prerender_service_endpoint', '');
		$token = $this->Xo->Services->Options->GetOption('xo_prerender_token', '');

		$url = str_replace('http://pyc.local', 'https://dev-perfect-yacht-charter.pantheonsite.io', $url);

		$request = $serviceEndpoint . '/' .urlencode($url);
		$args = [
			'timeout' => 30,
			'headers' => [
				'X-Prerender-Token' => $token
			]
		];

		$response = wp_remote_get($request, $args);

		if ((is_wp_error($response))
			|| ($response['response']['message'] != 'OK'))
			return false;

		return $response['body'];
	}

	public function ShouldShowPrerender() {
		if (!$this->Xo->Services->Options->GetOption('xo_prerender_middleware_enabled'))
			return false;

		if (isset($_GET['_escaped_fragment_']))
			return true;

		$user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
		$agents = $this->Xo->Services->Options->GetOption('xo_prerender_user_agents', []);

		if (!empty($user_agent) && !empty($agents)) {
			foreach ($agents as $agent) {
				if (strpos($user_agent, $agent) !== false) {
					return true;
				}
			}
		}

		return false;
	}

	public function Login($username, $password) {
		$response = wp_remote_post($this->adminApiEndpoint . '/login', array(
			'body' => array(
				'username' => $username,
				'password' => $password
			)
		));

		if ((is_wp_error($response))
			|| ($response['response']['message'] != 'OK'))
			return false;

		$cookies = [];
		foreach ($response['cookies'] as $cookie) {
			$cookies[$cookie->name] = $cookie->value;
		}

		if (!empty($cookies)) {
			return $cookies;
		}

		return false;
	}

	public function GetUser($session) {
		$response = wp_remote_get($this->adminApiEndpoint . '/api/user', array(
			'cookies' => $this->GenerateSessionCookies($session)
		));

		if ((is_wp_error($response))
			|| ($response['response']['message'] != 'OK'))
			return false;

		return json_decode($response['body'], true);
	}

	public function GetCachedPages($session) {
		$response = wp_remote_get($this->adminApiEndpoint . '/api/cached-pages', array(
			'cookies' => $this->GenerateSessionCookies($session)
		));

		if ((is_wp_error($response))
			|| ($response['response']['message'] != 'OK'))
			return false;

		print_r(json_decode($response['body'], true));
	}

	function GenerateSessionCookies($session) {
		$cookies = array();

		foreach ($session as $name => $value)
			$cookies[] = new WP_Http_Cookie(array(
				'name' => $name,
				'value' => $value
			));

		return $cookies;
	}
}
