<?php

/**
 * Tab adding options related to routing to the Xo General Settings screen.
 *
 * @since 1.0.0
 */
class XoOptionsTabRouting extends XoOptionsAbstractSettingsTab
{
	/**
	 * Add the various settings sections for the Routing tab.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	function Init() {
		$this->InitPreviewsSection();
		$this->InitErrorsSection();
	}

	function InitPreviewsSection() {
		$this->AddSettingsSection(
			'routing_previews_section',
			__('Previews Rewrites', 'xo'),
			__('Allows Xo to integrate with viewing drafts and unsaved page changes.', 'xo'),
			function ($section) {
				$this->AddPreviewsSectionPreviewsEnabledSetting($section);
			}
		);
	}

	function AddPreviewsSectionPreviewsEnabledSetting($section) {
		$this->AddSettingsField(
			$section,
			'xo_routing_previews_enabled',
			__('Previews Enabled', 'xo'),
			function ($option, $states, $value) {
				return $this->GenerateInputCheckboxField(
					$option, $states, $value,
					__('This will add a filter on preview_post_link and generate the appropriate routes.', 'xo')
				);
			}
		);
	}

	function InitErrorsSection() {
		$this->AddSettingsSection(
			'routing_errors_section',
			__('Routing Errors', 'xo'),
			__('Used to set the page to display for various routing errors.', 'xo'),
			function ($section) {
				$this->AddErrorsSection404PageSetting($section);
			}
		);
	}

	function AddErrorsSection404PageSetting($section) {
		$this->AddSettingsField(
			$section,
			'xo_404_page_id',
			__('404 Page', 'xo'),
			function ($option, $states, $value) {
				wp_dropdown_pages(
					array(
						'name' => $option,
						'show_option_none' => __('&mdash; Front Page &mdash;', 'xo'),
						'option_none_value' => '0',
						'selected' => $value
					)
				);
			}
		);
	}
}