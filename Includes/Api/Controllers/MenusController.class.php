<?php

/**
 * Provide endpoints for retrieving navigation menus.
 *
 * @since 1.0.0
 */
class XoApiControllerMenus extends XoApiAbstractIndexController
{
	/**
	 * Get a navigation menu by location name.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $params Request object
	 * @return XoApiAbstractMenusGetResponse
	 */
	public function Get($params) {
		// Return an error if menu location name is missing
		if (empty($params['menu']))
			return new XoApiAbstractMenusGetResponse(false, __('Missing menu name.', 'xo'));

		// Return an error if there are no locations defined
		if (!$locations = get_nav_menu_locations())
			return new XoApiAbstractMenusGetResponse(false, __('No theme menu locations defined.', 'xo'));

		// Return an error if the menu location was not found
		if (!$menu = get_term($locations[$params['menu']], 'nav_menu'))
			return new XoApiAbstractMenusGetResponse(false,
				sprintf(__('Requested menu location %s not found.', 'xo'), $params['menu']));

		// Iterate through menu items to retrieve fully formed menu item objects
		$items = array();
		$menuItems = wp_get_nav_menu_items($menu->term_id);
		foreach ($menuItems as $menuItem)
			$items[] = new XoApiAbstractMenu($menuItem, true, true, true);

		// Iterate through fully formed menu items and organize by parent/child
		$sortedItems = $this->GetNestedMenuItems($items);

		// Return success and the fully formed menu item objects
		return new XoApiAbstractMenusGetResponse(
			true, __('Successfully retrieved menu.', 'xo'),
			$sortedItems
		);
	}

	protected function GetNestedMenuItems(array $items, $parentId = 0) {
		$nestedItems = array_values(array_filter($items, function ($item) use ($parentId) {
			return $item->parent == $parentId;
		}));

		foreach ($nestedItems as $item) {
			$item->children = $this->GetNestedMenuItems($items, $item->id);
		}
	
		return $nestedItems;
	}
}