<?php

use Openbuildings\Emp\Exception;

/**
 * @package
 * @group   exception
 */
class ExceptionTest extends PHPUnit_Framework_TestCase {

	/**
	 * @covers Openbuildings\Emp\Exception::__construct
	 */
	public function test_construct()
	{
		$exception = new Exception('Text :var :var2', array(':var' => 'new var', ':var2' => 'new var2'), 10);

		$this->assertEquals('Text new var new var2', $exception->getMessage());
		$this->assertEquals(10, $exception->getCode());
	}
}
