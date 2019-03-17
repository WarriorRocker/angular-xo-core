<?php

/**
 * Tab adding options related to the Xo API to the Xo General Settings screen.
 *
 * @since 1.0.0
 */
class XoOptionsTabApi extends XoOptionsAbstractSettingsTab
{
	/**
	 * @var XoServiceAdminNotice
	 */
	private $RewritesNeedUpdatingNotice;

	/**
	 * Add the various settings sections for the Xo API tab.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	function Init() {
		$this->RewritesNeedUpdatingNotice = new XoServiceAdminNotice(
			'angular-xo-rewrites-need-update-notice',
			array($this, 'RewritesNeedUpdatingNoticeRender')
		);

		$this->InitGeneralSection();
		$this->InitAccessControlSection();

		$this->DoAction();
	}

	/**
	 * Settings section for configuring API Endpoint and API Endable fields.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function InitGeneralSection() {
		$this->AddSettingsSection(
			'api_general_section',
			__('General', 'xo'),
			__('Manage general API options.', 'xo'),
			function ($section) {
				$this->AddGeneralSectionApiEnabledSetting($section);
				$this->AddGeneralSectionApiEndpointSetting($section);
			}
		);
	}

	/**
	 * Settings field for API Enabled.
	 * Used to enable the Xo API.
	 *
	 * @since 1.0.0
	 *
	 * @param string $section Name of the section.
	 * @return void
	 */
	private function AddGeneralSectionApiEnabledSetting($section) {
		$this->AddSettingsField(
			$section,
			'xo_api_enabled',
			__('API Enabled', 'xo'),
			function ($option, $states, $value) {
				return $this->GenerateInputCheckboxField(
					$option, $states, $value,
					__('Whether to enable the Xo API.', 'xo')
				);
			},
			function ($oldValue, $newValue, $option) {
				if ($oldValue !== $newValue)
					$this->RewritesNeedUpdatingNotice->RegisterNotice();
			}
		);
	}

	/**
	 * Settings field for API Endpoint.
	 * Used to set the endpoint for the Xo API.
	 *
	 * @since 1.0.0
	 *
	 * @param string $section Name of the section.
	 * @return void
	 */
	private function AddGeneralSectionApiEndpointSetting($section) {
		$this->AddSettingsField(
			$section,
			'xo_api_endpoint',
			__('API Endpoint', 'xo'),
			function ($option, $states, $value) {
				return $this->GenerateInputTextField(
					$option, $states, $value,
					__('Relative base path for the Xo API.', 'xo')
				);
			},
			function ($oldValue, $newValue, $option) {
				if ($oldValue !== $newValue)
					$this->RewritesNeedUpdatingNotice->RegisterNotice();
			}
		);
	}

	/**
	 * Settings section for configuring API Access Control Mode and Hosts fields.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function InitAccessControlSection() {
		$this->AddSettingsSection(
			'api_access_control_section',
			__('Access Control', 'xo'),
			__('Manage the API cross-origin requests access control header.', 'xo'),
			function ($section) {
				$this->AddAccessControlSectionModeSetting($section);
				$this->AddAccessControlSectionAllowedHostsSetting($section);
			}
		);
	}

	/**
	 * Settings field for Access Control Mode.
	 * Used to set the access control mode for cross origin requests.
	 *
	 * @since 1.0.0
	 *
	 * @param string $section Name of the section.
	 * @return void
	 */
	private function AddAccessControlSectionModeSetting($section) {
		$this->AddSettingsField(
			$section,
			'xo_api_access_control_mode',
			__('Access Control Mode', 'xo'),
			function ($option, $states, $value) {
				$choices = array(
					'default' => __('Default', 'xo'),
					'all' => __('All', 'xo'),
					'list' => __('List', 'xo')
				);

				$descriptions = array(
					__('Configures the way in which cross-origin (CORS) requests are handled.', 'xo'),
					sprintf(
						__('<strong>%s</strong> - %s', 'xo'),
						__('Default', 'xo'),
						__('Do not set an access control header, generally disabling cross-origin requests.', 'xo')
					),
					sprintf(
						__('<strong>%s</strong> - %s', 'xo'),
						__('All', 'xo'),
						__('Allow all hosts access to the Xo API, potentially unsafe.', 'xo')
					),
					sprintf(
						__('<strong>%s</strong> - %s', 'xo'),
						__('List', 'xo'),
						__('Set the access control header allowing only a specified list of hosts.', 'xo')
					)
				);

				return $this->GenerateSelectField($option, $states, $choices, false, $value, $descriptions);
			},
			function ($oldValue, $newValue, $option) {
				if ($oldValue !== $newValue)
					flush_rewrite_rules();
			}
		);
	}

	/**
	 * Settings field for Access Control Allowed Hosts.
	 * Used to set the the allowed hosts when using the List Access Control mode.
	 *
	 * @since 1.0.0
	 *
	 * @param string $section Name of the section.
	 * @return void
	 */
	private function AddAccessControlSectionAllowedHostsSetting($section) {
		$this->AddSettingsField(
			$section,
			'xo_access_control_allowed_hosts',
			__('Allowed Hosts', 'xo'),
			function ($option, $states, $value) {
				return $this->GenerateTextareaField(
					$option, $states, $value,
					sprintf(
						__('Allowed hosts using access control allow origin when the Access Control Mode is set to List, each on a new line.', 'xo'),
						$this->Xo->name
					)
				);
			},
			function ($oldValue, $newValue, $option) {
				if ($oldValue !== $newValue)
					flush_rewrite_rules();
			}
		);
	}

	/**
	 * Render the rewrites need updating notice when registered.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings Additional data passed to the notice render.
	 * @return string Output of the admin notice
	 */
	public function RewritesNeedUpdatingNoticeRender($settings) {
		$output = '<p><strong>' . sprintf(
			__('%s API settings updated, please %s now.', 'xo'),
			$this->Xo->name,
			sprintf(
				'<a href="' . $this->SettingsPage->GetTabUrl()
					. '&action=update-rewrites' . '">%s</a>',
				__('update rewrites', 'xo')
			)
		) . '</strong></p>';

		return $output;
	}

	/**
	 * Handle additional actions on the API tab.
	 * 
	 * @since 1.0.0
	 * 
	 * @return void
	 */
	private function DoAction() {
		if (empty($_GET['action']))
			return;

		switch ($_GET['action']) {
			case 'update-rewrites':
				flush_rewrite_rules();
				break;
		}
	}
}