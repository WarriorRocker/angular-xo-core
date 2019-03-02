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
	}

	function RenderDistIndex() {
		if (!$srcIndex = $this->Xo->Services->Options->GetOption('xo_index_dist', false))
			return false;

		$indexFile = get_template_directory() . $srcIndex;
		if (!file_exists($indexFile))
			return false;

		$output = file_get_contents($indexFile);

		$output = apply_filters('xo/index/get/dist', $output);
		if (empty($output))
			return false;

		if ($this->Xo->Services->Options->GetOption('xo_index_live_header', false))
			$this->AddWpHead($output);

		if ($this->Xo->Services->Options->GetOption('xo_index_live_footer', false))
			$this->AddWpFooter($output);

		if ($this->Xo->Services->Options->GetOption('xo_index_live_config', false))
			$this->AddAppConfig($output);

		$output = apply_filters('xo/index/render/dist', $output);
		if (empty($output))
			return false;

		return $output;
	}

	private function AddWpHead(&$output) {
		if (($headPos = strpos($output, '</head>')) !== false) {
			ob_start();
			wp_head();
			$wpHead = ob_get_clean();

			$wpHead = apply_filters('xo/index/build/header', $wpHead);

			$this->InsertBetween($output, $wpHead, $headPos);
		}
	}

	private function AddWpFooter(&$output) {
		if (($bodyPos = strpos($output, '</body>')) !== false) {
			ob_start();
			wp_footer();
			$wpFooter = ob_get_clean();

			$wpFooter = apply_filters('xo/index/build/footer', $wpFooter);

			$this->InsertBetween($output, $wpFooter, $bodyPos);
		}
	}

	private function AddAppConfig(&$output) {
		$XoApiConfigController = new XoApiControllerConfig($this->Xo);
		$config = $XoApiConfigController->Get();

		$config = apply_filters('xo/index/get/config', $config);
		if ((empty($config)) || (!$config->success))
			return false;

		$scriptReplace = implode("\n", array(
			'<script id="' . $this->appConfigEntrypoint . '" type="text/javascript">',
			'/* <![CDATA[ */',
			'var appConfig = ' . json_encode($config->config) . ';',
			'/* ]]> */',
			'</script>'
		));

		$scriptReplace = apply_filters('xo/index/build/config', $scriptReplace);
		if (empty($scriptReplace))
			return false;

		if ((($scriptStartPos = strpos($output, '<script id="' . $this->appConfigEntrypoint . '"')) !== false) &&
			(($scriptEndPos = strpos($output, '</script>', $scriptStartPos)) !== false)) {
			$this->InsertBetween($output, $scriptReplace, $scriptStartPos, $scriptEndPos + 9);
			return true;
		}

		return false;
	}

	function AddAppConfigEntrypoint() {
		if (!$srcIndex = $this->Xo->Services->Options->GetOption('xo_index_src', false))
			return false;

		$indexFile = get_template_directory() . $srcIndex;
		if (!file_exists($indexFile))
			return false;

		$output = file_get_contents($indexFile);

		$output = apply_filters('xo/index/get/src', $output);
		if (empty($output))
			return false;

		$entrypointCheck = '<script id="' . $this->appConfigEntrypoint . '"';

		$entrypointCheck = apply_filters('xo/index/check/entrypoint', $entrypointCheck);
		if (empty($entrypointCheck))
		    return false;

		if (strpos($output, $entrypointCheck) !== false)
		    return true;

		$bodyPos = strpos($output, '</body>');
		if ($bodyPos === false)
			return false;

		$entrypointInsert = '<script id="' . $this->appConfigEntrypoint
			. '" type="text/javascript"></script>' . "\n";

		$entrypointInsert = apply_filters('xo/index/build/entrypoint', $entrypointInsert);
		if (empty($entrypointInsert))
			return false;

		$this->InsertBetween($output, $entrypointInsert, $bodyPos);

		return $this->WriteIndex($indexFile, $output);
	}

	function CheckAppConfigEntrypoint() {
		if (!$srcIndex = $this->Xo->Services->Options->GetOption('xo_index_src', false))
			return false;

		$indexFile = get_template_directory() . $srcIndex;
		if (!file_exists($indexFile))
			return false;

		$output = file_get_contents($indexFile);

		$output = apply_filters('xo/index/get/src', $output);
		if (empty($output))
			return false;

		$entrypointCheck = '<script id="' . $this->appConfigEntrypoint . '"';

		$entrypointCheck = apply_filters('xo/index/check/entrypoint', $entrypointCheck);
		if (empty($entrypointCheck))
			return false;

		return (strpos($output, $entrypointCheck) !== false);
	}

	private function InsertBetween(&$stream, $content, $start, $end = false) {
		if (!$end)
			$end = $start;

		$stream = substr($stream, 0, $start) . $content . substr($stream, $end);
	}

	private function WriteIndex($file, $content) {
		if (!$handle = fopen($file, 'r+'))
			return false;

		flock($handle, LOCK_EX);
		fseek($handle, 0);

		if (fwrite($handle, $content))
			ftruncate($handle, ftell($handle));

		fflush($handle);
		flock($handle, LOCK_UN);
		fclose($handle);

		return true;
	}
}