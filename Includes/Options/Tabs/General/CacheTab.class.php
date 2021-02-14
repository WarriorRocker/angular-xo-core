<?php

class XoOptionsTabCache extends XoOptionsAbstractSettingsTab
{
	function Init() {
		$this->InitMenusSection();

		if (class_exists('ACF'))
			$this->InitAcfSection();
	}

	function InitMenusSection() {
		$this->AddSettingsSection(
			'index_menus_section',
			__('Menus', 'xo'),
			__('Manage the addition of Nav Menus into the index.', 'xo'),
			function ($section) {
				$this->AddMenusSectionMenuItemsSetting($section);
			}
		);
	}

	function AddMenusSectionMenuItemsSetting($section) {
		$option = 'xo_index_menu_items';
		$value = $this->Xo->Services->Options->GetOption($option, array());
		$states = $this->Xo->Services->Options->GetStates($option);

		register_setting($this->tabPageSlug, $option);

		$menus = get_registered_nav_menus();

		foreach ($menus as $location => $name) {
			$fieldOption = $option . '_' . $location;
			$fieldName = $option . '[' . $location . ']';

			$compare = ((!empty($value[$location])) && ($value[$location] == 1));

			add_settings_field(
				$fieldOption,
				'<label for="' . $fieldName . '">' . $name . '</label>',
				function () use ($fieldName, $states, $compare) {
					return $this->GenerateInputCheckboxField($fieldName, $states, $compare);
				},
				$this->tabPageSlug,
				$section
			);
		}
	}

	function InitAcfSection() {
		$this->AddSettingsSection(
			'index_acf_section',
			__('ACF', 'xo'),
			__('Manage the addition of ACF groups into the index.', 'xo'),
			function ($section) {
				$this->AddAcfSectionAcfGroupsSetting($section);
			}
		);
	}

	function AddAcfSectionAcfGroupsSetting($section) {
		$option = 'xo_index_acf_groups';
		$value = $this->Xo->Services->Options->GetOption($option, array());
		$states = $this->Xo->Services->Options->GetStates($option);

		register_setting($this->tabPageSlug, $option);

		$groups = array();
		if (function_exists('acf_get_field_groups'))
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