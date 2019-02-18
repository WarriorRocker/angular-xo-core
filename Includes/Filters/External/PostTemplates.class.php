<?php

/**
 * Filter class used to add Xo for Angular compatible templates to the WordPress post edit screen.
 * 
 * @since 1.0.0
 */
class XoFilterPostTemplates
{
	/**
	 * @var Xo
	 */
	var $Xo;

	function __construct(Xo $Xo) {
		$this->Xo = $Xo;

		add_filter('template_include', array($this, 'TemplateInclude'), 1, 1);
		add_action('admin_init', array($this, 'RegisterTemplateFilters'), 10, 0);
	}

	function RegisterTemplateFilters() {
		add_filter('default_page_template_title', array($this, 'DefaultPageTemplateTitle'), 10, 2);
		add_filter('theme_page_templates', array($this, 'ThemeTemplates'), 10, 4);
	}

	function TemplateInclude($template) {
		if ($file = $this->Xo->GetFile('Includes/Theme/ThemeIndex.php'))
			return $file;

		return $template;
	}

	function ThemeTemplates($templates, WP_Theme $theme, $post, $post_type) {
		$annotatedTemplates = $this->Xo->Services->TemplateReader->GetAnnotatedTemplates();

		foreach ($annotatedTemplates as $template => $attrs)
			if (((empty($attrs['defaultTemplate'])) || ($attrs['defaultTemplate'] != 1))
				&& ($this->IsTemplateForPostType($attrs, $post_type)))
				$templates[$template] = $attrs['pageTemplate'];

		return $templates;
	}

	function DefaultPageTemplateTitle($title, $context) {
		$annotatedTemplates = $this->Xo->Services->TemplateReader->GetAnnotatedTemplates();

		foreach ($annotatedTemplates as $attrs)
			if ((isset($attrs['defaultTemplate']))
				&& ($attrs['defaultTemplate'] == 1))
				return $attrs['pageTemplate'];

		return $title;
	}

	private function IsTemplateForPostType($attrs, $post_type) {
		if (empty($attrs['postTypes']))
		    return true;

		$includes = array();
		foreach ($attrs['postTypes'] as $type) {
			if (substr($type, 0, 1) == '!') {
				if (substr($type, 1) == $post_type)
					return false;
			} else {
				$includes[] = $type;
			}
		}

		if ((!empty($includes)) && (!in_array($post_type, $includes)))
			return false;

		return true;
	}
}