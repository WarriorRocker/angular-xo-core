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
		add_filter('xo/index/dist/output', array($this, 'AddApiCacheToOuput'), 30, 1);

		add_filter('xo/index/cache/requests', array($this, 'AddRequestsToApiCache'), 10, 1);
	}

	/**
	 * Add live options such as wp_head and wp_footer to the output stream.
	 *
	 * @since 1.2.0
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

	/**
	 * Add apiCache responses to the output stream.
	 *
	 * @since 0.1.0
	 *
	 * @param string $output Output stream.
	 */
	public function AddApiCacheToOuput($output) {
		$this->Xo->Services->IndexBuilder->AddApiCacheToIndex($output);

		return $output;
	}

	public function AddRequestsToApiCache($requests) {
		global $wp_query;

		$addRequests = array();

		$addRequests[] = array(
			'path' => '/routes/get'
		);

		$this->Xo->Services->IndexBuilder->AddApiCacheMenuRequests($addRequests);

		$this->Xo->Services->IndexBuilder->AddApiCacheOptionGroupRequests($addRequests);

		if ($wp_query->is_category || $wp_query->is_tag) {
			$this->Xo->Services->IndexBuilder->AddApiCacheTermRequests($addRequests);
		} else {
			$this->Xo->Services->IndexBuilder->AddApiCachePostRequests($addRequests);
		}

		return array_merge($requests, $addRequests);
	}
}