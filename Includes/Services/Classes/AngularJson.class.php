<?php

/**
 * Service class used to retrieve various json related configurations.
 *
 * @since 1.0.0
 */
class XoServiceAngularJson
{
	function ParseConfig() {
		$file = get_template_directory() . '/angular.json';

		if (!file_exists($file))
			return false;

		$json = json_decode(file_get_contents($file));

		$builder = '@angular-devkit/build-angular:browser';

		$configs = array();

		foreach ($json->projects as $project_name => $project) {
			foreach ($project->architect as $architect_name => $architect) {
				if ($architect->builder != $builder)
					continue;

				$configs[$project_name . ':' . $architect_name] = array(
					'project' => $project_name,
					'architect' => $architect_name,
					'prefix' => $project->prefix,
					'sourceRoot' => $project->sourceRoot,
					'outputPath' => $architect->options->outputPath,
					'index' => $architect->options->index
				);
			}
		}

		return $configs;
	}

	function GetNodeModuleVersion() {
		$file = get_template_directory() . '/package.json';

		if (!file_exists($file))
			return false;

		$json = json_decode(file_get_contents($file));

		$package = 'angular-xo';

		if ((isset($json->dependencies)) && (isset($json->dependencies->$package)))
			return $json->dependencies->$package;

		if ((isset($json->devDependencies)) && (isset($json->devDependencies->$package)))
			return $json->devDependencies->$package;

		return false;
	}
}