<?php

/**
 * Tab adding options related to templates to the Xo General Settings screen.
 *
 * @since 1.0.0
 */
class XoOptionsTabTemplates extends XoOptionsAbstractSettingsTab
{
	/**
	 * Add the various settings sections for the Templates tab.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	function Init() {
		$this->InitReaderSection();
	}

	/**
	 * Settings section for configuring various options for the Template Reader.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function InitReaderSection() {
		$this->AddSettingsSection(
			'templates_reader_section',
			__('Templates Reader', 'xo'),
			__('Used to set options for the Angular template reader.', 'xo'),
			function ($section) {
				$this->AddReaderSectionReaderEnabledSetting($section);
				$this->AddReaderSectionCacheEnabledSetting($section);
				$this->AddReaderSectionTemplatesPathSetting($section);
			}
		);
	}

	/**
	 * Settings field for Reader Enabled.
	 * Used to enable the Template Reader.
	 *
	 * @since 1.0.0
	 *
	 * @param string $section Name of the section.
	 * @return void
	 */
	private function AddReaderSectionReaderEnabledSetting($section) {
		$this->AddSettingsField(
			$section,
			'xo_templates_reader_enabled',
			__('Reader Enabled', 'xo'),
			function ($option, $states, $value) {
				return $this->GenerateInputCheckboxField(
					$option, $states, $value,
					__('Set to enable the template reader.', 'xo')
				);
			}
		);
	}

	/**
	 * Settings field for Cache Enabled.
	 * Used to enable the caching of templates from the Template Reader.
	 *
	 * @since 1.0.0
	 *
	 * @param string $section Name of the section.
	 * @return void
	 */
	private function AddReaderSectionCacheEnabledSetting($section) {
		$this->AddSettingsField(
			$section,
			'xo_templates_cache_enabled',
			__('Cache Enabled', 'xo'),
			function ($option, $states, $value) {
				return $this->GenerateInputCheckboxField(
					$option, $states, $value,
					__('Set to enable caching of the annotated templates.', 'xo')
				);
			},
			function ($oldValue, $newValue, $option) {
				if ($newValue)
					$this->Xo->Services->TemplateReader->GetTemplates(true, false);
			}
		);
	}

	/**
	 * Settings field for Templates Path.
	 * Used to set the base path for reading templates.
	 *
	 * @since 1.0.0
	 *
	 * @param string $section Name of the section.
	 * @return void
	 */
	private function AddReaderSectionTemplatesPathSetting($section) {
		$this->AddSettingsField(
			$section,
			'xo_templates_path',
			__('Templates Path', 'xo'),
			function ($option, $states, $value) {
				return $this->GenerateInputTextField(
					$option, $states, $value,
					__('Relative to the current template directory.', 'xo')
				);
			}
		);
	}
}