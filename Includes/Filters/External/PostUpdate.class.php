<?php

class XoFilterPostUpdate
{
	/**
	 * @var Xo
	 */
	protected $Xo;

	function __construct(XoPro $Xo) {
		$this->Xo = $Xo;

		// add_action('save_post', array($this, 'SavePost'), 20, 1); // [beta] Disable Xo File Cache
	}

	public function SavePost($postId) {
		if (wp_is_post_revision($postId))
			return;

		if ((!$post = get_post($postId)) ||
			($post->post_status == 'draft') ||
			($post->post_type == 'revision'))
			return;

		$url = wp_make_link_relative(get_permalink($postId));
	}
}