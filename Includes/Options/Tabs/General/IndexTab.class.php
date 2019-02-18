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
				$this->AddGeneralSectionRedirectModeSetting($section);
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
					__('The full path to the src index relative to the active template folder.', 'xo')
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
					__('The full path to the dist index relative to active template folder.', 'xo')
				);
			}
		);
	}

	function AddGeneralSectionRedirectModeSetting($section) {
		$this->AddSettingsField(
			$section,
			'xo_index_redirect_mode',
			__('Redirect Mode', 'xo'),
			function ($option, $states, $value) {
				$choices = array(
					'default' => __('Default', 'xo'),
					'live' => __('Live', 'xo'),
					'offline' => __('Offline', 'xo')
				);

				$descriptions = array(
					__('Configures the way the main template index will be loaded.', 'xo'),
					sprintf(
						__('<strong>%s</strong> - %s', 'xo'),
						__('Default', 'xo'),
						__('Allow WordPress to load the active theme normally.', 'xo')
					),
					sprintf(
						__('<strong>%s</strong> - %s', 'xo'),
						__('Live', 'xo'),
						__('Attempt to parse the Dist Index and redirect template requests if successful.', 'xo')
					),
					sprintf(
						__('<strong>%s</strong> - %s', 'xo'),
						__('Offline', 'xo'),
						__('Advanced mode which redirects all front-end requests directly to the Dist Index.', 'xo')
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
}