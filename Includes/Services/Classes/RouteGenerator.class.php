<?php

/**
 * Service class used to generate Angular compatible Route configurations.
 *
 * @since 1.0.0
 */
class XoServiceRouteGenerator
{
	/**
	 * @var Xo
	 */
	var $Xo;

	function __construct(Xo $Xo) {
		$this->Xo = $Xo;
	}

	/**
	 * Get all available routes in an Angular Route compatible format.
	 * 
	 * @since 1.0.0
	 * 
	 * @param boolean $includeDraftsAndPreviews Optionally include draft and preview routes.
	 * @return array Angular Routes.
	 */
	public function GetRoutes($includeDraftsAndPreviews = false) {
		$routes = array();

		// Check if drafts and previews should be included
		if ($includeDraftsAndPreviews) {
			// Add routes for page drafts
			$this->AddRoutesForPageDrafts($routes);

			// Add routes for page previews
			$this->AddRoutesForPagePreviews($routes);

			// Add routes for post drafts and previews
			$this->AddRoutesForPostDraftsAndPreviews($routes);
		}

		// Add routes for pages
		$this->AddRoutesForPages($routes);

		// Add routes for custom post types
		$this->AddRoutesForPosts($routes);

		// Add route for the 404 page
		$this->AddRouteFor404Page($routes);

		$routes = apply_filters('xo/routes/get', $routes);

		return $routes;
	}

	public function AddRoutesForPages(&$routes) {
		$page404Id = intval($this->Xo->Services->Options->GetOption('xo_404_page_id', 0));

		$posts = get_posts(array(
			'post_status' => 'publish',
			'post_type' => 'page',
			'posts_per_page' => -1,
			'post__not_in' => array($page404Id),
			'fields' => 'ids'
		));

		foreach ($posts as $postId) {
			if ($attrs = $this->Xo->Services->TemplateReader->GetTemplateForPost($postId)) {
				$path = ltrim(wp_make_link_relative(get_permalink($postId)), '/');
				$routes[] = new XoApiAbstractRoute($path, $attrs['loadChildren'], 'full');
			}
		}
	}

	public function AddRouteFor404Page(&$routes) {
		if (($page404Id = intval($this->Xo->Services->Options->GetOption('xo_404_page_id', 0))) &&
			($attrs = $this->Xo->Services->TemplateReader->GetTemplateForPost($page404Id))) {
			$url = wp_make_link_relative(get_permalink($page404Id));
			$routes[] = new XoApiAbstractRoute('**', $attrs['loadChildren'], 'full', array(
				'url' => $url
			));
		}
	}

	public function AddRoutesForPosts(&$routes) {
		global $wp_post_types;

		foreach ($wp_post_types as $post_type => $post_type_config) {
			if ((!$post_type_config->public) || ($post_type == 'page'))
				continue;

			if ((!$template = $this->Xo->Services->Options->GetOption('xo_' . $post_type . '_template', false))
				|| (!$attrs = $this->Xo->Services->TemplateReader->GetAnnotatedTemplate($template)))
				continue;

			$rewrite = ((isset($post_type_config->rewrite['slug'])) ? $post_type_config->rewrite['slug'] : $post_type);
			$routes[] = new XoApiAbstractRoute($rewrite, $attrs['loadChildren'], 'prefix');
		}
	}

	public function AddRoutesForPageDrafts(&$routes) {
		$posts = get_posts(array(
			'post_status' => 'draft',
			'post_type' => 'page',
			'posts_per_page' => -1,
			'fields' => 'ids'
		));

		foreach ($posts as $postId) {
		    if ($attrs = $this->Xo->Services->TemplateReader->GetTemplateForPost($postId)) {
				$routes[] = new XoApiAbstractRoute('xo-page-preview-' . $postId, $attrs['loadChildren'], 'full', array(
					'postId' => $postId
				));
			}
		}
	}

	public function AddRoutesForPagePreviews(&$routes) {
		$posts = get_posts(array(
			'post_status' => 'publish',
			'post_type' => 'page',
			'posts_per_page' => -1,
			'fields' => 'ids'
		));

		foreach ($posts as $postId) {
			if (($attrs = $this->Xo->Services->TemplateReader->GetTemplateForPost($postId)) &&
				(wp_get_post_autosave($postId, get_current_user_id()))) {
				$routes[] = new XoApiAbstractRoute('xo-page-preview-' . $postId, $attrs['loadChildren'], 'full', array(
					'postId' => $postId
				));
			}
		}
	}

	public function AddRoutesForPostDraftsAndPreviews(&$routes) {
		global $wp_post_types;

		foreach ($wp_post_types as $post_type => $post_type_config) {
			if ((!$post_type_config->public) || ($post_type == 'page'))
				continue;

			if ((!$template = $this->Xo->Services->Options->GetOption('xo_' . $post_type . '_template', false))
				|| (!$attrs = $this->Xo->Services->TemplateReader->GetAnnotatedTemplate($template)))
				continue;

			$routes[] = new XoApiAbstractRoute('xo-' . $post_type . '-preview', $attrs['loadChildren'], 'prefix', array(
				'postPreview' => true
			));
		}
	}
}