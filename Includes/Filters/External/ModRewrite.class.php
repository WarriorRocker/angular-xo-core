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

	var $apiEndpoint = '/xo-api';

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

	function ModifyRewrites($rules) {
		$rulesCopy = $rules;
		$rulesHead = '# Modified by ' . $this->Xo->name. "\n";

		$this->UpdateRewrites($rulesCopy);

		if ($rulesCopy != $rules) {
			$this->UpdateNotice->RegisterNotice();
			return $rulesHead . $rulesCopy;
		}

		return $rules;
	}

	function UpdateRewrites(&$rules) {
		if ($this->UpdateEntryPointRules($rules)) {
			$this->AddWpJsonRule($rules);
			$this->AddXoApiRule($rules);
		}

		$this->AddIndentsToRules($rules);
	}

	function UpdateEntryPointRules(&$rules) {
		if (!$this->Xo->Services->Options->GetOption('xo_index_redirect_enabled', false))
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

		$apiEndpoint = ltrim($this->apiEndpoint, '/');

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