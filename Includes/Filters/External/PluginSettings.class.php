<?php

/**
 * Filter class responsible for handling various hooks related to plugin status pages and changes.
 *
 * @since 1.0.0
 */
class XoFilterPluginSettings
{
	/**
	 * @var Xo
	 */
	var $Xo;

	/**
	 * @var XoServiceAdminNotice
	 */
	var $ActivationNotice;

	var $settingsHref = 'admin.php?page=angular-xo';
	var $commentsHref = 'https://github.com/WarriorRocker/angular-xo-wordpress/issues';

	function __construct(Xo $Xo) {
		$this->Xo = $Xo;

		add_action('activate_' . $this->Xo->plugin, array(&$this, 'ActivatePlugin'), 10, 0);
		add_action('deactivate_'. $this->Xo->plugin, array($this, 'DeactivatePlugin'), 10, 0);

		add_filter('plugin_action_links_' . $this->Xo->plugin, array($this, 'AddSettingsPageLink'), 1, 1);

		$this->ActivationNotice = new XoServiceAdminNotice(
			'angular-xo-activation-notice',
			array($this, 'RenderActivationNotice')
		);
	}

	function AddSettingsPageLink($links) {
		$links['settings'] =  '<a href="' . $this->settingsHref . '">' . __('Settings', 'xo') . '</a>';
		return $links;
	}

	function ActivatePlugin() {
		$setDefaults = $this->Xo->Services->Options->SetDefaults();

		$this->ActivationNotice->RegisterNotice(array(
			'setDefaults' => $setDefaults
		));
	}

	function DeactivatePlugin() {
		$this->Xo->Filters->ModRewrite->__destruct();
		flush_rewrite_rules();
	}

	function RenderActivationNotice($settings) {
		flush_rewrite_rules();

		$output = '<p><strong>' . sprintf(
			__('Thanks for choosing %s.', 'xo'),
			$this->Xo->name
		);

		if ($settings['setDefaults'])
			$output .= ' ' . sprintf(
				__('Default settings have been applied, change them %s.', 'xo'),
				'<a href="' . $this->settingsHref . '">' . __('here', 'xo') . '</a>'
			);

		$output .= '</strong></p>';

		return $output;
	}
}