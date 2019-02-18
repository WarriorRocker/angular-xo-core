<?php

/**
 * Tab adding various tools to the Xo Tools screen.
 *
 * @since 1.0.0
 */
class XoOptionsTabTools extends XoOptionsAbstractTab
{
	public function Init() {
		$this->DoAction();
	}

	public function Render() {
		$this->AddGeneralSection();
		$this->AddGeneralSectionRebuildTemplateCache();
		$this->AddGeneralSectionResetDefaults();
	}

	function AddGeneralSection() {
		echo '<h3>' . __('Editor Options', 'xo') . '</h3>' .
			'<p>' . __('Some Tools.', 'xo') . '</p>';
	}

	function AddGeneralSectionRebuildTemplateCache() {
		$output = '<p><a href="' . $this->SettingsPage->GetTabUrl() .
			'&action=rebuild-template-cache" class="button-primary">' .
			__('Rebuild Templates Cache', 'xo') .
			'</a></p>';

		if (!$this->Xo->Services->Options->GetOption('xo_templates_cache_enabled', false))
			$output .= '<p>' . __('Usage of the templates cache is currently disabled.') . '</p>';

		echo $output;
	}

	function AddGeneralSectionResetDefaults() {
		$output = '<p><a href="' . $this->SettingsPage->GetTabUrl() .
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
		}
	}
}