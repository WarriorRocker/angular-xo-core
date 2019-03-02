<?php

/**
 * WordPress requires a file to include which displays the current template.
 * All template requests are routed to a singular index file which bootstraps the Angular application
 * 
 * @since 1.0.0
 */
$output = $Xo->Services->IndexBuilder->RenderDistIndex();

if (empty($output))
	die(__('Unable to render index.', 'xo'));

echo $output;