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
	protected $Xo;

	protected $draftPrefix = 'xo-draft-';

	public function __construct(Xo $Xo) {
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

	protected function PathExistsInRoutes($routes, $path) {
		return !empty(array_filter($routes, function ($route) use ($path) {
			return $route->path == $path;
		}));
	}

	protected function AddRoutesForPages(&$routes) {
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
				$routes[] = new XoApiAbstractRoute($path, $attrs['lazyPath'], 'full');
			}
		}
	}

	protected function AddRoutesForPageDrafts(&$routes) {
		$posts = get_posts(array(
			'post_status' => 'draft',
			'post_type' => 'page',
			'posts_per_page' => -1,
			'fields' => 'ids'
		));

		foreach ($posts as $postId) {
		    if ($attrs = $this->Xo->Services->TemplateReader->GetTemplateForPost($postId)) {
				$routes[] = new XoApiAbstractRoute($this->draftPrefix . $postId, $attrs['lazyPath'], 'full');
			}
		}
	}

	protected function AddRouteFor404Page(&$routes) {
		// Check if there is a 404 page set and the template can be found
		if (($page404Id = intval($this->Xo->Services->Options->GetOption('xo_404_page_id', 0))) &&
			($attrs = $this->Xo->Services->TemplateReader->GetTemplateForPost($page404Id))) {
			$url = wp_make_link_relative(get_permalink($page404Id));
			$routes[] = new XoApiAbstractRoute('**', $attrs['lazyPath'], 'full', [
				'postId' => $page404Id
			]);
		}
	}

	protected function AddRoutesForPosts(&$routes) {
		$post_types = get_post_types([
			'public' => true
		], 'objects');

		// Obtain the permalink base
		$structure = get_option('permalink_structure');
		$base = ltrim(untrailingslashit(preg_replace('/(%)(.*?)(%)/', '', $structure)), '/');

		// Iterate through all available post types
		foreach ($post_types as $post_type => $post_type_config) {
			// Skip if the post type is a page
			if ($post_type == 'page')
				continue;

			// Get the template of the rewrite base
			if ((!$template = $this->Xo->Services->Options->GetOption('xo_' . $post_type . '_template', false))
				|| (!$attrs = $this->Xo->Services->TemplateReader->GetAnnotatedTemplate($template)))
				continue;

			// Start path with base if post type uses with_front
			$path = $post_type == 'post' || !empty($post_type_config->rewrite['with_front']) ? $base : '';

			// Add post type rewrite slug to path
			if (!empty($post_type_config->rewrite['slug'])) {
				$rewrite = ltrim(untrailingslashit(preg_replace('/(%)(.*?)(%)/', '', $post_type_config->rewrite['slug'])), '/');
				$path .= ($path ? '/' : '') . $rewrite;
			}

			if ($path && !$this->PathExistsInRoutes($routes, $path)) {
				// Generate route for a posts page which will handle individual post urls
				$routes[] = new XoApiAbstractRoute($path, $attrs['lazyPath'], 'prefix', array(
					'postType' => $post_type
				));
			}
		}
	}
}
