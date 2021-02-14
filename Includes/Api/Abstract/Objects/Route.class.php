<?php

/**
 * An abstract class used to construct a fully formed Angular Route object.
 *
 * @since 1.0.0
 */
class XoApiAbstractRoute
{
	/**
	 * The real path used for routing of the Angular Route item.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $path;

	/**
	 * The lazyPath used to match the existing fake path of lazy Routes.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	public $lazyPath;

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
	 * @param string $path The real path used for routing of the Angular Route item.
	 * @param string $lazyPath The lazyPath used to match the existing fake path of lazy Routes.
	 * @param string $pathMatch The pathMatch property of the Angular Route item.
	 * @param mixed $data Additional data added to the data property of the Angular Route item.
	 */
	public function __construct($path, $lazyPath, $pathMatch = 'prefix', $data = false) {
		// Map base route properties
		$this->path = $path;
		$this->lazyPath = $lazyPath;
		$this->pathMatch = $pathMatch;
		$this->data = $data;
	}
}