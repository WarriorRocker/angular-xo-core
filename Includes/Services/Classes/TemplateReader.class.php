<?php

/**
 * Service class used to parse templates for inclusion with Xo and WordPress.
 *
 * @since 1.0.0
 */
class XoServiceTemplateReader
{
	/**
	 * @var Xo
	 */
	var $Xo;

	/**
	 * @var XoServiceAdminNotice
	 */
	var $UpdateTemplatesNotice;

	var $templateDir;

	var $templatesPath;
	var $templatesExtensions = array('ts');

	var $commentBlockRegex = '/\/\*\*\s*?\n(.*?)\n\s*?\*\//is';
	var $annotationsRegex = '/(?: *\*+ *@Xo)(?P<key>\w+)(?: )(?P<value>[ \w!,]+)/';
	var $annotationsFormats = array(
		'disableEditor' => 'boolean',
		'defaultTemplate' => 'boolean',
		'postTypes' => 'array',
		'acfGroups' => 'array'
	);

	var $annotatedTemplates = array();

	function __construct(Xo $Xo) {
		$this->Xo = $Xo;

		add_action('init', array($this, 'Init'), 10, 0);

		$this->UpdateTemplatesNotice = new XoServiceAdminNotice(
			'angular-xo-update-templates-notice',
			array($this, 'RenderUpdateTemplatesNotice')
		);
	}

	function Init() {
		$this->templateDir = get_template_directory();
		$this->templatesPath = $this->Xo->Services->Options->GetOption('xo_templates_path', '');
	}

	function GetTemplateForPost($post) {
		$this->GetAnnotatedTemplates();

		$post = get_post($post);

		if ((($template = get_page_template_slug($post))) &&
			(!empty($this->annotatedTemplates[$template])) &&
			($attrs = $this->annotatedTemplates[$template])) {
			return $attrs;
		}

		if (($post->post_type != 'page')
			&& ($template = get_option('xo_' . $post->post_type . '_template'))
			&& ($attrs = $this->annotatedTemplates[$template])) {
			return $attrs;
		}

		if (!empty($this->annotatedTemplates['default']))
			return $this->annotatedTemplates['default'];

		return false;
	}

	function GetAnnotatedTemplates() {
		if (!$this->Xo->Services->Options->GetOption('xo_templates_reader_enabled', false))
			return array();

		if (!$this->annotatedTemplates) {
			$cachingEnabled = $this->Xo->Services->Options->GetOption('xo_templates_cache_enabled', false);
			$this->annotatedTemplates = $this->GetTemplates($cachingEnabled);
		}

		return $this->annotatedTemplates;
	}

	function GetAnnotatedTemplate($template) {
		$this->GetAnnotatedTemplates();

		if (!empty($this->annotatedTemplates[$template]))
			return array_merge(
				$this->annotatedTemplates[$template],
				array(
					'template' => $template
				)
			);

		return false;
	}

	function GetTemplates($caching = true, $useCache = true) {
		$templatesCache = $this->Xo->Services->Options->GetOption('xo_templates_cache', array());

		if (($caching) && ($useCache) && (!empty($templatesCache)))
		    return $templatesCache;

		$templates = array();

		if ($files = $this->GetTemplateFiles()) {
			foreach ($files as $file) {
				if ($attrs = $this->ParseTemplateCommentBlocks($file)) {
					$isDefault = (!empty($attrs['defaultTemplate']));
					$templates[($isDefault ? 'default' : $file)] = array_merge(
						$attrs,
						array(
							'template' => $file
						)
					);
				}
			}
		}

		if (($caching) && ((!$useCache) || ($templatesCache) || ($templates))) {
			update_option('xo_templates_cache', $templates);
			$this->UpdateTemplatesNotice->RegisterNotice();
		}

		$templates = apply_filters('xo/templates/get', $templates);

		return $templates;
	}

	private function GetTemplateFiles() {
		$length = strlen($this->templateDir) + 1;
		$files = array();

		if (file_exists($this->templateDir . $this->templatesPath)) {
			$iterator = new RecursiveIteratorIterator(
				new RecursiveDirectoryIterator(
					$this->templateDir . $this->templatesPath,
					RecursiveDirectoryIterator::SKIP_DOTS
				)
			);

			foreach ($iterator as $fileinfo) {
				if (($extension = strtolower(pathinfo($fileinfo, PATHINFO_EXTENSION)))
				    && (!in_array($extension, $this->templatesExtensions)))
				    continue;

				$files[] = str_replace('\\', '/', substr($fileinfo,  $length));
			}
		}

		$files = apply_filters('xo/templates/files', $files);

		return $files;
	}

	private function ParseTemplateCommentBlocks($template) {
		$fileName = $this->templateDir . '/' . $template;

		if ((file_exists($fileName))
			&& ($contents = file_get_contents($fileName))
			&& (preg_match_all($this->commentBlockRegex, $contents, $matches, PREG_SET_ORDER))
			&& ($matches) && (count($matches))) {

			foreach ($matches as $match) {
				if ($attrs = $this->ParseTemplateCommentBlockAnnotations($template, $match[1]))
					return $attrs;
			}
		}

		return false;
	}

	private function ParseTemplateCommentBlockAnnotations($template, $block) {
		if (preg_match_all($this->annotationsRegex, $block, $matchAttrs, PREG_SET_ORDER)) {
			$attrs = array();

			foreach ($matchAttrs as $matchAttr) {
				$key = lcfirst($matchAttr['key']);
				$value = trim($matchAttr['value']);

				if ($key == 'loadChildren') {
					$trimPath = strlen(ltrim($this->templatesPath, '/'));
					$value = '.' . substr(substr($template, 0, strlen($template) - 3), $trimPath) . '#' . $value;
				} else if (array_key_exists($key, $this->annotationsFormats)) {
					if ($this->annotationsFormats[$key] == 'boolean') {
						$value = ((strtolower($value) == 'true') ? 1 : 0);
					} else if ($this->annotationsFormats[$key] == 'array') {
						$value = array_map('trim', explode(',', $value));
					}
				}

				$attrs[$key] = $value;
			}

			if ($attrs)
				return $attrs;
		}

		return false;
	}

	function RenderUpdateTemplatesNotice() {
		$output = '<p><strong>' . sprintf(
			__('%s templates cache updated.', 'xo'),
			$this->Xo->name
		) . '</strong></p>';

		return $output;
	}
}