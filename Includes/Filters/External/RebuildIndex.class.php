<?php

/**
 * Filter class to rebuild index files which may include data related to the given post.
 * 
 * @since 1.0.0
 */
class XoFilterRebuildIndex
{
	/**
	 * @var Xo
	 */
	var $Xo;

	function __construct(Xo $Xo) {
		$this->Xo = $Xo;

		add_action('save_post', array($this, 'RebuildIndexes'), 20, 0);
	}

	function RebuildIndexes() {
		$this->RebuildSrcIndex();
		$this->RebuildDistIndex();
	}

	private function RebuildSrcIndex() {
		if (!$index = $this->Xo->Services->Options->GetOption('xo_index_src', false))
			return false;

		$file = get_template_directory() . $index;

		if (!$newIndex = $this->Xo->Services->IndexBuilder->BuildSrcIndex(false))
			return false;

		return $this->WriteIndex($file, $newIndex);
	}

	private function RebuildDistIndex() {
		if (!$index = $this->Xo->Services->Options->GetOption('xo_index_dist', false))
			return false;

		$file = get_template_directory() . $index;

		if (!$newIndex = $this->Xo->Services->IndexBuilder->BuildDistIndex(false))
			return false;

		return $this->WriteIndex($file, $newIndex);
	}

	private function WriteIndex($file, $content) {
		if (!$handle = fopen($file, 'r+'))
			return false;

		flock($handle, LOCK_EX);
		fseek($handle, 0);

		if (fwrite($handle, $content))
			ftruncate($handle, ftell($handle));

		fflush($handle);
		flock($handle, LOCK_UN);
		fclose($handle);

		return true;
	}
}