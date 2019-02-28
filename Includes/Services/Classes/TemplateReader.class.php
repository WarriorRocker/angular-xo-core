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

	/**
	 * Path to the currently active theme folder.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	var $templateDir;

	/**
	 * Relative path from the theme folder to search for template files.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	var $templatesPath;

	/**
	 * Array of file extensions which will be checked for annotations.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	var $templatesExtensions = array('ts');

	/**
	 * Regex used to retrieve the body of an annotations comment block.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	var $commentBlockRegex = '/\/\*\*\s*?\n(.*?)\n\s*?\*\//is';

	/**
	 * Regex used to retrieve annotations by a key value relationship.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	var $annotationsRegex = '/(?: *\*+ *@Xo)(?P<key>\w+)(?: )(?P<value>[ \w!,]+)/';

	/**
	 * Configuration array setting specific annotations to be treated as booleans or array. All others treated as string.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	var $annotationsFormats = array(
		'disableEditor' => 'boolean',
		'defaultTemplate' => 'boolean',
		'postTypes' => 'array',
		'acfGroups' => 'array'
	);

	/**
	 * Internal cache of annotated templates which are set and retrieved by GetAnnotatedTemplates().
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	var $annotatedTemplates = array();

	function __construct(Xo $Xo) {
		$this->Xo = $Xo;

		add_action('init', array($this, 'Init'), 10, 0);

		$this->UpdateTemplatesNotice = new XoServiceAdminNotice(
			'angular-xo-update-templates-notice',
			array($this, 'RenderUpdateTemplatesNotice')
		);
	}

	/**
	 * Set templatesDir and templatesPath.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	function Init() {
		$this->templateDir = get_template_directory();
		$this->templatesPath = $this->Xo->Services->Options->GetOption('xo_templates_path', '');
	}

	/**
	 * Get the annotated template data for a given post.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post|integer $post Post object or id.
	 * @return array|boolean Annotated template data.
	 */
	function GetTemplateForPost($post) {
		$this->GetAnnotatedTemplates();

		if (!is_object($post))
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

	/**
	 * Get all templates and annotations by either internal cache, wp_options cache, or by reading the template files.
	 *
	 * @since 1.0.0
	 *
	 * @return array All templates and annotations.
	 */
	function GetAnnotatedTemplates() {
		if (!$this->Xo->Services->Options->GetOption('xo_templates_reader_enabled', false))
			return array();

		if (!$this->annotatedTemplates) {
			$cachingEnabled = $this->Xo->Services->Options->GetOption('xo_templates_cache_enabled', false);
			$this->annotatedTemplates = $this->GetTemplates($cachingEnabled);
		}

		return $this->annotatedTemplates;
	}

	/**
	 * Get the annotations for a single template by name.
	 *
	 * @since 1.0.0
	 *
	 * @param string $template Name of the template relative to templatesPath.
	 *
	 * @return array|boolean Annotations for the given template.
	 */
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

	/**
	 * Get all templates and annotations, optionally from and setting wp_options cache.
	 *
	 * @since 1.0.0
	 *
	 * @param boolean $caching Whether the templates should be cached.
	 * @param mixed $useCache Whether to use the cache, if available.
	 * @return array All templates and annotations.
	 */
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
			$this->Xo->Services->Options->SetOption('xo_templates_cache', $templates);
			$this->UpdateTemplatesNotice->RegisterNotice();
		}

		$templates = apply_filters('xo/templates/get', $templates);

		return $templates;
	}

	/**
	 * Get all possible template files existing in the set path and file extensions.
	 *
	 * @since 1.0.0
	 *
	 * @return array All possible template files.
	 */
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

	/**
	 * Get the contents of the comment block from a possible template file.
	 *
	 * @since 1.0.0
	 *
	 * @param string $template Path of the template relative to templateDir.
	 * @return array|boolean Annotations.
	 */
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

	/**
	 * Format the annotations if found within a template comment block.
	 *
	 * @since 1.0.0
	 *
	 * @param string $template Path of the template relative to templateDir.
	 * @param string $block Raw contents of the comment block.
	 * @return array|boolean Formatted annotations.
	 */
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