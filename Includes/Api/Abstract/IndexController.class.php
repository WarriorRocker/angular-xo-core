<?php

/**
 * An abstract API controller that extends controller providing a self reflecting index method.
 *
 * @since 1.0.0
 */
class XoApiAbstractIndexController extends XoApiAbstractController
{
	public function Index() {
		$Reflector = new XoApiClassReflector();

		$className = get_class($this);

		$reflectClass = new ReflectionClass($className);

		$controller = array(
			'name' => $className,
			'slug' => lcfirst(substr($className, 15))
		);

		if ($classComments = $reflectClass->getDocComment())
			$controller['description'] = $classComments;

		$methods = array();
		$reflectClassMethods = $reflectClass->getMethods();

		foreach ($reflectClassMethods as $reflectClassMethod) {
			if (($reflectClassMethod->class != $className) || ($reflectClassMethod->name == '__construct'))
				continue;

			$reflectMethod = new ReflectionMethod($reflectClassMethod->class, $reflectClassMethod->name);

			$method = array(
				'name' => $reflectMethod->name,
				'slug' => lcfirst($reflectMethod->name)
			);

			$methodComments = $reflectMethod->getDocComment();

			if ($params = $Reflector->ParseMethodParams($methodComments))
				$method['params'] = $params;

			if ($return = $Reflector->ParseMethodReturn($methodComments))
				$method['return'] = $return;

			$methods[] = $method;
		}

		if ($methods)
			$controller['methods'] = $methods;

		return new XoApiAbstractControllerIndexResponse(
			true, __('Successfully retrieved controller index.'),
			$controller
		);
	}
}