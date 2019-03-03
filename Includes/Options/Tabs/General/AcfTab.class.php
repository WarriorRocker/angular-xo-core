<?php

/**
 * Tab adding options related to ACF to the Xo General Settings screen.
 *
 * @since 1.0.0
 */
class XoOptionsTabAcf extends XoOptionsAbstractSettingsTab
{
	/**
	 * Add the various settings sections for the ACF tab.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	function Init() {
		$this->InitAllowedSection();
	}

	/**
	 * Settings section for configuring allowed ACF field groups.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function InitAllowedSection() {
		$this->AddSettingsSection(
			'acf_allowed_section',
			__('Allowed Options', 'xo'),
			__('Used to set which option groups are allowed through the Xo API.', 'xo'),
			function ($section) {
				$this->AddAllowedSectionAllowedGroupsSetting($section);
			}
		);
	}

	/**
	 * Settings field for Allowed Groups.
	 * Used to allow specified ACF groups through the Xo API.
	 *
	 * @since 1.0.0
	 *
	 * @param string $section Name of the section.
	 * @return void
	 */
	private function AddAllowedSectionAllowedGroupsSetting($section) {
		$option = 'xo_acf_allowed_groups';
		$value = $this->Xo->Services->Options->GetOption($option, array());
		$states = $this->Xo->Services->Options->GetStates($option);

		register_setting($this->tabPageSlug, $option);

		$groups = acf_get_field_groups();

		foreach ($groups as $group) {
			$isOptionsPageGroup = false;

			foreach ($group['location'] as $locationGroup) {
				foreach ($locationGroup as $location) {
					if ($location['param'] == 'options_page') {
						$isOptionsPageGroup = true;
						break;
					}
				}
			}

			if (!$isOptionsPageGroup)
				continue;

			$fieldOption = $option . '_' . $group['key'];
			$fieldName = $option . '[' . $group['key'] . ']';

			$compare = ((!empty($value[$group['key']])) && ($value[$group['key']] == 1));

			add_settings_field(
				$fieldOption,
				'<label for="' . $fieldName . '">' . (($group['title']) ? $group['title'] : $group['key']) . '</label>',
				function () use ($fieldName, $states, $compare) {
					return $this->GenerateInputCheckboxField($fieldName, $states, $compare);
				},
				$this->tabPageSlug,
				$section
			);
		}
	}
}