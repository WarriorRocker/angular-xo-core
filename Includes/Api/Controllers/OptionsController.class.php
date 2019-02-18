<?php

/**
 * Provide endpoints for retrieving option groups.
 *
 * @since 1.0.0
 */
class XoApiControllerOptions extends XoApiAbstractController
{
	/**
	 * Get an option group by name.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $params Request object
	 * @return XoApiAbstractOptionsGetResponse
	 */
	function Get($params) {
		// Return an error if ACF is not present or activated
		if ((!function_exists('acf_get_fields')) ||
			(!function_exists('get_field')))
			return new XoApiAbstractOptionsGetResponse(false, __('ACF not present.', 'xo'));

		// Return an error if missing the name of the option group
		if (empty($params['name']))
		    return new XoApiAbstractOptionsGetResponse(false, __('Missing group name.', 'xo'));

		// Return an error if the group is not allowed over the Xo API
		if (!$allowedGroups = $this->Xo->Services->Options->GetOption('xo_acf_allowed_groups', array()))
			return new XoApiAbstractOptionsGetResponse(false, __('No allowed groups are defined.', 'xo'));

		// Check if group name is not in the allowed list
		if ((!array_key_exists($params['name'], $allowedGroups))
			|| ($allowedGroups[$params['name']] != 1))
			return new XoApiAbstractOptionsGetResponse(false, __('Not an allowed option group.', 'xo'));

		// Return an error if the option group does not exist or contains no data
		if (!$group = acf_get_fields($params['name']))
			return new XoApiAbstractOptionsGetResponse(false, __('Unable to retrieve options group.', 'xo'));

		// Define collection of individual fields within the option group
		$fields = array();

		// Iterate through the individual fields within the option group
		foreach ($group as $field)
			$fields[$field['key']] = get_field($field['key'], 'option');

		// Return success and fields for the requested option group
		return new XoApiAbstractOptionsGetResponse(
			true, __('Successfully retrieved options group.', 'xo'),
			$fields
		);
	}
}