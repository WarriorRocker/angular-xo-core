<?php

/**
 * Filter class responsible for generating various apache mod_rewrite rules.
 *
 * @since 1.0.0
 */
class XoFilterModRewrite
{
	/**
	 * @var Xo
	 */
	var $Xo;

	/**
	 * @var XoServiceAdminNotice
	 */
	var $UpdateNotice;

	function __construct(Xo $Xo) {
		$this->Xo = $Xo;

		$this->UpdateNotice = new XoServiceAdminNotice(
			'angular-xo-rewrites-update-notice',
			array($this, 'RenderUpdateNotice')
		);

		//todo: investigate using generate_rewrite_rules and non_wp_rules
		add_filter('mod_rewrite_rules', array($this, 'ModifyRewrites'), 20, 1);
	}

	function __destruct() {
		remove_filter('mod_rewrite_rules', array($this, 'ModifyRewrites'), 20);
	}

	function ModifyRewrites($rulesOriginal) {
		$rulesUpdated = $rulesOriginal;
		$rulesHead = '# Modified by ' . $this->Xo->name. "\n";

		$this->UpdateRewrites($rulesUpdated);

		if ($rulesUpdated != $rulesOriginal) {
			//$this->UpdateNotice->RegisterNotice();

			$this->AddIndentsToRules($rulesUpdated);
			return $rulesHead . $rulesUpdated;
		}

		return $rulesOriginal;
	}

	function UpdateRewrites(&$rules) {
		$this->AddAccessControlHeaders($rules);

		if ($this->UpdateEntryPointRules($rules)) {
			$this->AddWpJsonRule($rules);
			$this->AddXoApiRule($rules);
		}
	}

	function AddAccessControlHeaders(&$rules) {
		$mode = $this->Xo->Services->Options->GetOption('xo_api_access_control_mode', '');

		if ($mode == 'default')
			return;

		if ($mode == 'all') {
			$rules = implode("\n", array(
				'<IfModule mod_headers.c>',
				'Header add Access-Control-Allow-Origin "*"',
				//'Header add Access-Control-Allow-Headers "origin, x-requested-with, content-type"',
				//'Header add Access-Control-Allow-Methods "PUT, GET, POST, DELETE, OPTIONS"',
				'</IfModule>'
			)) . "\n\n" . $rules;
		} else if ($mode == 'list') {
			$hosts = $this->Xo->Services->Options->GetOption('xo_access_control_allowed_hosts', '');

			if (empty($hosts))
				return;

			$hostsFormatted = str_replace("\n", '|', $hosts);

			$rules = implode("\n", array(
				'<IfModule mod_headers.c>',
				'SetEnvIf Origin "http(s)?://(www\.)?(' . $hostsFormatted . ')$" AccessControlAllowOrigin=$0$1',
				'Header add Access-Control-Allow-Origin %{AccessControlAllowOrigin}e env=AccessControlAllowOrigin',
				'</IfModule>'
			)) . "\n\n" . $rules;
		}
	}

	function UpdateEntryPointRules(&$rules) {
		if ($this->Xo->Services->Options->GetOption('xo_index_redirect_mode') != 'offline')
			return false;

		if (!$index = $this->Xo->Services->Options->GetOption('xo_index_dist', false))
			return false;

		$indexRel = wp_make_link_relative(get_bloginfo('template_url')) . $index;

		$rules = str_replace(
			'RewriteRule ^index\.php$ - [L]',
			'RewriteRule ^/?$ ' . $indexRel . ' [L]',
			$rules
		);

		$rules = str_replace(
			'RewriteRule . '. $this->GetHomeRoot() . 'index.php [L]',
			'RewriteRule . ' . $indexRel . ' [L]',
			$rules
		);

		return true;
	}

	function AddWpJsonRule(&$rules) {
		if (($pos = strpos($rules, 'RewriteRule')) === false)
			return;

		$wpJsonEndpoint = ltrim('/wp-json', '/');

		$rules = substr($rules, 0, $pos) .
			'RewriteRule ^' . $wpJsonEndpoint . '/(.*)$ /index.php [NC,L]' . "\n" .
			substr($rules, $pos);
	}

	function AddXoApiRule(&$rules) {
		if (($pos = strpos($rules, 'RewriteRule')) === false)
			return;

		if ((!$this->Xo->Services->Options->GetOption('xo_api_enabled', false))
			|| (!$apiEndpoint = $this->Xo->Services->Options->GetOption('xo_api_endpoint')))
			return;

		$apiEndpoint = ltrim($apiEndpoint, '/');

		$rules = substr($rules, 0, $pos) .
			'RewriteRule ^' . $apiEndpoint . '/(.*)$ /index.php [NC,L]' . "\n" .
			substr($rules, $pos);
	}

	function GetHomeRoot() {
		$url = parse_url(home_url());

		if (isset($url['path']))
			return trailingslashit($url['path']);

		return '/';
	}

	function AddIndentsToRules(&$rules) {
		$level = 0;

		$rules = array_map('trim', explode("\n", $rules));

		foreach ($rules as &$rule) {
			if ($level < 0)
				$level = 0;

			$ruleCopy = $rule;

			if (substr($rule, 0, 2) == '</')
				$level = (($level > 0) ? ($level - 1) : 0);

			if ($level)
				$rule = str_repeat("\t", $level) . $rule;

			if ((substr($ruleCopy, 0 , 1) == '<') && (substr($ruleCopy, 1, 1) != '/'))
				$level++;
		}

		$rules = implode("\n", $rules);
	}

	function RenderUpdateNotice($settings) {
		$output = '<p><strong>' . sprintf(
			__('%s rewrite rules updated.', 'xo'),
			$this->Xo->name
		) . '</strong></p>';

		return $output;
	}
}