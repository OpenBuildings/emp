<?php

use OpenBuildings\EMP\EMP as EMP;

/**
 * Unit tests for the EMP class.
 * @author Haralan Dobrev <hdobrev@despark.com>
 * @copyright (c) 2013 OpenBuildings Inc.
 * @license http://spdx.org/licenses/BSD-3-Clause
 */
class EMPTest extends PHPUnit_Framework_TestCase {

	public function setUp()
	{
		$this->emp = new EMP('A', 'B');
	}

	/**
	 * @covers OpenBuildings\EMP\EMP::default_params
	 */
	public function test_default_params()
	{
		$this->assertSame(array(
			'credit_card_trans_type' => 'sale',
			'payment_method' => 'creditcard',
		), $this->emp->default_params());
	}

	/**
	 * @covers OpenBuildings\EMP\EMP::client_id
	 */
	public function test_client_id()
	{
		$this->assertSame('A', $this->emp->client_id());

		$this->emp->client_id('Z');
		$this->assertSame('Z', $this->emp->client_id());

		$this->emp->client_id('A');
		$this->assertSame('A', $this->emp->client_id());
	}

	/**
	 * @covers OpenBuildings\EMP\EMP::api_key
	 */
	public function test_api_key()
	{
		$this->assertSame('B', $this->emp->api_key());

		$this->emp->api_key('Y');
		$this->assertSame('Y', $this->emp->api_key());

		$this->emp->api_key('B');
		$this->assertSame('B', $this->emp->api_key());
	}

	/**
	 * @covers OpenBuildings\EMP\EMP::order
	 */
	public function test_order()
	{
		$this->assertSame(array(), $this->emp->order());

		$this->emp->order(array(
			'amount' => '10.00'
		));
		$this->assertSame(array(
			'amount' => '10.00'
		), $this->emp->order());

		$this->emp->order(array());
		$this->assertSame(array(), $this->emp->order());
	}

	/**
	 * @covers OpenBuildings\EMP\EMP::execute
	 */
	public function test_execute()
	{
		$emp = $this->getMock('OpenBuildings\EMP\EMP', array(
			'_parse',
			'_request'
		), array(
			'A',
			'B'
		));

		$emp
			->expects($this->once())
			->method('_request')
			->with($this->equalTo(
				'https://my.emerchantpay.com/service/order/submit?ABCDE=QWERTY&credit_card_trans_type=sale&payment_method=creditcard&client_id=A&api_key=B'
			))
			->will($this->returnValue('raw response'));

		$emp
			->expects($this->once())
			->method('_parse')
			->with($this->equalTo('raw response'))
			->will($this->returnValue('parsed response'));

		$response = $emp
			->order(array('ABCDE' => 'QWERTY'))
			->execute();

		$this->assertEquals('parsed response', $response);
	}

	/**
	 * @covers OpenBuildings\EMP\EMP::execute
	 */
	public function test_execute_missing_client_id_exception()
	{
		$this->emp->client_id(FALSE);
		$this->setExpectedException('OpenBuildings\EMP\Exception', 'Client ID MUST NOT be empty!');
		$this->emp->execute();
	}

	/**
	 * @covers OpenBuildings\EMP\EMP::execute
	 */
	public function test_execute_missing_api_key_exception()
	{
		$this->emp->api_key(FALSE);
		$this->setExpectedException('OpenBuildings\EMP\Exception', 'API key MUST NOT be empty!');
		$this->emp->execute();
	}

	/**
	 * @covers OpenBuildings\EMP\EMP::execute
	 */
	public function test_execute_missing_order_exception()
	{
		$this->emp->order(array());
		$this->setExpectedException('OpenBuildings\EMP\Exception', 'Order MUST NOT be empty!');
		$this->emp->execute();
	}

	/**
	 * @covers OpenBuildings\EMP\EMP::_request
	 */
	public function test_request()
	{
		$this->markTestIncomplete();
	}

	/**
	 * @covers OpenBuildings\EMP\EMP::_parse
	 */
	public function test_parse()
	{
		$this->markTestIncomplete();
	}
}