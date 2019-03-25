<?php

class XoFilterLiveIndex
{
	/**
	 * @var Xo
	 */
	protected $Xo;

	public function __construct(Xo $Xo) {
		$this->Xo = $Xo;

		add_filter('xo/index/dist/output', array($this, 'AddLiveOptionsToOuput'), 10, 1);
	}

	/**
	 * Add live options such as wp_head and wp_footer to the output stream.
	 *
	 * @since 1.1.1
	 *
	 * @param string $output Output stream.
	 */
	public function AddLiveOptionsToOuput($output) {
		if ($this->Xo->Services->Options->GetOption('xo_index_live_header', false))
			$this->Xo->Services->IndexBuilder->AddWpHeadToIndex($output);

		if ($this->Xo->Services->Options->GetOption('xo_index_live_footer', false))
			$this->Xo->Services->IndexBuilder->AddWpFooterToIndex($output);

		if ($this->Xo->Services->Options->GetOption('xo_index_live_config', false))
			$this->Xo->Services->IndexBuilder->AddAppConfigToIndex($output);

		return $output;
	}
}