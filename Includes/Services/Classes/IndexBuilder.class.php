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
	protected $Xo;

	/**
	 * Handle used to check and insert the App Config into the index.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $appConfigScriptId = 'xo-config';

	public function __construct(Xo $Xo) {
		$this->Xo = $Xo;
	}

	/**
	 * Read and return a rendered index optionally including wp_head, wp_footer, and App Config.
	 *
	 * @since 1.0.0
	 *
	 * @return boolean|string Rendered index output.
	 */
	public function RenderDistIndex() {
		if (!$srcIndex = $this->Xo->Services->Options->GetOption('xo_index_dist', false))
			return false;

		$indexFile = get_template_directory() . $srcIndex;
		if (!file_exists($indexFile))
			return false;

		$output = file_get_contents($indexFile);
		if (empty($output))
			return false;

		$output = apply_filters('xo/index/dist/output', $output);
		if (empty($output))
			return false;

		return $output;
	}

	/**
	 * Render and add wp_head to the index output before the closing HEAD tag.
	 *
	 * @since 1.0.0
	 *
	 * @param string $output Output stream.
	 * @return boolean Whether wp_head was successfully added to the output stream.
	 */
	public function AddWpHeadToIndex(&$output) {
		$headPos = strpos($output, '</head>');
		if ($headPos === false)
			return false;

		ob_start();
		wp_head();
		$wpHead = ob_get_clean();

		$wpHead = apply_filters('xo/index/build/header', $wpHead);
		if (empty($wpHead))
			return false;

		$this->InsertBetween($output, $wpHead, $headPos);

		return true;
	}

	/**
	 * Render and add wp_footer to the index output before the closing BODY tag.
	 *
	 * @since 1.0.0
	 *
	 * @param string $output Output stream.
	 * @return boolean Whether wp_footer was successfully added to the output stream.
	 */
	public function AddWpFooterToIndex(&$output) {
		$bodyPos = strpos($output, '</body>');
		if ($bodyPos === false)
			return false;

		ob_start();
		wp_footer();
		$wpFooter = ob_get_clean();

		$wpFooter = apply_filters('xo/index/build/footer', $wpFooter);
		if (empty($wpFooter))
			return false;

		$this->InsertBetween($output, $wpFooter, $bodyPos);

		return true;
	}

	/**
	 * Generate and add App Config to the index output within the entrypoint tag if found.
	 *
	 * @since 1.0.0
	 *
	 * @param string $output Output stream.
	 * @return boolean Whether App Config was successfully added to the output stream.
	 */
	public function AddAppConfigToIndex(&$output) {
		$XoApiConfigController = new XoApiControllerConfig($this->Xo);
		$config = $XoApiConfigController->Get();

		$config = apply_filters('xo/index/get/config', $config);
		if ((empty($config)) || (!$config->success))
			return false;

		$scriptReplace = implode("\n", array(
			'<script id="' . $this->appConfigScriptId . '" type="text/javascript">',
			'/* <![CDATA[ */',
			'var appConfig = ' . json_encode($config->config) . ';',
			'/* ]]> */',
			'</script>'
		));

		$scriptReplace = apply_filters('xo/index/build/config', $scriptReplace);
		if (empty($scriptReplace))
			return false;

		if ((($scriptStartPos = strpos($output, '<script id="' . $this->appConfigScriptId . '"')) !== false) &&
			(($scriptEndPos = strpos($output, '</script>', $scriptStartPos)) !== false)) {
			$this->InsertBetween($output, $scriptReplace, $scriptStartPos, $scriptEndPos + 9);
			return true;
		}

		return false;
	}

	/**
	 * Add the App Config entrypoint to the src index if the tag is not already present.
	 *
	 * @since 1.0.0
	 *
	 * @return boolean Whether the entrypoint was successfully found or added in the src index.
	 */
	public function AddAppConfigEntrypoint() {
		if (!$srcIndex = $this->Xo->Services->Options->GetOption('xo_index_src', false))
			return false;

		$indexFile = get_template_directory() . $srcIndex;
		if (!file_exists($indexFile))
			return false;

		$output = file_get_contents($indexFile);

		$output = apply_filters('xo/index/get/src', $output);
		if (empty($output))
			return false;

		$entrypointCheck = '<script id="' . $this->appConfigScriptId . '"';

		$entrypointCheck = apply_filters('xo/index/check/entrypoint', $entrypointCheck);
		if (empty($entrypointCheck))
		    return false;

		if (strpos($output, $entrypointCheck) !== false)
		    return true;

		$bodyPos = strpos($output, '</body>');
		if ($bodyPos === false)
			return false;

		$entrypointInsert = '<script id="' . $this->appConfigScriptId
			. '" type="text/javascript"></script>' . "\n";

		$entrypointInsert = apply_filters('xo/index/build/entrypoint', $entrypointInsert);
		if (empty($entrypointInsert))
			return false;

		$this->InsertBetween($output, $entrypointInsert, $bodyPos);

		return $this->WriteIndex($indexFile, $output);
	}

	/**
	 * Check whether the App Config entrypoint is found in the src index.
	 *
	 * @since 1.0.0
	 *
	 * @return boolean Whether the App Config entrypoint is found in the src index.
	 */
	public function CheckAppConfigEntrypoint() {
		if (!$srcIndex = $this->Xo->Services->Options->GetOption('xo_index_src', false))
			return false;

		$indexFile = get_template_directory() . $srcIndex;
		if (!file_exists($indexFile))
			return false;

		$output = file_get_contents($indexFile);

		$output = apply_filters('xo/index/get/src', $output);
		if (empty($output))
			return false;

		$entrypointCheck = '<script id="' . $this->appConfigScriptId . '"';

		$entrypointCheck = apply_filters('xo/index/check/entrypoint', $entrypointCheck);
		if (empty($entrypointCheck))
			return false;

		return (strpos($output, $entrypointCheck) !== false);
	}

	/**
	 * Insert some content string within another content string by start and end indexes.
	 *
	 * @since 1.0.0
	 *
	 * @param string $stream Stream string to modify passed by reference.
	 * @param string $content Content to insert.
	 * @param integer $start Starting index to insert from.
	 * @param boolean|integer $end Index to append from the stream, false if same as start index.
	 * @return void
	 */
	protected function InsertBetween(&$stream, $content, $start, $end = false) {
		if (!$end)
			$end = $start;

		$stream = substr($stream, 0, $start) . $content . substr($stream, $end);
	}

	/**
	 * Write the index file with the given content.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file Absolute path to the file to write.
	 * @param string $content Content to write into the file.
	 * @return boolean Whether the content was successfully written.
	 */
	protected function WriteIndex($file, $content) {
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