<?php

/**
 * Service class used to manage the display of a given notice shown in the WordPress admin.
 *
 * @since 1.0.0
 */
class XoServiceAdminNotice
{
	/**
	 * The transient key used to track the display of the notice.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $key;

	/**
	 * Callback for generating the body of the notice.
	 *
	 * @since 1.0.0
	 *
	 * @var callable
	 */
	public $renderFn;

	/**
	 * Construct handler for a given admin notice.
	 * 
	 * @since 1.0.0
	 *
	 * @param string $key The transient key used to track the display of the notice.
	 * @param callable $renderFn Callback for generating the body of the notice.
	 */
	public function __construct($key, callable $renderFn) {
		$this->key = $key;
		$this->renderFn = $renderFn;

		add_action('admin_notices', array($this, 'ShowNotice'), 11, 0);
	}

	/**
	 * Register a notice to be shown in the WordPress admin.
	 * 
	 * @since 1.0.0
	 * 
	 * @param array $settings Additional data passed to the notice such as the expiration.
	 */
	public function RegisterNotice($settings = array()) {
		if (!isset($settings['dismissable']))
			$settings['dismissable'] = true;

		if (!isset($settings['expiration']))
			$settings['expiration'] = 0;

		set_transient($this->key, $settings, $settings['expiration']);
	}

	/**
	 * Check if the admin notice should be shown.
	 * 
	 * @since 1.0.0
	 * 
	 * @return void
	 */
	public function ShowNotice() {
		if (!$settings = get_transient($this->key))
			return;

		$output = '<div class="updated notice ' . ((!empty($settings['dismissable'])) ? 'is-dismissible' : '') . '">'
			. call_user_func($this->renderFn, $settings)
			. '</div>';

		echo $output;

		if (!empty($settings['dismissable']))
			$this->DismissNotice();
	}

	/**
	 * Dismiss the admin notice.
	 * 
	 * @since 1.0.0
	 * 
	 * @return void
	 */
	public function DismissNotice() {
		delete_transient($this->key);
	}
}