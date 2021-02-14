<?php

class XoFilterPostUpdate
{
	/**
	 * @var Xo
	 */
	protected $Xo;

	/**
	 * @var PostUpdatedNotice
	 */
	protected $PostUpdatedNotice;

	function __construct(Xo $Xo) {
		$this->Xo = $Xo;

		add_action('init', array($this, 'Init'), 10, 0);
		add_action('save_post', array($this, 'SavePost'), 20, 1);
	}

	function Init() {
		$this->PostUpdatedNotice = new XoServiceAdminNotice(
			'angular-xo-post-updated-notice',
			array($this, 'PostUpdatedNoticeRender')
		);
	}

	/**
	 * @since 2.0.0
	 */
	public function SavePost($postId) {
		if (wp_is_post_revision($postId))
			return;

		if ((!$post = get_post($postId)) ||
			($post->post_status == 'draft') ||
			($post->post_type == 'revision'))
			return;

		$url = get_permalink($postId);

		$session = $this->Xo->Services->Options->GetOption('xo_prerender_session', array());
		$cache = $session ? $this->Xo->Services->Prerender->CachePage($session, $url) : false;

		if ($cache) {
			$this->PostUpdatedNotice->RegisterNotice();
		}
	}

	/**
	 * @since 2.0.0
	 */
	public function PostUpdatedNoticeRender($settings) {
		$output = '<p><strong>' . 
			__('Prerender successfully requested page recache.', 'xo')
			. '</strong></p>';

		return $output;
	}
}