<?php

/**
 * Tab adding options related to Angular indexes to the Xo General Settings screen.
 *
 * @since 1.0.0
 */
class XoOptionsTabIndex extends XoOptionsAbstractSettingsTab
{
	function Init() {
		$this->InitGeneralSection();
	}

	function InitGeneralSection() {
		$this->AddSettingsSection(
			'index_general_section',
			__('General', 'xo'),
			__('Manage general index options.', 'xo'),
			function ($section) {
				$this->AddGeneralSectionSrcIndexSetting($section);
				$this->AddGeneralSectionDistIndexSetting($section);
				$this->AddGeneralSectionRedirectEnabledSetting($section);
			}
		);
	}

	function AddGeneralSectionSrcIndexSetting($section) {
		$this->AddSettingsField(
			$section,
			'xo_index_src',
			__('Src Index', 'xo'),
			function ($option, $states, $value) {
				return $this->GenerateInputTextField(
					$option, $states, $value,
					__('The full path to the src index relative to get_template_directory.', 'xo')
				);
			}
		);
	}

	function AddGeneralSectionDistIndexSetting($section) {
		$this->AddSettingsField(
			$section,
			'xo_index_dist',
			__('Dist Index', 'xo'),
			function ($option, $states, $value) {
				return $this->GenerateInputTextField(
					$option, $states, $value,
					__('The full path to the dist index relative to get_template_directory.', 'xo')
				);
			}
		);
	}

	function AddGeneralSectionRedirectEnabledSetting($section) {
		$this->AddSettingsField(
			$section,
			'xo_index_redirect_enabled',
			__('Redirect Enabled', 'xo'),
			function ($option, $states, $value) {
				return $this->GenerateInputCheckboxField(
					$option, $states, $value,
					__('This will redirect all front-end requests to the dist index.', 'xo')
				);
			},
			function ($oldValue, $newValue, $option) {
				if ($oldValue !== $newValue)
					flush_rewrite_rules();
			}
		);
	}
}