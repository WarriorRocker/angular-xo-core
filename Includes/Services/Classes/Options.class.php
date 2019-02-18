<?php

/**
 * Service class providing a repository of defaults and abstraction for option requests used throughout Xo.
 *
 * @since 1.0.0
 */
class XoServiceOptions
{
	/**
	 * @var Xo
	 */
	var $Xo;

	/**
	 * Collection of options which override defaults or database configurations.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	var $overrides = array();

	function __construct(Xo $Xo) {
		$this->Xo = $Xo;

		add_filter('xo/options/states', array($this, 'GetStates'), 10, 2);

		if (defined('XO_SETTINGS')) {
			$settings = json_decode(XO_SETTINGS, true);
			if (!empty($settings['overrides']))
				$this->overrides = $settings['overrides'];
		}
	}

	/**
	 * Get an option value using get_option filtered by xo/options/get/{{option_name}}.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name Name of the option to get.
	 * @param mixed $value Default value if the option was not found.
	 * @return mixed Return value of the option.
	 */
	function GetOption($name, $value = false) {
		$value = get_option($name, $value);

		$value = apply_filters('xo/options/get/' . $name, $value);

		return $value;
	}

	/**
	 * Set an option using update_option filtered by xo/options/set/{{option_name}}.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name Name of the option to set.
	 * @param mixed $value Value to set for the given option.
	 * @return bool Whether the option was updated.
	 */
	function SetOption($name, $value = false) {
		$value = apply_filters('xo/options/set/' . $name, $value);

		return update_option($name, $value);
	}

	/**
	 * Get the default settings for Xo.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	function GetDefaultSettings() {
		$defaults = array(
			// Index Tab
			'xo_index_src' => '/src/index.html',
			'xo_index_dist' => '/dist/index.html',
			'xo_index_redirect_mode' => 'default',

			// Api Tab
			'xo_api_enabled' => true,
			'xo_api_endpoint' => '/xo-api',

			// Routing Tab
			'xo_routing_previews_enabled' => true,
			'xo_404_page_id' => 0,

			// Templates Tab
			'xo_templates_cache_enabled' => true,
			'xo_templates_path' => '/src',

			// ACF Tab
			'xo_acf_allowed_groups' => array()
		);

		return $defaults;
	}

	/**
	 * Get the defaults for Xo filtered by xo/options/defaults.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed The defaults filtered by xo/options/defaults.
	 */
	function GetDefaults() {
		$defaults = $this->GetDefaultSettings();

		if ($config = $this->GetOptionsFromJson())
			$defaults = array_merge($defaults, $config);

		$defaults = apply_filters('xo/options/defaults', $defaults);

		return $defaults;
	}

	/**
	 * Set the default options for Xo based on the current internal defaults.
	 *
	 * @since 1.0.0
	 *
	 * @return bool Whether any options were set.
	 */
	function SetDefaults() {
		$defaults = $this->GetDefaults();

		$setDefaults = false;
		foreach ($defaults as $option => $value)
			if (add_option($option, $value, '', true))
				$setDefaults = true;

		return $setDefaults;
	}

	/**
	 * Reset all options for Xo based on the current internal defaults.
	 *
	 * @since 1.0.0
	 *
	 * @return bool Whether any options were set.
	 */
	function ResetDefaults() {
		$defaults = $this->GetDefaults();

		$setOptions = false;
		foreach ($defaults as $option => $value)
			if (update_option($option, $value, true))
				$setOptions = true;

		return $setOptions;
	}

	/**
	 * Get the states of a given option filtered by xo/options/states/{{option_name}}.
	 *
	 * @since 1.0.0
	 *
	 * @param string $option Name of the option.
	 * @return array States of the given option.
	 */
	function GetStates($option) {
		$states = array();

		if (isset($this->overrides[$option]))
			array_push($states, 'override');

		$states = apply_filters('xo/options/states/' . $option, $states);

		return $states;
	}

	/**
	 * Get options which may override the defaults by reading the angular.json file.
	 *
	 * @since 1.0.0
	 *
	 * @return bool|string[]
	 */
	function GetOptionsFromJson() {
		if (!$jsons = $this->Xo->Services->AngularJson->ParseConfig())
			return false;

		foreach ($jsons as $json) {
			$config = array();

			if (!empty($json['index'])) {
				$config['xo_index_src'] = '/' . ltrim($json['index']);

				if ((!empty($json['sourceRoot'])) && (!empty($json['outputPath']))) {
					$pos = strpos($json['index'], $json['sourceRoot']);
					if ($pos === 0)
						$config['xo_index_dist'] = '/' . ltrim($json['outputPath'] . substr($json['index'], strlen($json['sourceRoot'])));
				}
			}

			if (!empty($json['sourceRoot']))
				$config['xo_templates_path'] = '/' . ltrim($json['sourceRoot'], '/');

			if ($config)
				return $config;
		}

		return false;
	}
}