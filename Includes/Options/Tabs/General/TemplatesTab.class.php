<?php

/**
 * Tab adding options related to templates to the Xo General Settings screen.
 *
 * @since 1.0.0
 */
class XoOptionsTabTemplates extends XoOptionsAbstractSettingsTab
{
	function Init() {
		$this->InitReaderSection();
	}

	function InitReaderSection() {
		$this->AddSettingsSection(
			'templates_reader_section',
			__('Templates Reader', 'xo'),
			__('Used to set options for the Angular template reader.', 'xo'),
			function ($section) {
				$this->AddReaderSectionCacheEnabledSetting($section);
				$this->AddReaderSectionTemplatesPathSetting($section);
			}
		);
	}

	function AddReaderSectionCacheEnabledSetting($section) {
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

	function AddReaderSectionTemplatesPathSetting($section) {
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