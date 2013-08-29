<?php

use Openbuildings\Emp\Threatmatrix;
use Openbuildings\Emp\Api;
use Openbuildings\Emp\Remote;

/**
 * @package 
 * @group   api
 */
class ApiTest extends PHPUnit_Framework_TestCase {

	/**
	 * @covers Openbuildings\Emp\Api::configure
	 * @covers Openbuildings\Emp\Api::instance
	 */
	public function test_initialize()
	{
		Api::configure('http://example.com', 'client-id', 'api-key');

		$instance = Api::instance();

		$this->assertEquals('http://example.com', $instance->gateway_url());
		$this->assertEquals('client-id', $instance->client_id());
		$this->assertEquals('api-key', $instance->api_key());
	}

	/**
	 * @covers Openbuildings\Emp\Api::__construct
	 * @covers Openbuildings\Emp\Api::gateway_url
	 * @covers Openbuildings\Emp\Api::client_id
	 * @covers Openbuildings\Emp\Api::api_key
	 */
	public function test_construct()
	{
		$instance = new Api('http://test.example.com', 'client-id-2', 'api-key-2');
		$this->assertEquals('http://test.example.com', $instance->gateway_url());
		$this->assertEquals('client-id-2', $instance->client_id());
		$this->assertEquals('api-key-2', $instance->api_key());
	}

	/**
	 * @covers Openbuildings\Emp\Api::auth_params
	 * @covers Openbuildings\Emp\Api::threatmatrix
	 */
	public function test_auth_params()
	{
		$instance = new Api('http://test.example.com', 'client-id-3', 'api-key-3');
		$expected = array('client_id' => 'client-id-3', 'api_key' => 'api-key-3');
		$this->assertEquals($expected, $instance->auth_params());

		$threatmatrix = new Threatmatrix('ORG_ID', 'CLIENT_ID');
		$instance->threatmatrix($threatmatrix);

		$expected = array('client_id' => 'client-id-3', 'api_key' => 'api-key-3', 'thm_session_id' => $threatmatrix->session_id());
		$this->assertEquals($expected, $instance->auth_params());
	}

	/**
	 * @covers Openbuildings\Emp\Api::generate_url
	 */
	public function test_generate_url()
	{
		$instance = new Api('https://my.emerchantpay.com', '11111111', 'TEST_API_KEY');

		$url = $instance->generate_url('/service/order/submit', array(
			'payment_type' => 'creditcard',
			'thm_session_id' => 'TEST_SESSION_ID',
		));

		$this->assertEquals('https://my.emerchantpay.com/service/order/submit?client_id=11111111&api_key=TEST_API_KEY&payment_type=creditcard&thm_session_id=TEST_SESSION_ID', $url);
	}

	/**
	 * @covers Openbuildings\Emp\Api::request
	 * @expectedException Openbuildings\Emp\Exception
	 * @expectedExceptionMessage Error sendig request to gateway: (OP903) Authentication failure
	 */
	public function test_request()
	{
		$instance = new Api('https://my.emerchantpay.com', '11111111', 'TEST_API_KEY');

		$instance->request('/service/order/submit', array(
			'payment_type' => 'creditcard',
			'test_transaction' => 1,
		));
	}

	/**
	 * @covers Openbuildings\Emp\Api::request
	 * @expectedException Openbuildings\Emp\Exception
	 * @expectedExceptionMessage The transaction was declined: DeclineTEST
	 */
	public function test_request_declined()
	{
		$instance = new Api('https://my.emerchantpay.com', getenv('PHP_EMP_CLIENT_ID'), getenv('PHP_EMP_API_KEY'));

		$thm = new Threatmatrix(getenv('PHP_THREATMATRIX_ORG_ID'), getenv('PHP_EMP_CLIENT_ID'));
		Remote::get($thm->tracking_url());

		$instance
			->threatmatrix($thm)
			->request('/service/order/submit', array(
			'card_holder_name'       => 'TEST HOLDER',
			'card_number'            => '4111111111111111',
			'exp_month'              => '10',
			'exp_year'               => '19',
			'cvv'                    => '123',
			'order_reference'        => '521c7556ccdd8',
			'order_currency'         => 'GBP',
			'payment_method'         => 'creditcard',

			'customer_email'         => 'test.user.purchase@example.com',

			'test_transaction'       => '1',

			'item_1_code'            => '1',
			'item_1_qty'             => '1',
			'item_1_predefined'      => '0',
			'item_1_name'            => 'basket',
			'item_1_unit_price_GBP'  => '1.01',

			'ip_address'             => '95.87.212.88',
			'credit_card_trans_type' => 'sale',
		));
	}

	/**
	 * @covers Openbuildings\Emp\Api::request
	 * @expectedException Openbuildings\Emp\Exception
	 * @expectedExceptionMessage Error sendig request to gateway: (OP998) ErrorTEST
	 */
	public function test_request_error()
	{
		$instance = new Api('https://my.emerchantpay.com', getenv('PHP_EMP_CLIENT_ID'), getenv('PHP_EMP_API_KEY'));

		$thm = new Threatmatrix(getenv('PHP_THREATMATRIX_ORG_ID'), getenv('PHP_EMP_CLIENT_ID'));
		Remote::get($thm->tracking_url());

		$instance
			->threatmatrix($thm)
			->request('/service/order/submit', array(
			'card_holder_name'       => 'TEST HOLDER',
			'card_number'            => '4111111111111111',
			'exp_month'              => '10',
			'exp_year'               => '19',
			'cvv'                    => '123',
			'order_reference'        => '521c7556ccdd8',
			'order_currency'         => 'GBP',
			'payment_method'         => 'creditcard',

			'customer_email'         => 'test.user.purchase@example.com',

			'test_transaction'       => '1',

			'item_1_code'            => '1',
			'item_1_qty'             => '1',
			'item_1_predefined'      => '0',
			'item_1_name'            => 'basket',
			'item_1_unit_price_GBP'  => '1.97',

			'ip_address'             => '95.87.212.88',
			'credit_card_trans_type' => 'sale',
		));
	}
	/**
	 * @covers Openbuildings\Emp\Api::request
	 */
	public function test_correct_request()
	{
		$instance = new Api('https://my.emerchantpay.com', getenv('PHP_EMP_CLIENT_ID'), getenv('PHP_EMP_API_KEY'));

		$thm = new Threatmatrix(getenv('PHP_THREATMATRIX_ORG_ID'), getenv('PHP_EMP_CLIENT_ID'));
		Remote::get($thm->tracking_url());

		$response = $instance
			->threatmatrix($thm)
			->request('/service/order/submit', array(
				'card_holder_name'       => 'TEST HOLDER',
				'card_number'            => '4111111111111111',
				'exp_month'              => '10',
				'exp_year'               => '19',
				'cvv'                    => '123',
				'order_reference'        => '521c7556ccdd8',
				'order_currency'         => 'GBP',
				'payment_method'         => 'creditcard',

				'customer_email'         => 'test.user.purchase@example.com',

				'test_transaction'       => '1',

				'item_1_code'            => '1',
				'item_1_qty'             => '1',
				'item_1_predefined'      => '0',
				'item_1_name'            => 'basket',
				'item_1_unit_price_GBP'  => '20.00',

				'ip_address'             => '95.87.212.88',
				'credit_card_trans_type' => 'sale',
		));

		$this->assertEquals('Paid', $response['order_status']);
		$this->assertGreaterThan(0, $response['transaction_id']);
		$this->assertEquals('A', $response['transaction_response']);
		$this->assertEquals(20.00, $response['raw']['order_total']);
	}
}