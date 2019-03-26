<?php

/**
 * Tab adding debug output of profile info to the Xo Tools screen.
 *
 * @since 1.0.0
 */
class XoOptionsTabProfile extends XoOptionsAbstractFieldsTab
{
	/**
	 * Add the various sections for the Profile tab.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function Render() {
		echo '<div class="xo-form">';

		$this->AddGeneralSection();

		echo '</div>';
	}

	protected function AddGeneralSection() {
		global $wp_version;
		global $wpdb;

		$this->GenerateSection(
			__('Profile Information', 'xo'),
			__('Additional profile and environment information useful for debugging.', 'xo')
		);

		$plugin = get_plugin_data(ABSPATH . 'wp-content/plugins/' . $this->Xo->plugin, false);

		$items = array();

		$items[] = array($plugin['Name'], $plugin['Version']);
		$items[] = array(__('WordPress', 'xo'), $wp_version);
		$items[] = array(__('MySQL', 'xo'), mysqli_get_server_info($wpdb->dbh));
		$items[] = array(__('PHP', 'xo'), phpversion());

		$annotatedTemplates = $this->Xo->Services->TemplateReader->GetAnnotatedTemplates();
		$items[] = array(__('Templates', 'xo'), (($annotatedTemplates) ? count($annotatedTemplates) : 0));

		$entrypointFound = $this->Xo->Services->IndexBuilder->CheckAppConfigEntrypoint();
		$items[] = array(__('App Config Entrypoint', 'xo'), (($entrypointFound) ? __('Present', 'xo') : __('Not Found', 'xo')));

		$output = '';

		foreach ($items as $item)
			$output .= '<p><strong>' . $item[0] . '</strong> => ' . $item[1] . '</p>';

		echo $output;
	}
}