<?php

class XoOptionsTabPrerender extends XoOptionsAbstractFieldsTab
{
	/**
	 * @var XoServiceAdminNotice
	 */
	var $PrerenderLoginNotice;

	public function Init() {
		$this->PrerenderLoginNotice = new XoServiceAdminNotice(
			'angular-xo-prerender-login-notice',
			array($this, 'RenderPrerenderLoginNotice')
		);

		$this->DoAction();
	}

	public function Render() {
		$session = $this->Xo->Services->Options->GetOption('xo_prerender_session', array());
		$user = $session ? $this->Xo->Services->Prerender->GetUser($session) : false;

		if ($user) {
			$this->AddPrerenderInfoSection($user);
			$this->AddRemovePrerenderSection();
		} else {
			$this->AddPrerenderLoginSection();
		}
	}

	function AddPrerenderLoginSection() {
		$this->GenerateForm(
			'POST',
			$this->tabPageUrl,
			array(
				'action' => 'prerender-login'
			),
			function () {
				$this->GenerateSection(
					__('Prerender Login', 'xo'),
					__('Login to Prerender below. Only the authorization token will be stored.', 'xo')
				);

				$this->GenerateTable(function () {
					$this->GenerateFieldRow(
						'username',
						__('Username', 'xo'),
						function ($name) {
							$this->GenerateInputTextField($name);
						}
					);

					$this->GenerateFieldRow(
						'password',
						__('Password', 'xo'),
						function ($name) {
							$this->GenerateInputTextField($name);
						}
					);
				});

				submit_button(__('Login to Prerender', 'xo'));
			}
		);
	}

	function AddPrerenderInfoSection($user) {
		$this->GenerateSection(
			__('Prerender Account', 'xo'),
			__('Information related to your Prerender account.', 'xo')
		);

		if (isset($user['planName'])) {
			$this->GenerateInfoField(
				__('Plan Name', 'xo'),
				$user['planName']
			);
		}

		if (isset($user['trackingCodeInstalled'])) {
			$this->GenerateInfoField(
				__('Token Detected', 'xo'),
				($user['trackingCodeInstalled'] ? __('Yes', 'xo') : __('No', 'xo'))
			);
		}

		if (isset($user['numPagesCached'])) {
			$this->GenerateInfoField(
				__('Cached Pages', 'xo'),
				$user['numPagesCached']
			);
		}

		if (isset($user['cacheFreshness'])) {
			$this->GenerateInfoField(
				__('Cache Freshness', 'xo'),
				sprintf(__('%s days', 'xo'), $user['cacheFreshness'])
			);
		}
	}

	function AddRemovePrerenderSection() {
		$this->GenerateForm(
			'POST',
			$this->tabPageUrl,
			array(
				'action' => 'prerender-remove'
			),
			function () {
				$this->GenerateSection(
					__('Prerender Login', 'xo'),
					__('You are currently logged into Prerender.io.', 'xo')
				);

				submit_button(__('Remove Prerender', 'xo'));
			}
		);
	}

	function DoAction() {
		if (empty($_POST['action']))
			return;

		switch ($_POST['action']) {
			case 'prerender-login':
				$this->LoginPrerenderAndStoreSession();
				break;

			case 'prerender-remove':
				$this->RemovePrerender();
				break;
		}
	}

	function LoginPrerenderAndStoreSession() {
		$session = $this->Xo->Services->Prerender->Login($_POST['username'], $_POST['password']);

		if ($session) {
			$this->Xo->Services->Options->SetOption('xo_prerender_session', $session);

			$user = $this->Xo->Services->Prerender->GetUser($session);

			if (!empty($user['token'])) {
				$this->Xo->Services->Options->SetOption('xo_prerender_token', $user['token']);
			}

			$this->PrerenderLoginNotice->RegisterNotice();
		}
	}

	function RenderPrerenderLoginNotice($settings) {
		$output = '<p><strong>' . sprintf(
			__('%s logged into Prerender.io.', 'xo'),
			$this->Xo->name
		) . '</strong></p>';

		return $output;
	}

	function RemovePrerender() {
		delete_option('xo_prerender_session');
	}
}