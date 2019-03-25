<?php

class XoFilterEntrypoints
{
	/**
	 * @var Xo
	 */
	protected $Xo;

	public function __construct(Xo $Xo) {
		$this->Xo = $Xo;

		add_filter('xo/rewrite/entrypoint/index', array($this, 'AddIndexEntrypoint'), 10, 1);
	}

	public function AddIndexEntrypoint($entrypoint) {
		if ($this->Xo->Services->Options->GetOption('xo_index_redirect_mode') != 'offline')
			return $entrypoint;

		if (!$index = $this->Xo->Services->Options->GetOption('xo_index_dist', false))
			return $entrypoint;

		return wp_make_link_relative(get_bloginfo('template_url')) . $index;
	}
}