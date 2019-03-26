<?php

/**
 * Tab adding various tools to the Xo Tools screen.
 *
 * @since 1.0.0
 */
class XoOptionsTabTools extends XoOptionsAbstractFieldsTab
{
	/**
	 * Add the various sections for the Tools tab.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function Init() {
		$this->DoAction();
	}

	public function Render() {
		echo '<div class="xo-form">';

		$this->AddGeneralSection();
		$this->AddGeneralSectionAppConfigEntrypoint();
		$this->AddGeneralSectionRebuildTemplateCache();
		$this->AddGeneralSectionResetDefaults();

		echo '</div>';
	}

	function AddGeneralSection() {
		$this->GenerateSection(
			__('Tools and Actions', 'xo'),
			__('Additional tools and manual actions.', 'xo')
		);
	}

	function AddGeneralSectionAppConfigEntrypoint() {
		$output = '<p><a href="' . $this->tabPageUrl .
			'&action=add-entrypoint" class="button-primary">' .
			__('Add App Config Entrypoint', 'xo') .
			'</a></p>';

		echo $output;
	}

	function AddGeneralSectionRebuildTemplateCache() {
		$output = '<p><a href="' . $this->tabPageUrl .
			'&action=rebuild-template-cache" class="button-primary">' .
			__('Rebuild Templates Cache', 'xo') .
			'</a></p>';

		if (!$this->Xo->Services->Options->GetOption('xo_templates_cache_enabled', false))
			$output .= '<p>' . __('Usage of the templates cache is currently disabled.') . '</p>';

		echo $output;
	}

	function AddGeneralSectionResetDefaults() {
		$output = '<p><a href="' . $this->tabPageUrl .
		'&action=reset-defaults" class="button-primary">' .
		__('Reset Defaults', 'xo') .
		'</a></p>';

		echo $output;
	}

	function DoAction() {
		if (empty($_GET['action']))
			return;

		switch ($_GET['action']) {
			case 'rebuild-template-cache':
				$this->Xo->Services->TemplateReader->GetTemplates(true, false);
				break;

			case 'reset-defaults':
				$this->Xo->Services->Options->ResetDefaults();
				break;

			case 'add-entrypoint':
				$this->Xo->Services->IndexBuilder->AddAppConfigEntrypoint();
				break;
		}
	}
}