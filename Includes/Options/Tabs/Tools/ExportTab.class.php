<?php

/**
 * Tab adding export data from Xo to the Xo Tools screen.
 *
 * @since 1.0.0
 */
class XoOptionsTabExport extends XoOptionsAbstractFieldsTab
{
	/**
	 * Add the various sections for the Exports tab.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function Render() {
		echo '<div class="xo-form">';

		$this->AddExportSettingsSection();
		$this->AddExportAppConfigSection();

		echo '</div>';
	}

	protected function AddExportSettingsSection() {
		$this->GenerateSection(
			__('Settings', 'xo'),
			__('Settings which override options within Xo.', 'xo')
		);

		$this->GenerateTable(function () {
			$this->GenerateFieldRow(
				'xo_settings_export',
				__('XO_SETTINGS Export', 'xo'),
				function ($name) {
					$settings = array(
						'overrides' => $this->Xo->Services->Options->GetCurrentSettings()
					);

					$value = var_export($settings, true);

					$value = "define('XO_SETTINGS', json_encode(" . $value . "));";

					$this->GenerateTextareaField(
						$name, array(), $value,
						sprintf(
							__('Copy the output below to your wp-config.php or functions.php file to fully override all %s settings.', 'xo'),
							$this->Xo->name
						),
						20
					);
				}
			);
		});
	}

	protected function AddExportAppConfigSection() {
		$this->GenerateSection(
			__('Config', 'xo'),
			__('Settings used to configure your Angular app.', 'xo')
		);

		$this->GenerateTable(function () {
			$this->GenerateFieldRow(
				'xo_config_export',
				__('AppConfig Export', 'xo'),
				function ($name) {
					$XoApiConfigController = new XoApiControllerConfig($this->Xo);
					$config = $XoApiConfigController->Get();

					$value = json_encode($config->config, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

					$value = 'var appConfig = ' . $value . ';';

					$this->GenerateTextareaField(
						$name, array(), $value,
						sprintf(
							__('Use the below output to set or manually modify the Xo appConfig global in your Angular app.', 'xo'),
							$this->Xo->name
						),
						20
					);
				}
			);
		});
	}
}