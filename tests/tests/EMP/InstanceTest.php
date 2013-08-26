<?php

use OpenBuildings\EMP\EMP as EMP;

/**
 * Unit tests for the EMP class.
 * @author Haralan Dobrev <hdobrev@despark.com>
 * @copyright (c) 2013 OpenBuildings Inc.
 * @license http://spdx.org/licenses/BSD-3-Clause
 */
class EMP_InstanceTest extends PHPUnit_Framework_TestCase {

	/**
	 * @covers OpenBuildings\EMP\EMP::instance
	 */
	public function test_instance()
	{
		$this->markTestIncomplete();
	}

	/**
	 * @covers OpenBuildings\EMP\EMP::__construct
	 */
	public function test_constructor()
	{
		$emp = new EMP('abc', 'qwerty');
		$this->assertSame('abc', $emp->client_id());
		$this->assertSame('qwerty', $emp->api_key());
	}
}
