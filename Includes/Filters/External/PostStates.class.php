<?php

/**
 * Filter class used to add and extend various post states used on WordPress post edit and list pages.
 * 
 * @since 1.0.0
 */
class XoFilterPostStates
{
	/**
	 * @var Xo
	 */
	var $Xo;

	function __construct(Xo $Xo) {
		$this->Xo = $Xo;

		add_action('admin_menu', array($this, 'AdminInit'), 10, 0);
	}

	function AdminInit() {
		add_filter('display_post_states', array($this, 'AddPostStates'), 10, 2);
	}

	function AddPostStates($post_states, WP_Post $post) {
		global $wp_post_types;

		foreach ($wp_post_types as $post_type => $post_type_config) {
			if ((!$post_type_config->public) || ($post_type == 'page'))
				continue;

			if (($page_id = intval($this->Xo->Services->Options->GetOption('xo_' . $post_type . '_page_id', 0)))
				&& ($page_id === $post->ID))
					$post_states['page_' . $post_type] = sprintf(__('%s Page', 'xo'), $post_type_config->label);
		}

		if ((($page_404_id = intval($this->Xo->Services->Options->GetOption('xo_404_page_id', 0))) && ($page_404_id === $post->ID))
			|| ((!$page_404_id) && (isset($post_states['page_on_front']))))
			$post_states['page_404'] = __('404 Page', 'xo');

		return $post_states;
	}
}