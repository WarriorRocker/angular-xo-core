<?php

/**
 * Service class used to generate an Xo/WordPress compatible index.
 *
 * @since 1.0.0
 */
class XoServiceIndexBuilder
{
	/**
	 * @var Xo
	 */
	var $Xo;

	/**
	 * @var string
	 */
	var $appConfigEntrypoint = 'xo-config';

	function __construct(Xo $Xo) {
		$this->Xo = $Xo;

		add_action('xo/index/build/src', array($this, 'BuildSrcIndex'), 10, 0);
		add_action('xo/index/build/dist', array($this, 'BuildDistIndex'), 10, 0);
	}

	function CheckAppConfigSrcEntrypoint() {
		if (!$output = $this->GetTemplateIndex('xo_index_dist'))
			return false;

		return $this->CheckAppConfigEntrypoint($output);
	}

	function CheckAppConfigDistEntrypoint() {
		if (!$output = $this->GetTemplateIndex('xo_index_dist'))
			return false;

		return $this->CheckAppConfigEntrypoint($output);
	}

	function CheckAppConfigEntrypoint($output) {
		return (strpos($output, '<script id="' . $this->appConfigEntrypoint . '"') !== false);
	}

	function BuildSrcIndex($echo = true) {
		if (!$output = $this->GetTemplateIndex('xo_index_src'))
			return false;

		$this->AddAppConfig($output, false);

		$output = apply_filters('xo/index/build/src', $output);

		if ($echo)
			echo $output;

		return $output;
	}

	function BuildDistIndex($echo = true) {
		if (!$output = $this->GetTemplateIndex('xo_index_dist'))
			return false;

		$this->AddAppConfig($output);

		$output = apply_filters('xo/index/build/dist', $output);

		if ($echo)
			echo $output;

		return $output;
	}

	function RenderDistIndex($echo = true) {
		if (!$output = $this->GetTemplateIndex('xo_index_dist'))
			return false;

		$this->AddWpHead($output);

		$this->AddWpFooter($output);

		$output = apply_filters('xo/index/render/dist');

		if ($echo)
			echo $output;

		return $output;
	}

	function AddWpHead(&$output) {
		if (($headPos = strpos($output, '</head>')) !== false) {
			ob_start();
			wp_head();
			$wpHead = ob_get_clean();

			$wpHead = apply_filters('xo/index/build/header', $wpHead);

			$this->InsertBetween($output, $wpHead, $headPos);
		}
	}

	function AddWpFooter(&$output) {
		if (($bodyPos = strpos($output, '</body>')) !== false) {
			ob_start();
			wp_footer();
			$wpFooter = ob_get_clean();

			$wpFooter = apply_filters('xo/index/build/footer', $wpFooter);

			$this->InsertBetween($output, $wpFooter, $bodyPos);
		}
	}

	function AddAppConfig(&$output, $relative = true) {
		$XoApiConfigController = new XoApiControllerConfig($this->Xo);
		$config = $XoApiConfigController->Get();

		if ((!$relative) && (!empty($config->config['paths']))) {
			if (!empty($config->config['paths']['apiUrl']))
				$config->config['paths']['apiUrl'] = get_site_url() . $config->config['paths']['apiUrl'];
		}

		$config = apply_filters('xo/index/build/config', $config);

		if (!$config->success)
			return false;

		$scriptReplace = implode("\n", array(
			'<script id="' . $this->appConfigEntrypoint . '" type="text/javascript">',
			'/* <![CDATA[ */',
			'var appConfig = ' . json_encode($config->config) . ';',
			'/* ]]> */',
			'</script>'
		));

		if ((($scriptStartPos = strpos($output, '<script id="' . $this->appConfigEntrypoint . '"')) !== false) &&
			(($scriptEndPos = strpos($output, '</script>', $scriptStartPos)) !== false)) {
			$this->InsertBetween($output, $scriptReplace, $scriptStartPos, $scriptEndPos + 9);
			return true;
		}

		return false;
	}

	public function GetTemplateIndex($option) {
		if (!$index = $this->Xo->Services->Options->GetOption($option, false))
			return false;

		$file = get_template_directory() . $index;

		if (!file_exists($file))
			return false;

		return file_get_contents($file);
	}

	public function InsertBetween(&$stream, $content, $start, $end = false) {
		if (!$end)
			$end = $start;

		$stream = substr($stream, 0, $start) . $content . substr($stream, $end);
	}
}