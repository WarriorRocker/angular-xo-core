<?php

/**
 * Tab adding export data from Xo to the Xo Tools screen.
 *
 * @since 1.0.0
 */
class XoOptionsTabExport extends XoOptionsAbstractFieldsTab
{
	public function Render() {
		$this->AddGeneralSection();
	}

	function AddGeneralSection() {
		$this->GenerateSection(
			__('General', 'xo'),
			__('General export data.', 'xo')
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
						$name,
						array(),
						$value,
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
}