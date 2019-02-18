<?php

/**
 * Reflection class used to generate an information representation of a given controller.
 * 
 * @since 1.0.0
 */
class XoApiClassReflector
{
	function ParseMethodParams($comments) {
		$params = array();

		if (preg_match_all('/@param\s+(?P<type>\w+)\s+\$(?P<name>\w+)\s+(?<description>[\w\s.]+)/', $comments, $matches, PREG_SET_ORDER)) {
			foreach ($matches as $match) {
				$params[] = array(
					'name' => $match['name'],
					'type' => $match['type'],
					'description' => trim(str_replace('\n', '', $match['description']))
				);
			}
		}

		return $params;
	}

	function ParseMethodReturn($comments) {
		if (preg_match_all('/@return\s+(?P<type>\w+)\s+(?<description>[\w\s.]+)/', $comments, $matches, PREG_SET_ORDER)) {
			return array(
				'type' => $matches[0]['type'],
				'description' => $matches[0]['description']
			);
		}

		return false;
	}
}