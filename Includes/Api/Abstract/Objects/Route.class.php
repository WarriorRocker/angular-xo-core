<?php

/**
 * An abstract class used to construct a fully formed Angular Route object.
 *
 * @since 1.0.0
 */
class XoApiAbstractRoute
{
	/**
	 * The path property of the Angular Route item.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $path;

	/**
	 * The loadChildren property of the Angular Route item.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $loadChildren;

	/**
	 * The pathMatch property of the Angular Route item.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $pathMatch;

	/**
	 * Additional data added to the data property of the Angular Route item.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	public $data;

	/**
	 * Generate a fully formed Angular Route object.
	 *
	 * @since 1.0.0
	 *
	 * @param string $path The path property of the Angular Route item.
	 * @param string $loadChildren The loadChildren property of the Angular Route item.
	 * @param string $pathMatch The pathMatch property of the Angular Route item.
	 * @param mixed $data Additional data added to the data property of the Angular Route item.
	 */
	public function __construct($path, $loadChildren, $pathMatch = 'prefix', $data = false) {
		// Map base route properties
		$this->path = $path;
		$this->loadChildren = $loadChildren;
		$this->pathMatch = $pathMatch;
		$this->data = $data;
	}
}