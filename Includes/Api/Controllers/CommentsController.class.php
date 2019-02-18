<?php

/**
 * Provide endpoints for comments. This endpoint is experimental.
 *
 * @since 1.0.0
 */
class XoApiControllerComments extends XoApiAbstractIndexController
{
	/**
	 * Experimental filter for comments.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $params Request object
	 * @return XoApiAbstractResponse
	 */
	function Filter($params) {
		// Return an error if postId is missing
		if (empty($params['postId']))
			return new XoApiAbstractResponse(false, __('Missing post id.', 'xo'));

		// Get the comments for the given postId
		$comments = get_comments(array(
			'post_id' => $params['postId']
		));

		print_r($comments);
	}
}