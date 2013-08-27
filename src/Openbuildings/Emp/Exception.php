<?php

namespace Openbuildings\Emp;

/**
 * @package    Openbuildings\Emp
 * @author     Ivan Kerin
 * @copyright  (c) 2013 OpenBuildings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class Exception extends \Exception {

	public function __construct($message, $variables = array(), $code = 0, \Exception $previous = NULL)
	{
		if ($variables)
		{
			$message = strtr($message, $variables);
		}

		parent::__construct($message, $code, $previous);

		$this->code = $code;
	}
}