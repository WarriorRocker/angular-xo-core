<?php

class XoOptionsTabMiddleware extends XoOptionsAbstractSettingsTab
{
	function Init() {
		add_filter('pre_update_option_xo_prerender_user_agents', array($this, 'PreUpdateOptionUserAgents'), 10, 2);

		$this->InitGeneralSection();
	}

	function InitGeneralSection() {
		$this->AddSettingsSection(
			'middleware_general_section',
			__('Middleware Settings', 'xo'),
			__('General settings related to the prerender middleware.', 'xo'),
			function ($section) {
				$this->AddGeneralSectionMiddlewareEnabledSetting($section);
				$this->AddGeneralSectionAccessTokenSetting($section);
				$this->AddGeneralSectionServiceEndpointSetting($section);
				$this->AddGeneralSectionUserAgentsSetting($section);
			}
		);
	}

	function AddGeneralSectionMiddlewareEnabledSetting($section) {
		$this->AddSettingsField(
			$section,
			'xo_prerender_middleware_enabled',
			__('Middleware Enabled', 'xo'),
			function ($option, $states, $value) {
				return $this->GenerateInputCheckboxField(
					$option, $states, $value,
					__('Enable the prerender middleware in your .htaccess file.', 'xo')
				);
			}
		);
	}

	function AddGeneralSectionAccessTokenSetting($section) {
		$this->AddSettingsField(
			$section,
			'xo_prerender_token',
			__('Access Token', 'xo'),
			function ($option, $states, $value) {
				return $this->GenerateInputTextField(
					$option, $states, $value,
					__('The access token used with the prerender middleware.', 'xo')
				);
			}
		);
	}

	function AddGeneralSectionServiceEndpointSetting($section) {
		$this->AddSettingsField(
			$section,
			'xo_prerender_service_endpoint',
			__('Service Endpoint', 'xo'),
			function ($option, $states, $value) {
				return $this->GenerateInputTextField(
					$option, $states, $value,
					__('The endpoint through which middleware requests will be served.', 'xo')
				);
			}
		);
	}

	function AddGeneralSectionUserAgentsSetting($section) {
		$this->AddSettingsField(
			$section,
			'xo_prerender_user_agents',
			__('User Agents', 'xo'),
			function ($option, $states, $value) {
				if (is_array($value))
					$value = implode(', ', $value);

				return $this->GenerateTextareaField(
					$option, $states, $value,
					__('A comma-separated list of user agents for which the middleware will respond.', 'xo'),
					5
				);
			}
		);
	}

	function PreUpdateOptionUserAgents($new_value, $old_value) {
		$value = array_map('trim', explode(',', $new_value));

		return $value;
	}
}