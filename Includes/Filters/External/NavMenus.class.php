<?php

/**
 * Filter class for adding additional options to the WordPress menu editor.
 * 
 * @since 1.0.0
 */
class XoFiltersNavMenus
{
	/**
	 * @var Xo
	 */
	var $Xo;

	var $options = array(
		array(
			'menu' => 'exact',
			'key' => 'xo_menu_item_router_exact',
			'name' => 'xo-menu-item-router-exact'
		)
	);

	function __construct(Xo $Xo) {
		$this->Xo = $Xo;

		add_filter('wp_setup_nav_menu_item', array($this, 'GetNavMenu'), 10, 1);
		add_action('wp_update_nav_menu_item', array($this, 'UpdateNavMenu'), 10, 3);
		add_filter('manage_nav-menus_columns', array($this, 'AddNavMenuColumn'), 20, 1);
		add_filter('wp_edit_nav_menu_walker', array($this, 'SetNavMenuWalker'), 11, 0);
	}

	function GetNavMenu($menu_item) {
		foreach ($this->options as $option)
			$menu_item->{$option['menu']} = get_post_meta($menu_item->ID, $option['key'], true);

		return $menu_item;
	}

	function UpdateNavMenu($menu_id, $menu_item_db_id, $args) {
		foreach ($this->options as $option)
			if ((isset($_REQUEST[$option['name']])) && (is_array($_REQUEST[$option['name']])))
				update_post_meta($menu_item_db_id, $option['key'], $_REQUEST[$option['name']][$menu_item_db_id]);
	}

	function AddNavMenuColumn($columns) {
		$columns['angular-xo'] = $this->Xo->name;
		return $columns;
	}

	function SetNavMenuWalker() {
		$this->Xo->RequireOnce('Includes/Filters/Classes/NavMenuWalker.class.php');

		return 'XoFiltersClassNavMenuWalker';
	}
}