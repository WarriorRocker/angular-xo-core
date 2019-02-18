<?php

/**
 * Tab adding options related to the Xo API to the Xo General Settings screen.
 *
 * @since 1.0.0
 */
class XoOptionsTabApi extends XoOptionsAbstractSettingsTab
{
	function Init() {
		$this->InitGeneralSection();
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
			}
		);
	}
}