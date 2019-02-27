<?php

/**
 * Tab adding debug output of profile info to the Xo Tools screen.
 *
 * @since 1.0.0
 */
class XoOptionsTabProfile extends XoOptionsAbstractTab
{
	public function Render() {
		$this->AddGeneralSection();
	}

	function AddGeneralSection() {
		global $wp_version;
		global $wpdb;

		$plugin = get_plugin_data(ABSPATH . 'wp-content/plugins/' . $this->Xo->plugin, false);

		$items = array();

		$items[] = array($plugin['Name'], $plugin['Version']);
		$items[] = array(__('WordPress', 'xo'), $wp_version);
		$items[] = array(__('MySQL', 'xo'), mysqli_get_server_info($wpdb->dbh));
		$items[] = array(__('PHP', 'xo'), phpversion());

		$annotatedTemplates = $this->Xo->Services->TemplateReader->GetAnnotatedTemplates();
		$items[] = array(__('Templates', 'xo'), (($annotatedTemplates) ? count($annotatedTemplates) : 0));

		$distIndexFound = $this->Xo->Services->IndexBuilder->GetDistIndex();
		$items[] = array(__('Dist Index', 'xo'), (($distIndexFound) ? __('Present', 'xo') : __('Not Found', 'xo')));

		if ($distIndexFound) {
			$distAppConfigFound = $this->Xo->Services->IndexBuilder->CheckAppConfigDistEntrypoint();
			$items[] = array(__('Dist appConfig', 'xo'), (($distAppConfigFound) ? __('Present', 'xo') : __('Not Found', 'xo')));
		}

		$srcIndexFound = $this->Xo->Services->IndexBuilder->GetSrcIndex();
		$items[] = array(__('Src Index', 'xo'), (($srcIndexFound) ? __('Present', 'xo') : __('Not Found', 'xo')));

		if ($srcIndexFound) {
			$srcAppConfigFound = $this->Xo->Services->IndexBuilder->CheckAppConfigSrcEntrypoint();
			$items[] = array(__('Src appConfig', 'xo'), (($srcAppConfigFound) ? __('Present', 'xo') : __('Not Found', 'xo')));
		}

		$output = '';

		foreach ($items as $item)
			$output .= '<p><strong>' . $item[0] . '</strong> => ' . $item[1] . '</p>';

		echo $output;
	}
}