<?php

/**
 * Render the output from a prerender request.
 * 
 * @since 2.0.0
 */
$output = $Xo->Services->IndexBuilder->RenderPrerenderResponse();

if (empty($output))
	die(__('Unable to render index.', 'xo'));

echo $output;