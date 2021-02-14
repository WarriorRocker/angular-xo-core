<?php

// Check if ACF is also extending Walker_Nav_Menu_Edit
if (!class_exists('ACF_Walker_Nav_Menu_Edit')) {
	class ACF_Walker_Nav_Menu_Edit extends Walker_Nav_Menu_Edit { }
}

/**
 * Extend the WordPress Walker_Nav_Menu_Edit class to inject additional options.
 *
 * @since 1.0.0
 */
class XoFiltersClassNavMenuWalker extends ACF_Walker_Nav_Menu_Edit  {
	function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
		$menuHtml = '';
		parent::start_el($menuHtml, $item, $depth, $args, $id);

		if (($entry = strpos($menuHtml, '<fieldset class="field-move')) !== false)
			$output = $output . substr($menuHtml, 0, $entry) . $this->GenerateOptions($item) . substr($menuHtml, $entry);
	}

	private function GenerateOptions(WP_Post $post) {
		$item_id = esc_attr($post->ID);

		ob_start();

		?>
		<p class="field-xo-api field-link-router-exact description description-wide">
			<label for="xo-edit-menu-item-router-exact-<?php echo $item_id; ?>">
				<input type="checkbox" id="xo-edit-menu-item-router-exact-<?php echo $item_id; ?>" value="1" name="xo-menu-item-router-exact[<?php echo $item_id; ?>]" <?php checked($post->exact, 1); ?> />
				<?php _e('Use routerLinkActiveOptions exact', 'xo'); ?>
			</label>
		</p>
		<?php

		return ob_get_clean();
	}
}