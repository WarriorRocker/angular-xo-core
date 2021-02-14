<?php

/**
 * An abstract API controller to be extended by API endpoint controllers.
 * 
 * @since 1.0.0
 */
class XoApiAbstractController
{
	/**
	 * @var Xo
	 */
	protected $Xo;

	public function __construct(Xo $Xo) {
		$this->Xo = $Xo;
	}
}