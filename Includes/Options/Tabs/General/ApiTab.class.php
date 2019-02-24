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
	var $RewritesNeedUpdatingNotice;

	function Init() {
		$this->RewritesNeedUpdatingNotice = new XoServiceAdminNotice(
			'angular-xo-rewrites-need-update-notice',
			array($this, 'RewritesNeedUpdatingNoticeRender')
		);

		$this->InitGeneralSection();
		$this->InitAccessControlSection();

		$this->DoAction();
	}

	function InitGeneralSection() {
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

	function AddGeneralSectionApiEnabledSetting($section) {
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
				$this->UpdatedApiSetting($oldValue, $newValue, $option);
			}
		);
	}

	function AddGeneralSectionApiEndpointSetting($section) {
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
				$this->UpdatedApiSetting($oldValue, $newValue, $option);
			}
		);
	}

	function InitAccessControlSection() {
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

	function AddAccessControlSectionModeSetting($section) {
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

	function AddAccessControlSectionAllowedHostsSetting($section) {
		$this->AddSettingsField(
			$section,
			'xo_access_control_allowed_hosts',
			__('API Enabled', 'xo'),
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

	function UpdatedApiSetting($oldValue, $newValue, $option) {
		if ($oldValue !== $newValue) {
			$this->RewritesNeedUpdatingNotice->RegisterNotice();
		}
	}

	function RewritesNeedUpdatingNoticeRender($settings) {
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

	function DoAction() {
		if (empty($_GET['action']))
			return;

		switch ($_GET['action']) {
			case 'update-rewrites':
				flush_rewrite_rules();
				break;
		}
	}
}