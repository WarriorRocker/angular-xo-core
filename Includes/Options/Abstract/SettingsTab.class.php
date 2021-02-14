<?php

/**
 * An abstract class extending tab used to construct a WordPress options page.
 * 
 * @since 1.0.0
 */
class XoOptionsAbstractSettingsTab extends XoOptionsAbstractFieldsTab
{
	public function Render() {
		settings_errors();

		echo '<form class="xo-form" method="post" action="options.php">';

		settings_fields($this->tabPageSlug);
		do_settings_sections($this->tabPageSlug);
		submit_button();

		echo '</form>';
	}

	public function AddSettingsSection($section, $title, $description, Callable $callback) {
		add_settings_section(
			$section,
			$title,
			function () use ($description) {
				if ($description)
					echo '<p>' . $description . '</p>';
			},
			$this->tabPageSlug
		);

		if (is_callable($callback))
			call_user_func($callback, $section);
	}

	public function AddSettingsField($section, $option, $title, Callable $callback, Callable $update = NULL) {
		$value = $this->Xo->Services->Options->GetOption($option, false);
		$states = $this->Xo->Services->Options->GetStates($option);

		register_setting($this->tabPageSlug, $option);

		add_settings_field(
			$option,
			'<label for="' . $option . '">' . $title . '</label>',
			function () use ($option, $states, $value, $callback) {
				if (is_callable($callback))
					call_user_func($callback, $option, $states, $value);
			},
			$this->tabPageSlug,
			$section
		);

		if (is_callable($update))
			add_action('update_option_' . $option, $update, 10, 3);
	}
}