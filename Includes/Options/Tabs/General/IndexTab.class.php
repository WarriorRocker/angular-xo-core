<?php

/**
 * Tab adding options related to Angular indexes to the Xo General Settings screen.
 *
 * @since 1.0.0
 */
class XoOptionsTabIndex extends XoOptionsAbstractSettingsTab
{
	/**
	 * Add the various settings sections for the Index tab.
	 * 
	 * @since 1.0.0
	 * 
	 * @return void
	 */
	function Init() {
		$this->InitGeneralSection();
		$this->InitLiveIndexSection();
	}

	/**
	 * Settings section for configuring Src and Dist Index and Redirect Mode fields.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function InitGeneralSection() {
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

	/**
	 * Settings field for Src Index.
	 * Used when reading or modifying the Angular App's src index.
	 *
	 * @since 1.0.0
	 *
	 * @param string $section Name of the section.
	 * @return void
	 */
	private function AddGeneralSectionSrcIndexSetting($section) {
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

	/**
	 * Settings field for Dist Index.
	 * Used when reading or modifying the Angular App's dist index.
	 *
	 * @since 1.0.0
	 *
	 * @param string $section Name of the section.
	 * @return void
	 */
	private function AddGeneralSectionDistIndexSetting($section) {
		$this->AddSettingsField(
			$section,
			'xo_index_dist',
			__('Dist Index', 'xo'),
			function ($option, $states, $value) {
				return $this->GenerateInputTextField(
					$option, $states, $value,
					__('The full path to the dist index relative to the active template folder.', 'xo')
				);
			}
		);
	}

	/**
	 * Settings field for Redirect Mode.
	 * Used to determine the way the Angular App's index should be loaded.
	 *
	 * @since 1.0.0
	 *
	 * @param string $section Name of the section.
	 * @return void
	 */
	private function AddGeneralSectionRedirectModeSetting($section) {
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

	/**
	 * Settings section for configuring the output of the Live Redirect Mode.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function InitLiveIndexSection() {
		$this->AddSettingsSection(
			'index_live_index_section',
			__('Live Index', 'xo'),
			__('Manage the output when using the Live Redirect Mode.', 'xo'),
			function ($section) {
				$this->AddLiveIndexHeaderSection($section);
				$this->AddLiveIndexFooterSection($section);
				$this->AddLiveIndexConfigSection($section);
			}
		);
	}

	/**
	 * Settings field for Header in Live Index.
	 * Used to optionally include wp_head() in the index output.
	 *
	 * @since 1.0.0
	 *
	 * @param string $section Name of the section.
	 * @return void
	 */
	private function AddLiveIndexHeaderSection($section) {
		$this->AddSettingsField(
			$section,
			'xo_index_live_header',
			__('Header', 'xo'),
			function ($option, $states, $value) {
				return $this->GenerateInputCheckboxField(
					$option, $states, $value,
					__('Render and add wp_head() before the closing HEAD tag.', 'xo')
				);
			}
		);
	}

	/**
	 * Settings field for Footer in Live Index.
	 * Used to optionally include wp_footer() in the index output.
	 *
	 * @since 1.0.0
	 *
	 * @param string $section Name of the section.
	 * @return void
	 */
	private function AddLiveIndexFooterSection($section) {
		$this->AddSettingsField(
			$section,
			'xo_index_live_footer',
			__('Footer', 'xo'),
			function ($option, $states, $value) {
				return $this->GenerateInputCheckboxField(
					$option, $states, $value,
					__('Render and add wp_footer() before the closing BODY tag.', 'xo')
				);
			}
		);
	}

	/**
	 * Settings field for App Config in Live Index.
	 * Used to optionally include the App Config in the index output.
	 *
	 * @since 1.0.0
	 *
	 * @param string $section Name of the section.
	 * @return void
	 */
	private function AddLiveIndexConfigSection($section) {
		$this->AddSettingsField(
			$section,
			'xo_index_live_config',
			__('App Config', 'xo'),
			function ($option, $states, $value) {
				return $this->GenerateInputCheckboxField(
					$option, $states, $value,
					__('Generate the App Config and add within the App Config Entrypoint if found.', 'xo')
				);
			}
		);
	}
}