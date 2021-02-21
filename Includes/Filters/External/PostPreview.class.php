<?php

/**
 * Filter class used to generate Xo compatible post preview links.
 * 
 * @since 1.0.0
 */
class XoFilterPostPreview
{
	/**
	 * @var Xo
	 */
	protected $Xo;

	function __construct(Xo $Xo) {
		$this->Xo = $Xo;

		add_filter('preview_post_link', array($this, 'PreviewLink'), 11, 2);
	}

	// Gutenberg preview links not filterable - https://github.com/WordPress/gutenberg/issues/13998
	function PreviewLink($link, WP_Post $post) {
		if (!$this->Xo->Services->Options->GetOption('xo_routing_previews_enabled', false))
			return $link;

		$link = $this->Xo->Services->Permalinks->GetPermalink($post) . '?postId=' . $post->ID;

		return $link;
	}
}
