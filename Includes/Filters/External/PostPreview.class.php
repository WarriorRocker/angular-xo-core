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
	var $Xo;

	function __construct(Xo $Xo) {
		$this->Xo = $Xo;

		add_filter('preview_post_link', array($this, 'PreviewLink'), 11, 2);
	}

	function PreviewLink($link, WP_Post $post) {
		if (!$this->Xo->Services->Options->GetOption('xo_routing_previews_enabled', false))
			return $link;

		return get_site_url() . '/' . 'xo-' . $post->post_type . '-preview' .
			(($post->post_type == 'page') ? '-' : '/') . $post->ID;
	}
}