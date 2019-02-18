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

		$output = '';

		foreach ($items as $item)
			$output .= '<p><strong>' . $item[0] . '</strong> => ' . $item[1] . '</p>';

		echo $output;
	}

	//function AddTableRow(...$columns) {
	//    foreach ($columns as $column)
	//}
}