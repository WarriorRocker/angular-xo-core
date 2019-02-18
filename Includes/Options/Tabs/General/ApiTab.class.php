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