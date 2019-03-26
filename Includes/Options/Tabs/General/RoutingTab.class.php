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

	/**
	 * Settings section for configuring page drafts and previews.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function InitPreviewsSection() {
		$this->AddSettingsSection(
			'routing_previews_section',
			__('Previews Rewrites', 'xo'),
			__('Allows Xo to integrate with viewing drafts and unsaved page changes.', 'xo'),
			function ($section) {
				$this->AddPreviewsSectionPreviewsEnabledSetting($section);
			}
		);
	}

	/**
	 * Settings field for Previews Enabled.
	 * Used when generating routes to include drafts and previews.
	 *
	 * @since 1.0.0
	 *
	 * @param string $section Name of the section.
	 * @return void
	 */
	protected function AddPreviewsSectionPreviewsEnabledSetting($section) {
		$this->AddSettingsField(
			$section,
			'xo_routing_previews_enabled',
			__('Previews Enabled', 'xo'),
			function ($option, $states, $value) {
				return $this->GenerateInputCheckboxField(
					$option, $states, $value,
					__('Add post and page preview links to dynamic routes when logged in.', 'xo')
				);
			}
		);
	}

	/**
	 * Settings section for configuring how to handle various routing errors.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function InitErrorsSection() {
		$this->AddSettingsSection(
			'routing_errors_section',
			__('Routing Errors', 'xo'),
			__('Used to set the page to display for various routing errors.', 'xo'),
			function ($section) {
				$this->AddErrorsSection404PageSetting($section);
			}
		);
	}

	/**
	 * Settings field for 404 Page.
	 * Used when generating routes to indicate a page which is shown for routes which have no match.
	 *
	 * @since 1.0.0
	 *
	 * @param string $section Name of the section.
	 * @return void
	 */
	protected function AddErrorsSection404PageSetting($section) {
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